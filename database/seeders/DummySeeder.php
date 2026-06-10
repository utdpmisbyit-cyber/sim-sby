<?php

namespace Database\Seeders;


use App\Models\Donor;
use App\Models\Kecamatan;
use App\Models\Kewarganegaraan;
use App\Models\Pekerjaan;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $donorNames = [
            'Budi Utomo', 'Siti Aminah', 'Joko Widodo', 'Sri Wahyuni', 'Ahmad Dahlan',
            'Kartini Lestari', 'Hendra Wijaya', 'Dewi Sartika', 'Bambang Pamungkas', 'Megawati Sukarnoputri',
            'Susilo Bambang', 'Ani Yudhoyono', 'Prabowo Subianto', 'Ganjar Pranowo', 'Anies Baswedan',
            'Puan Maharani', 'Ridwan Kamil', 'Khofifah Indar', 'Gibran Rakabuming', 'Erick Thohir',
        ];

        $kewarganegaraanId = Kewarganegaraan::first()?->id ?? 1;
        $pekerjaanId = Pekerjaan::first()?->id ?? 1;

        foreach ($donorNames as $index => $nama) {
            $kode = 'D-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            $noPendaftaran = 'REG-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            // Get random Kecamatan and matching Wilayah
            $kecamatan = Kecamatan::inRandomOrder()->first();
            $kecId = $kecamatan ? $kecamatan->id : 1;
            $wilId = $kecamatan ? $kecamatan->wilayah_id : 1;

            $tanggalLahir = Carbon::now()->subYears(rand(18, 60))->subDays(rand(1, 365));
            $usia = Carbon::parse($tanggalLahir)->age;

            Donor::updateOrCreate(
                ['kode' => $kode],
                [
                    'no_pendaftaran' => $noPendaftaran,
                    'nama' => $nama,
                    'alamat_1' => 'Jl. Mawar No. '.($index + 1),
                    'alamat_2' => 'RT 0'.rand(1, 9).' RW 0'.rand(1, 9),
                    'kode_pos' => '60'.rand(100, 999),
                    'tanggal_lahir' => $tanggalLahir,
                    'usia' => $usia,
                    'jenis_kelamin' => Donor::JENIS_KELAMIN[array_rand(Donor::JENIS_KELAMIN)],
                    'kewarganegaraan_id' => $kewarganegaraanId,
                    'wilayah_id' => $wilId,
                    'kecamatan_id' => $kecId,
                    'pekerjaan_id' => $pekerjaanId,
                    'agama' => Donor::AGAMA[array_rand(Donor::AGAMA)],
                    'no_telp' => '081'.rand(10000000, 99999999),
                    'no_ktp' => '3578'.rand(10, 99).rand(100000, 999999).'000'.($index + 1),
                    'no_sim' => rand(1000, 9999).'-'.rand(1000, 9999).'-0000'.($index + 1),
                    'golongan_darah' => Donor::GOLONGAN_DARAH[array_rand(Donor::GOLONGAN_DARAH)],
                    'rhesus' => Donor::RHESUS[array_rand(Donor::RHESUS)],
                    'golongan_darah_lain' => null,
                    'golongan_rhesus' => 'Positif',
                    'cekal' => null,
                    'donor_ke' => rand(1, 20),
                    'skrining' => 'Negatif',
                    'counter_cekal' => 0,
                    'is_golongan_darah_locked' => false,
                ]
            );

        }

    }
}
