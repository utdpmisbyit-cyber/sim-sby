<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\PengirimanAftap;
use App\Models\Petugas;
use App\Models\RencanaProduksi;
use App\Models\RencanaProduksiDetail;
use App\Models\TipeKantong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DetailRencanaProduksiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $petugas;
    private $bagianPetugas;
    private $cabang;
    private $jabatan;
    private $tipeKantong;
    private $rencanaProduksi;
    private $rencanaProduksiDetail;

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
            'nama' => 'PRODUKSI',
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

        $pengiriman = PengirimanAftap::create([
            'no_pengiriman' => 'SHIP001',
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $this->rencanaProduksi = RencanaProduksi::create([
            'pengiriman_aftap_id' => $pengiriman->id,
            'tanggal' => now(),
            'petugas_id' => $this->petugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
        ]);

        $this->rencanaProduksiDetail = RencanaProduksiDetail::create([
            'rencana_produksi_id' => $this->rencanaProduksi->id,
            'no_kantong' => '1234567890',
            'no_satelit' => '1',
            'jenis_darah' => 'WB',
        ]);

        // Put active_cabang in session
        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_index_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)
            ->get(route('kantong_darah.detail_rencana_produksi.index'));

        $response->assertStatus(200);
        $response->assertSee('Detail Rencana Produksi');
    }

    public function test_scan_finds_correct_details()
    {
        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.detail_rencana_produksi.scan'), [
                'barcode' => '1234567890',
            ]);

        $response->assertStatus(200);
        $response->assertSee('1234567890');
        $response->assertSee('Satelit 1');
        $response->assertSee('WB');
    }

    public function test_scan_fails_if_barcode_not_found()
    {
        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.detail_rencana_produksi.scan'), [
                'barcode' => 'NON_EXISTENT_BARCODE',
            ]);

        $response->assertStatus(404);
        $response->assertJsonStructure(['error']);
    }

    public function test_calculate_volume_endpoint()
    {
        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.detail_rencana_produksi.calculate'), [
                'gram' => 74.00,
                'jenis_darah' => 'WB',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'volume' => 50,
        ]);
    }

    public function test_save_weight_and_volume_successfully()
    {
        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.detail_rencana_produksi.save', $this->rencanaProduksiDetail->id), [
                'gram' => 74.00,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'gram' => 74.00,
            'volume' => 50,
        ]);

        $this->assertDatabaseHas('rencana_produksi_detail', [
            'id' => $this->rencanaProduksiDetail->id,
            'gram' => 74.00,
            'volume' => 50,
        ]);
    }

    public function test_saved_list_returns_table_view()
    {
        // Fill detail so it appears in the list
        $this->rencanaProduksiDetail->update([
            'gram' => 74.00,
            'volume' => 50,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.detail_rencana_produksi.list'), [
                'keyword' => '1234567890',
            ]);

        $response->assertStatus(200);
        $response->assertSee('1234567890');
        $response->assertSee('74.0');
        $response->assertSee('50.0');
    }
}
