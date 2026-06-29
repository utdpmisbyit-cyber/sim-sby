<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\PendataanKantong;
use App\Models\PenyimpananKantong;
use App\Models\PermintaanKantong;
use App\Models\PermintaanKantongDetail;
use App\Models\Petugas;
use App\Models\TipeKantong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GudangKonfirmasiPermintaanTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $petugas;
    private $bagianPetugas;
    private $cabang;
    private $jabatan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create initial database records
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
    }

    public function test_konfirmasi_fails_when_barcode_is_empty(): void
    {
        $permintaan = PermintaanKantong::create([
            'nomor' => 'PK0001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $detail = PermintaanKantongDetail::create([
            'permintaan_kantong_id' => $permintaan->id,
            'jumlah' => 1,
            'jenis' => 'Single',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('gudang.konfirmasi_permintaan.update', $permintaan->id), [
                "barcode_{$detail->id}_1" => "",
            ]);

        $response->assertStatus(422);
        $this->assertEquals("Semua nomor kantong harus diisi!", $response->json());
    }

    public function test_konfirmasi_fails_when_barcode_is_duplicate(): void
    {
        $permintaan = PermintaanKantong::create([
            'nomor' => 'PK0001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $detail = PermintaanKantongDetail::create([
            'permintaan_kantong_id' => $permintaan->id,
            'jumlah' => 2,
            'jenis' => 'Single',
        ]);

        // Create the barcode in DB so the first item check passes
        PendataanKantong::create([
            'kode' => 'KOD-DUP',
            'barcode' => 'BARCODE1',
            'merk_kantong' => 'Amicore',
            'jenis_kantong' => 'Single',
            'type_kantong' => 'SG',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('gudang.konfirmasi_permintaan.update', $permintaan->id), [
                "barcode_{$detail->id}_1" => "BARCODE1",
                "barcode_{$detail->id}_2" => "BARCODE1",
            ]);

        $response->assertStatus(422);
        $this->assertEquals("Nomor kantong 'BARCODE1' diinput duplikat!", $response->json());
    }

    public function test_konfirmasi_fails_when_barcode_does_not_exist(): void
    {
        $permintaan = PermintaanKantong::create([
            'nomor' => 'PK0001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $detail = PermintaanKantongDetail::create([
            'permintaan_kantong_id' => $permintaan->id,
            'jumlah' => 1,
            'jenis' => 'Single',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('gudang.konfirmasi_permintaan.update', $permintaan->id), [
                "barcode_{$detail->id}_1" => "NONEXISTENT",
            ]);

        $response->assertStatus(422);
        $this->assertEquals("Nomor kantong 'NONEXISTENT' tidak ditemukan di pendataan kantong!", $response->json());
    }

    public function test_konfirmasi_success_updates_db_and_saves_to_penyimpanan(): void
    {
        $jenisKantong = JenisKantong::create([
            'nama' => 'Single',
            'list_satuan' => '350 cc',
        ]);

        $tipeKantong = TipeKantong::create([
            'jenis_kantong_id' => $jenisKantong->id,
            'nama' => 'SG',
        ]);

        $permintaan = PermintaanKantong::create([
            'nomor' => 'PK0001',
            'tanggal' => now(),
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'petugas_id' => $this->petugas->id,
            'flag' => 0,
        ]);

        $detail = PermintaanKantongDetail::create([
            'permintaan_kantong_id' => $permintaan->id,
            'tipe_kantong_id' => $tipeKantong->id,
            'jumlah' => 2,
            'jenis' => 'Single',
        ]);

        $pendataan1 = PendataanKantong::create([
            'kode' => 'KOD-01',
            'barcode' => 'BAR-01',
            'merk_kantong' => 'Amicore',
            'jenis_kantong' => 'Single',
            'type_kantong' => 'SG',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'status' => 'PENDING',
            'tipe_kantong_id' => $tipeKantong->id,
        ]);

        $pendataan2 = PendataanKantong::create([
            'kode' => 'KOD-02',
            'barcode' => 'BAR-02',
            'merk_kantong' => 'Amicore',
            'jenis_kantong' => 'Single',
            'type_kantong' => 'SG',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-02',
            'status' => 'PENDING',
            'tipe_kantong_id' => $tipeKantong->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('gudang.konfirmasi_permintaan.update', $permintaan->id), [
                "barcode_{$detail->id}_1" => "BAR-01",
                "barcode_{$detail->id}_2" => "BAR-02",
            ]);

        $response->assertStatus(200);

        // Verify PermintaanKantong was updated
        $this->assertDatabaseHas('permintaan_kantong', [
            'id' => $permintaan->id,
            'flag' => 1,
            'verifikator_id' => $this->petugas->id,
        ]);

        // Verify status was updated on PendataanKantong
        $this->assertDatabaseHas('pendataan_kantong', [
            'id' => $pendataan1->id,
            'status' => 'AFTAP',
        ]);
        $this->assertDatabaseHas('pendataan_kantong', [
            'id' => $pendataan2->id,
            'status' => 'AFTAP',
        ]);

        // Verify PenyimpananKantong has records
        $this->assertDatabaseHas('penyimpanan_kantong', [
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $tipeKantong->id,
            'no_kantong' => 'BAR-01',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-01',
            'flag' => 1,
            'jumlah' => 1,
        ]);

        $this->assertDatabaseHas('penyimpanan_kantong', [
            'bagian_petugas_id' => $this->bagianPetugas->id,
            'tipe_kantong_id' => $tipeKantong->id,
            'no_kantong' => 'BAR-02',
            'ukuran' => '350 CC',
            'no_lot' => 'LOT-02',
            'flag' => 1,
            'jumlah' => 1,
        ]);
    }
}
