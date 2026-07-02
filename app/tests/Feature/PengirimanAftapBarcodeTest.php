<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\PenyimpananKantong;
use App\Models\PengirimanAftap;
use App\Models\PengirimanAftapDetail;
use App\Models\Petugas;
use App\Models\TipeKantong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengirimanAftapBarcodeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $petugas;
    private $bagianPetugas;
    private $cabang;
    private $jabatan;
    private $tipeKantong;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabang = Cabang::create([
            'kode' => 'CAB-01',
            'nama' => 'Cabang Test',
            'jenis' => 'UDD',
            'status' => true,
        ]);

        $this->jabatan = Jabatan::create([
            'kode' => 'JAB-01',
            'nama' => 'Jabatan Test',
        ]);

        $this->bagianPetugas = BagianPetugas::create([
            'kode' => 'BAG-01',
            'nama' => 'AFTAP',
        ]);

        $this->user = User::factory()->create([
            'role' => 'Petugas',
        ]);

        $this->petugas = Petugas::create([
            'kode' => 'PET-01',
            'nama' => 'Petugas Test',
            'user_id' => $this->user->id,
            'cabang_id' => $this->cabang->id,
            'jabatan_id' => $this->jabatan->id,
            'bagian_id' => $this->bagianPetugas->id,
        ]);

        $jenisKantong = JenisKantong::create([
            'nama' => 'Single',
            'list_satuan' => '350 cc',
        ]);

        $this->tipeKantong = TipeKantong::create([
            'jenis_kantong_id' => $jenisKantong->id,
            'nama' => 'SG',
        ]);

        // Put active_cabang in session
        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_store_fails_when_barcode_not_found(): void
    {
        $pengiriman = PengirimanAftap::create([
            'no_pengiriman' => 'SHIP001',
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengiriman_aftap.detail.store', $pengiriman->id), [
                'no_kantong' => 'NON-EXISTENT',
                'flag' => 1
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
        $this->assertEquals('No. Kantong tidak ditemukan di penyimpanan.', $response->json('errors.no_kantong.0'));
    }

    public function test_store_fails_when_barcode_is_duplicate(): void
    {
        $pengiriman = PengirimanAftap::create([
            'no_pengiriman' => 'SHIP001',
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-123',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 2,
            'jumlah' => 1,
        ]);

        // Insert first detail record
        PengirimanAftapDetail::create([
            'pengiriman_aftap_id' => $pengiriman->id,
            'no_kantong' => 'BAR-123',
            'flag' => 1
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengiriman_aftap.detail.store', $pengiriman->id), [
                'no_kantong' => 'BAR-123',
                'flag' => 1
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
        $this->assertEquals('No. Kantong sudah ditambahkan.', $response->json('errors.no_kantong.0'));
    }

    public function test_store_and_delete_success_flow(): void
    {
        $pengiriman = PengirimanAftap::create([
            'no_pengiriman' => 'SHIP001',
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $penyimpanan = PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-999',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 2,
            'jumlah' => 1,
        ]);

        // 1. Store the detail
        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengiriman_aftap.detail.store', $pengiriman->id), [
                'no_kantong' => 'BAR-999',
                'flag' => 1
            ]);

        $response->assertStatus(201);

        // Verify detail record was created in database
        $this->assertDatabaseHas('pengiriman_aftap_detail', [
            'pengiriman_aftap_id' => $pengiriman->id,
            'no_kantong' => 'BAR-999',
            'flag' => 1
        ]);

        // Verify PenyimpananKantong flag changed to 3
        $this->assertEquals(3, $penyimpanan->fresh()->flag);

        // 2. Delete the detail
        $detail = PengirimanAftapDetail::where('no_kantong', 'BAR-999')->first();
        $response = $this->actingAs($this->user)
            ->deleteJson(route('aftap.pengiriman_aftap.detail.destroy', [$pengiriman->id, $detail->id]));

        $response->assertStatus(200);

        // Verify detail record was removed
        $this->assertDatabaseMissing('pengiriman_aftap_detail', [
            'id' => $detail->id,
        ]);

        // Verify PenyimpananKantong flag reverted to 2
        $this->assertEquals(2, $penyimpanan->fresh()->flag);
    }

    public function test_store_fails_when_barcode_status_is_not_2(): void
    {
        $pengiriman = PengirimanAftap::create([
            'no_pengiriman' => 'SHIP001',
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $penyimpanan = PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-STATUS-NOT-2',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 1, // Status is not 2
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengiriman_aftap.detail.store', $pengiriman->id), [
                'no_kantong' => 'BAR-STATUS-NOT-2',
                'flag' => 1
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
        $this->assertEquals('No. Kantong harus memiliki status penyimpanan.', $response->json('errors.no_kantong.0'));
    }
}
