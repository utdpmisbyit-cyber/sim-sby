<?php

namespace Tests\Feature;

use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\Petugas;
use App\Models\User;
use App\Models\Donor;
use App\Models\Aftap;
use App\Models\LogDonor;
use App\Models\Litbang;
use App\Models\Kewarganegaraan;
use App\Models\Wilayah;
use App\Models\Kecamatan;
use App\Models\Pekerjaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LitbangTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $petugas;
    private $bagianPetugas;
    private $cabang;
    private $jabatan;
    private $donor;
    private $aftap;
    private $logDonor;

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

        $kewarganegaraan = Kewarganegaraan::create([
            'kode' => 'WNI',
            'nama' => 'Warga Negara Indonesia',
        ]);

        $wilayah = Wilayah::create([
            'kode' => 'WIL-01',
            'nama' => 'Wilayah Test',
        ]);

        $kecamatan = Kecamatan::create([
            'kode' => 'KEC-01',
            'nama' => 'Kecamatan Test',
            'wilayah_id' => $wilayah->id,
        ]);

        $pekerjaan = Pekerjaan::create([
            'kode' => 'PEK-01',
            'nama' => 'Swasta',
        ]);

        $this->donor = Donor::create([
            'kode' => 'D001',
            'no_pendaftaran' => 'P001',
            'nama' => 'Donor Test',
            'kewarganegaraan_id' => $kewarganegaraan->id,
            'wilayah_id' => $wilayah->id,
            'kecamatan_id' => $kecamatan->id,
            'pekerjaan_id' => $pekerjaan->id,
            'golongan_darah' => 'O',
            'rhesus' => '+',
        ]);

        $this->logDonor = LogDonor::create([
            'kode' => 'LOG-01',
            'cabang_id' => $this->cabang->id,
            'donor_id' => $this->donor->id,
            'petugas_registrasi_id' => $this->petugas->id,
            'step' => 'Aftap',
        ]);

        $this->aftap = Aftap::create([
            'kode' => 'A001',
            'no_kantong' => '123456789',
            'log_donor_id' => $this->logDonor->id,
            'donor_id' => $this->donor->id,
            'status' => 'Approved',
        ]);

        session(['active_cabang' => ['id' => $this->cabang->id, 'nama' => $this->cabang->nama]]);
    }

    public function test_kirim_litbang_success(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('serologi.kirim_litbang.store'), [
                'no_kantong' => '123456789',
                'tanggal_kirim' => '15-06-2026',
                'petugas_kirim_id' => $this->petugas->id,
                'keterangan' => 'Uji Coba Litbang',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('litbang', [
            'no_kantong' => '123456789',
            'aftap_id' => $this->aftap->id,
            'donor_id' => $this->donor->id,
            'tanggal_kirim' => '2026-06-15 00:00:00',
            'petugas_kirim_id' => $this->petugas->id,
            'status' => 'pending',
            'keterangan' => 'Uji Coba Litbang',
        ]);
    }

    public function test_kirim_litbang_fails_when_no_kantong_does_not_exist_in_aftap(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('serologi.kirim_litbang.store'), [
                'no_kantong' => '999999999',
                'tanggal_kirim' => '15-06-2026',
                'petugas_kirim_id' => $this->petugas->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
    }

    public function test_kirim_litbang_fails_when_duplicate_no_kantong(): void
    {
        // First one sent
        Litbang::create([
            'no_kantong' => '123456789',
            'aftap_id' => $this->aftap->id,
            'donor_id' => $this->donor->id,
            'tanggal_kirim' => '2026-06-15',
            'status' => 'pending',
            'petugas_kirim_id' => $this->petugas->id,
        ]);

        // Second request for the same no_kantong
        $response = $this->actingAs($this->user)
            ->postJson(route('serologi.kirim_litbang.store'), [
                'no_kantong' => '123456789',
                'tanggal_kirim' => '16-06-2026',
                'petugas_kirim_id' => $this->petugas->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_kantong']);
    }

    public function test_konfirmasi_litbang_success(): void
    {
        $litbang = Litbang::create([
            'no_kantong' => '123456789',
            'aftap_id' => $this->aftap->id,
            'donor_id' => $this->donor->id,
            'tanggal_kirim' => '2026-06-15',
            'status' => 'pending',
            'petugas_kirim_id' => $this->petugas->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('serologi.konfirmasi_litbang.update', $litbang->id), [
                'golongan_darah' => 'A',
                'rhesus' => '+',
                'tanggal_konfirmasi' => '16-06-2026',
                'petugas_konfirmasi_id' => $this->petugas->id,
                'keterangan' => 'Hasil cocok',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('litbang', [
            'id' => $litbang->id,
            'golongan_darah' => 'A',
            'rhesus' => '+',
            'tanggal_konfirmasi' => '2026-06-16 00:00:00',
            'petugas_konfirmasi_id' => $this->petugas->id,
            'status' => 'selesai',
            'keterangan' => 'Hasil cocok',
        ]);
    }
}
