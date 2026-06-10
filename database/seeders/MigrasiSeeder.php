<?php

namespace Database\Seeders;

use App\Models\AturanSatelit;
use App\Models\BagianPetugas;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\JenisKantong;
use App\Models\JenisPeriksaSerologi;
use App\Models\Kecamatan;
use App\Models\Kewarganegaraan;
use App\Models\MetodeSerologi;
use App\Models\Pekerjaan;
use App\Models\Petugas;
use App\Models\ReagenSerologi;
use App\Models\TipeKantong;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MigrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wilayah::insert([
            ['kode' => '0001', 'nama' => 'PUSAT'],
            ['kode' => '0002', 'nama' => 'UTARA'],
            ['kode' => '0003', 'nama' => 'SELATAN'],
            ['kode' => '0004', 'nama' => 'TIMUR'],
            ['kode' => '0005', 'nama' => 'BARAT'],
            ['kode' => '0006', 'nama' => 'LUAR SURABAYA'],
            ['kode' => '0007', 'nama' => 'SIDOARJO'],
            ['kode' => '0099', 'nama' => 'Lain-lain'],
        ]);

        Kecamatan::insert([
            ['kode' => '1001', 'nama' => 'SIMOKERTO', 'wilayah_id' => 1],
            ['kode' => '1002', 'nama' => 'BUBUTAN', 'wilayah_id' => 1],
            ['kode' => '1003', 'nama' => 'GENTENG', 'wilayah_id' => 1],
            ['kode' => '1004', 'nama' => 'TEGALSARI', 'wilayah_id' => 1],
            ['kode' => '2001', 'nama' => 'SEMAMPIR', 'wilayah_id' => 2],
            ['kode' => '2002', 'nama' => 'PABEAN CANTIKAN', 'wilayah_id' => 2],
            ['kode' => '2003', 'nama' => 'KREMBANGAN', 'wilayah_id' => 2],
            ['kode' => '2004', 'nama' => 'BULAK', 'wilayah_id' => 2],
            ['kode' => '2005', 'nama' => 'ASEM ROWO', 'wilayah_id' => 2],
            ['kode' => '2006', 'nama' => 'SUKOMANUNGGAL', 'wilayah_id' => 2],
            ['kode' => '2007', 'nama' => 'KENJERAN', 'wilayah_id' => 2],
            ['kode' => '3001', 'nama' => 'SAWAHAN', 'wilayah_id' => 3],
            ['kode' => '3002', 'nama' => 'WONOKROMO', 'wilayah_id' => 3],
            ['kode' => '3003', 'nama' => 'WONOCOLO', 'wilayah_id' => 3],
            ['kode' => '3004', 'nama' => 'JAMBANGAN', 'wilayah_id' => 3],
            ['kode' => '3005', 'nama' => 'KARANG PILANG', 'wilayah_id' => 3],
            ['kode' => '3006', 'nama' => 'GAYUNGAN', 'wilayah_id' => 3],
            ['kode' => '4001', 'nama' => 'TAMBAKSARI', 'wilayah_id' => 4],
            ['kode' => '4002', 'nama' => 'MULYOREJO', 'wilayah_id' => 4],
            ['kode' => '4003', 'nama' => 'GUBENG', 'wilayah_id' => 4],
            ['kode' => '4004', 'nama' => 'SUKOLILO', 'wilayah_id' => 4],
            ['kode' => '4005', 'nama' => 'RUNGKUT', 'wilayah_id' => 4],
            ['kode' => '4006', 'nama' => 'TENGGILIS MEJOYO', 'wilayah_id' => 4],
            ['kode' => '4007', 'nama' => 'GUNUNG ANYAR', 'wilayah_id' => 4],
            ['kode' => '5001', 'nama' => 'BENOWO', 'wilayah_id' => 5],
            ['kode' => '5002', 'nama' => 'PAKAL', 'wilayah_id' => 5],
            ['kode' => '5003', 'nama' => 'TANDES', 'wilayah_id' => 5],
            ['kode' => '5004', 'nama' => 'SAMBIKEREP', 'wilayah_id' => 5],
            ['kode' => '5005', 'nama' => 'DUKUH PAKIS', 'wilayah_id' => 5],
            ['kode' => '5006', 'nama' => 'WIYUNG', 'wilayah_id' => 5],
            ['kode' => '5007', 'nama' => 'LAKARSANTRI', 'wilayah_id' => 5],
            ['kode' => '6001', 'nama' => 'LUAR SURABAYA', 'wilayah_id' => 6],
            ['kode' => '7001', 'nama' => 'SIDOARJO', 'wilayah_id' => 7],
            ['kode' => '7002', 'nama' => 'SIDOARJO', 'wilayah_id' => 7],
            ['kode' => '99001', 'nama' => 'LAIN LAIN', 'wilayah_id' => 8],
        ]);

        Kewarganegaraan::insert([
            ['kode' => '0000', 'nama' => 'INDONESIA'],
            ['kode' => '0001', 'nama' => 'AUSTRALIA'],
            ['kode' => '0002', 'nama' => 'MALAYSIA'],
            ['kode' => '0003', 'nama' => 'AMERIKA'],
            ['kode' => '0004', 'nama' => 'KOREA'],
            ['kode' => '0005', 'nama' => 'SINGAPURA'],
            ['kode' => '0006', 'nama' => 'INDIA'],
            ['kode' => '0007', 'nama' => 'JEPANG'],
            ['kode' => '0999', 'nama' => 'Lain-lain'],
        ]);

        Pekerjaan::insert([
            ['kode' => '0001', 'nama' => 'Pegawai Swasta'],
            ['kode' => '0002', 'nama' => 'Wiraswasta'],
            ['kode' => '0003', 'nama' => 'Mahasiswa/Pelajar'],
            ['kode' => '0004', 'nama' => 'TNI / POLRI'],
            ['kode' => '0005', 'nama' => 'Pegawai Negeri / PNS'],
            ['kode' => '0006', 'nama' => 'Lain - Lain'],
            ['kode' => '0007', 'nama' => 'Ibu rumah tangga'],
        ]);

        Jabatan::insert([
            ['kode' => '0001', 'nama' => 'KEPALA'],
            ['kode' => '0002', 'nama' => 'KEPALA BAGIAN'],
            ['kode' => '0003', 'nama' => 'KEPALA SEKSI'],
            ['kode' => '0004', 'nama' => 'STAF'],
            ['kode' => '0005', 'nama' => 'MANAJER KUALITAS'],
            ['kode' => '0006', 'nama' => 'KOORDINATOR'],
            ['kode' => '0999', 'nama' => 'Administrator'],
        ]);

        BagianPetugas::insert([
            ['kode' => '0001', 'nama' => 'UMUM DAN LOGITISK'],
            ['kode' => '0002', 'nama' => 'ADMIN DONOR (PDK)'],
            ['kode' => '0003', 'nama' => 'AFTAP (PDK)'],
            ['kode' => '0004', 'nama' => 'PANTRY (PDK)'],
            ['kode' => '0005', 'nama' => 'POLY'],
            ['kode' => '0006', 'nama' => 'RUJUKAN DAN LITBANG'],
            ['kode' => '0007', 'nama' => 'PENGUJIAN DARAH'],
            ['kode' => '0008', 'nama' => 'UJI SILANG DARAH'],
            ['kode' => '0009', 'nama' => 'KEPEGAWAIN DAN DIKLAT'],
            ['kode' => '0010', 'nama' => 'HUMAS REKRUITMEN JEJARING'],
            ['kode' => '0011', 'nama' => 'DISTRIBUSI (PPD)'],
            ['kode' => '0012', 'nama' => 'PENGOLAHAN DARAH KOMPONEN'],
            ['kode' => '0013', 'nama' => 'KEUANGAN'],
            ['kode' => '0014', 'nama' => 'LOGISTIK'],
            ['kode' => '0015', 'nama' => 'SECURITY'],
            ['kode' => '0016', 'nama' => 'DRIVER'],
            ['kode' => '0017', 'nama' => 'BARCODE'],
            ['kode' => '0018', 'nama' => 'DIKLAT'],
            ['kode' => '0019', 'nama' => 'DOKUMEN DATA IT'],
            ['kode' => '0020', 'nama' => 'PELULUSAN PRODUK DISTRIBUSI'],
        ]);

        $cabang = Cabang::create([
            'kode' => '0001',
            'nama' => 'UDD - PMI Kota Surabaya',
            'jenis' => 'UDD',
            'status' => true
        ]);

        $users = file_get_contents(public_path('users.json'));
        $users = json_decode($users, true);
        foreach ($users as $row) {
            $user = User::factory()->create([
                'name'  => $row['nama'],
                'email' => $row['kode'],
                'role'  => 'Petugas',
            ]);

            Petugas::create([
                'kode' => $row['kode'],
                'nama' => $row['nama'],
                'user_id' => $user->id,
                'cabang_id' => $cabang->id,
                'jabatan_id' => 1,
                'bagian_id' => 1,
            ]);
        }

        $data = [
            ['kdtype' => 'S', 'typektg' => 'SG', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DB', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DB', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DB', 'jenisdarah' => 'FFP', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'FFP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => '2', 'typektg' => 'AP2', 'jenisdarah' => 'TC', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'FFP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'BC', 'satelit' => 4],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'BC', 'satelit' => 4],
            ['kdtype' => 'A', 'typektg' => 'AP', 'jenisdarah' => 'TC', 'satelit' => 1],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 2],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 3],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 4],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 5],
            ['kdtype' => 'P', 'typektg' => 'PD', 'jenisdarah' => 'PRC', 'satelit' => 6],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => '3', 'typektg' => 'AP', 'jenisdarah' => 'TC', 'satelit' => 1],
            ['kdtype' => '3', 'typektg' => 'AP', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => '3', 'typektg' => 'AP', 'jenisdarah' => 'TC', 'satelit' => 3],
            ['kdtype' => 'B', 'typektg' => 'AP', 'jenisdarah' => 'TC', 'satelit' => 1],
            ['kdtype' => 'B', 'typektg' => 'AP', 'jenisdarah' => 'LP', 'satelit' => 2],
            ['kdtype' => '@', 'typektg' => 'AP', 'jenisdarah' => 'LP', 'satelit' => 1],
            ['kdtype' => '^', 'typektg' => 'AP', 'jenisdarah' => 'LP', 'satelit' => 1],
            ['kdtype' => '^', 'typektg' => 'AP', 'jenisdarah' => 'FP', 'satelit' => 1],
            ['kdtype' => '^', 'typektg' => 'AP', 'jenisdarah' => 'FFP', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'PCLs', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QW', 'jenisdarah' => 'PCLs', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'PCLs', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QT', 'jenisdarah' => 'TP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QW', 'jenisdarah' => 'TP', 'satelit' => 3],
            ['kdtype' => 'D', 'typektg' => 'DB', 'jenisdarah' => 'TP', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'TP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'TP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'FP', 'satelit' => 3],
            ['kdtype' => 'E', 'typektg' => 'APp', 'jenisdarah' => 'FFP', 'satelit' => 1],
            ['kdtype' => 'E', 'typektg' => 'APp', 'jenisdarah' => 'FP', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DB', 'jenisdarah' => 'FP', 'satelit' => 2],
            ['kdtype' => 'D', 'typektg' => 'DJ', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DJ', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DJ', 'jenisdarah' => 'FFP', 'satelit' => 2],
            ['kdtype' => 'D', 'typektg' => 'DJ', 'jenisdarah' => 'FP', 'satelit' => 2],
            ['kdtype' => 'D', 'typektg' => 'DK', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DK', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'D', 'typektg' => 'DK', 'jenisdarah' => 'FFP', 'satelit' => 2],
            ['kdtype' => 'D', 'typektg' => 'DK', 'jenisdarah' => 'FP', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'AHF', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TR', 'jenisdarah' => 'LP', 'satelit' => 3],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'PRC', 'satelit' => 1],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'FFP', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'AHF', 'satelit' => 2],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'LP', 'satelit' => 3],
            ['kdtype' => 'T', 'typektg' => 'TJ', 'jenisdarah' => 'TP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'PCR', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'TC', 'satelit' => 2],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'FFP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QD', 'jenisdarah' => 'BC', 'satelit' => 4],
            ['kdtype' => 'Q', 'typektg' => 'QW', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QW', 'jenisdarah' => 'FFP', 'satelit' => 3],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'WB', 'satelit' => 1],
            ['kdtype' => 'Q', 'typektg' => 'QR', 'jenisdarah' => 'PCR', 'satelit' => 1],
        ];
        AturanSatelit::insert($data);

        $list_tipe_kantong = [
            'Apheresis' => ['AP', 'AP2', 'AP3', 'APc', 'APk', 'APp'],
            'Double Besar' => ['DB', 'DJ'],
            'Double Kecil' => ['DK'],
            'Pediatrix' => ['PD'],
            'Quadruple' => ['QD', 'QR', 'QW'],
            'Quintruple' => ['QT'],
            'Single' => ['SG'],
            'Triple' => ['TJ', 'TR']
        ];

        $list_satuan = [
            'Apheresis' => '350 cc,450 cc',
            'Double Besar' => '350 cc,450 cc',
            'Double Kecil' => '350 cc,250 cc',
            'Pediatrix' => '350 cc',
            'Quadruple' => '450 cc',
            'Quintruple' => '450 cc',
            'Single' => '350 cc,250 cc',
            'Triple' => '350 cc,450 cc',
        ];

        foreach ($list_tipe_kantong as $jenis_nama => $tipe_list) {
            $jenis = JenisKantong::create([
                'nama' => $jenis_nama,
                'list_satuan' => $list_satuan[$jenis_nama] ?? null,
            ]);
            foreach ($tipe_list as $tipe_nama) {
                TipeKantong::create(['jenis_kantong_id' => $jenis->id, 'nama' => $tipe_nama]);
            }
        }

        $metodeSerologi = [
            ['kode' => '001', 'nama' => 'CMIA'],
            ['kode' => '002', 'nama' => 'MICRO ELISA'],
            ['kode' => '003', 'nama' => 'NAT'],
            ['kode' => '004', 'nama' => 'CLEIA'],
            ['kode' => '005', 'nama' => 'CLIA'],
        ];

        $reagenSerologi = [
            ['kode' => '013', 'nama' => 'BIOMERIEUX'],
            ['kode' => '014', 'nama' => 'ALINITY'],
            ['kode' => '015', 'nama' => 'DISCRIMINATORY'],
            ['kode' => '016', 'nama' => 'ULTRIO PLUS'],
            ['kode' => '017', 'nama' => 'DIASORIN'],
            ['kode' => '018', 'nama' => 'HISCL SYSMEX'],
            ['kode' => '019', 'nama' => 'MINDRAY'],
        ];

        $jenisPeriksaSerologi = [
            ['kode' => '001', 'nama' => 'HBsAg'],
            ['kode' => '002', 'nama' => 'HCV'],
            ['kode' => '003', 'nama' => 'HIV'],
            ['kode' => '004', 'nama' => 'Syphilis'],
            ['kode' => '005', 'nama' => 'LAIN1'],
            ['kode' => '006', 'nama' => 'LAIN2'],
            ['kode' => '007', 'nama' => 'LAIN3'],
            ['kode' => '008', 'nama' => 'LAIN4'],
        ];

        foreach ($metodeSerologi as $item) {
            MetodeSerologi::updateOrInsert(
                ['kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }

        foreach ($reagenSerologi as $item) {
            ReagenSerologi::updateOrInsert(
                ['kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }

        foreach ($jenisPeriksaSerologi as $item) {
            JenisPeriksaSerologi::updateOrInsert(
                ['kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }
    }

}