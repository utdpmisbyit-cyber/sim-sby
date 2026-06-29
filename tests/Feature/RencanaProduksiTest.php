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
use App\Models\RencanaProduksi;
use App\Models\RencanaProduksiDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RencanaProduksiTest extends TestCase
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

        // Insert dummy satelit rule for SG tipe_kantong
        DB::table('aturan_satelit')->insert([
            'kdtype' => 'S',
            'typektg' => 'SG',
            'jenisdarah' => 'WB',
            'satelit' => 1,
        ]);

        // Put active_cabang in session
        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_store_rencana_produksi_success(): void
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
            'no_kantong' => 'BAR-999',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 2,
            'jumlah' => 1,
        ]);

        PengirimanAftapDetail::create([
            'pengiriman_aftap_id' => $pengiriman->id,
            'no_kantong' => 'BAR-999',
            'flag' => 1
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('kantong_darah.rencana_produksi.store'), [
                'pengiriman_aftap_id' => $pengiriman->id,
                'tanggal' => '15-06-2026',
                'petugas_id' => $this->petugas->id,
                'petugas_kode' => $this->petugas->kode,
            ]);

        $response->assertStatus(201); // resource created successfully

        // Check if plans record exists
        $this->assertDatabaseHas('rencana_produksi', [
            'pengiriman_aftap_id' => $pengiriman->id,
            'petugas_id' => $this->petugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
        ]);

        $plan = RencanaProduksi::where('pengiriman_aftap_id', $pengiriman->id)->first();

        // Check if plan details were created based on satelit configuration
        $this->assertDatabaseHas('rencana_produksi_detail', [
            'rencana_produksi_id' => $plan->id,
            'no_kantong' => 'BAR-999',
            'no_satelit' => 1,
        ]);
    }
}
