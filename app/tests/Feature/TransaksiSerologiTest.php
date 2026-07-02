<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\Petugas;
use App\Models\User;
use App\Models\JenisPeriksaSerologi;
use App\Models\MetodeSerologi;
use App\Models\ReagenSerologi;
use App\Models\Serologi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiSerologiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $petugas;
    private $bagianPetugas;
    private $cabang;
    private $jabatan;
    private $jenisPeriksa;
    private $metode;
    private $reagen;

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

        $this->jenisPeriksa = JenisPeriksaSerologi::create([
            'kode' => 'JP01',
            'nama' => 'HBsAg',
        ]);

        $this->metode = MetodeSerologi::create([
            'kode' => 'M01',
            'nama' => 'EIA',
        ]);

        $this->reagen = ReagenSerologi::create([
            'kode' => 'R01',
            'nama' => 'Bio-Rad',
        ]);

        // Put active_cabang in session
        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_store_grouped_serologi_transaction_success(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('serologi.transaksi_serologi.store'), [
                'tanggal' => '15-06-2026',
                'petugas_id' => $this->petugas->id,
                'pemeriksa_serologi_id' => $this->petugas->id,
                'nomor_list' => ['SRL001'],
                'jenis_periksa_serologi_id_list' => [$this->jenisPeriksa->id],
                'metode_serologi_id_list' => [$this->metode->id],
                'reagen_serologi_id_list' => [$this->reagen->id],
                'no_lot_reagen_list' => ['LOT12345'],
                'tanggal_expired_reagen_list' => ['31-12-2026'],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('serologi', [
            'nomor' => 'SRL001',
            'tanggal' => '2026-06-15 00:00:00',
            'jenis_periksa_serologi_id' => $this->jenisPeriksa->id,
            'metode_serologi_id' => $this->metode->id,
            'reagen_serologi_id' => $this->reagen->id,
            'no_lot_reagen' => 'LOT12345',
            'tanggal_expired_reagen' => '2026-12-31 00:00:00',
        ]);
    }

    public function test_duplicate_serologi_transaction_success(): void
    {
        $source = Serologi::create([
            'nomor' => 'SRL001',
            'tanggal' => '2026-06-15',
            'jenis_periksa_serologi_id' => $this->jenisPeriksa->id,
            'metode_serologi_id' => $this->metode->id,
            'reagen_serologi_id' => $this->reagen->id,
            'no_lot_reagen' => 'LOT12345',
            'tanggal_expired_reagen' => '2026-12-31',
            'group' => 'GRP123',
            'petugas_id' => $this->petugas->id,
            'pemeriksa_serologi_id' => $this->petugas->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('serologi.transaksi_serologi.duplicate', $source->id), [
                'nomor' => 'SRL002',
                'tanggal' => '16-06-2026',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('serologi', [
            'nomor' => 'SRL002',
            'tanggal' => '2026-06-16 00:00:00',
            'jenis_periksa_serologi_id' => $this->jenisPeriksa->id,
            'metode_serologi_id' => $this->metode->id,
            'reagen_serologi_id' => $this->reagen->id,
            'no_lot_reagen' => 'LOT12345',
            'tanggal_expired_reagen' => '2026-12-31 00:00:00',
        ]);
    }
}
