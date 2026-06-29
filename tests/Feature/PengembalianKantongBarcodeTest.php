<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\PenyimpananKantong;
use App\Models\PengembalianKantong;
use App\Models\PengembalianKantongDetail;
use App\Models\Petugas;
use App\Models\TipeKantong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengembalianKantongBarcodeTest extends TestCase
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
        $pengembalian = PengembalianKantong::create([
            'nomor' => 'RET001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengembalian_kantong.detail.store', $pengembalian->id), [
                'no_kantong' => 'NON-EXISTENT',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
        $this->assertEquals('No. Kantong tidak ditemukan di penyimpanan.', $response->json('errors.no_kantong.0'));
    }

    public function test_store_fails_when_barcode_is_duplicate(): void
    {
        $pengembalian = PengembalianKantong::create([
            'nomor' => 'RET001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
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
        PengembalianKantongDetail::create([
            'pengembalian_kantong_id' => $pengembalian->id,
            'no_kantong' => 'BAR-123',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('aftap.pengembalian_kantong.detail.store', $pengembalian->id), [
                'no_kantong' => 'BAR-123',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
        $this->assertEquals('No. Kantong sudah ditambahkan.', $response->json('errors.no_kantong.0'));
    }

    public function test_store_and_delete_success_flow(): void
    {
        $pengembalian = PengembalianKantong::create([
            'nomor' => 'RET001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
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
            ->postJson(route('aftap.pengembalian_kantong.detail.store', $pengembalian->id), [
                'no_kantong' => 'BAR-999',
            ]);

        $response->assertStatus(201);

        // Verify detail record was created in database
        $this->assertDatabaseHas('pengembalian_kantong_detail', [
            'pengembalian_kantong_id' => $pengembalian->id,
            'no_kantong' => 'BAR-999',
        ]);

        // Verify PenyimpananKantong flag changed to -1
        $this->assertEquals(-1, $penyimpanan->fresh()->flag);

        // 2. Delete the detail
        $detail = PengembalianKantongDetail::where('no_kantong', 'BAR-999')->first();
        $response = $this->actingAs($this->user)
            ->deleteJson(route('aftap.pengembalian_kantong.detail.destroy', [$pengembalian->id, $detail->id]));

        $response->assertStatus(200);

        // Verify detail record was removed
        $this->assertDatabaseMissing('pengembalian_kantong_detail', [
            'id' => $detail->id,
        ]);

        // Verify PenyimpananKantong flag reverted to 2
        $this->assertEquals(2, $penyimpanan->fresh()->flag);
    }
}
