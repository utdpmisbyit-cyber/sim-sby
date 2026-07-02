<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\PenyimpananKantong;
use App\Models\Petugas;
use App\Models\TipeKantong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersediaanKantongDetailTest extends TestCase
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

        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_index_displays_only_active_stock(): void
    {
        // Pouch 1: Active
        PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-001',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 1, // Aktif
            'jumlah' => 1,
        ]);

        // Pouch 2: Dikembalikan (Inactive)
        PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-002',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => -1, // Dikembalikan
            'jumlah' => 1,
        ]);

        // Pouch 3: Digunakan (Inactive)
        PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-003',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 2, // Digunakan
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('aftap.persediaan_kantong.index'));

        $response->assertStatus(200);
        // The view list should show stock = 1 (only the active one is counted)
        $list_tipe_kantong = $response->viewData('list_tipe_kantong');
        $this->assertEquals(1, $list_tipe_kantong->first()->stock);
    }

    public function test_detail_displays_active_and_inactive_lists_with_search(): void
    {
        // Pouch 1: Active
        $pouch1 = PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-ACTIVE',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 1, // Aktif
            'jumlah' => 1,
        ]);

        // Pouch 2: Dikembalikan (Inactive)
        $pouch2 = PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-RETURNED',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => -1, // Dikembalikan
            'jumlah' => 1,
        ]);

        // Pouch 3: Digunakan (Inactive)
        $pouch3 = PenyimpananKantong::create([
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $this->tipeKantong->id,
            'no_kantong' => 'BAR-USED',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 2, // Digunakan
            'jumlah' => 1,
        ]);

        // 1. Check full list details
        $response = $this->actingAs($this->user)
            ->get(route('aftap.persediaan_kantong.show', $this->tipeKantong->id));

        $response->assertStatus(200);
        
        $active_list = $response->viewData('active_list');
        $inactive_list = $response->viewData('inactive_list');

        $this->assertCount(1, $active_list);
        $this->assertEquals('BAR-ACTIVE', $active_list->first()->no_kantong);

        $this->assertCount(2, $inactive_list);
        $this->assertContains('BAR-RETURNED', $inactive_list->pluck('no_kantong')->toArray());
        $this->assertContains('BAR-USED', $inactive_list->pluck('no_kantong')->toArray());

        // 2. Search active pouch
        $responseSearchActive = $this->actingAs($this->user)
            ->get(route('aftap.persediaan_kantong.show', [
                'id' => $this->tipeKantong->id,
                'keyword' => 'ACTIVE'
            ]));
        
        $responseSearchActive->assertStatus(200);
        $this->assertCount(1, $responseSearchActive->viewData('active_list'));
        $this->assertCount(0, $responseSearchActive->viewData('inactive_list'));

        // 3. Search inactive pouch
        $responseSearchInactive = $this->actingAs($this->user)
            ->get(route('aftap.persediaan_kantong.show', [
                'id' => $this->tipeKantong->id,
                'keyword' => 'RETURNED'
            ]));
        
        $responseSearchInactive->assertStatus(200);
        $this->assertCount(0, $responseSearchInactive->viewData('active_list'));
        $this->assertCount(1, $responseSearchInactive->viewData('inactive_list'));
        $this->assertEquals('BAR-RETURNED', $responseSearchInactive->viewData('inactive_list')->first()->no_kantong);
    }
}
