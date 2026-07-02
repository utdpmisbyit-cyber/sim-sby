<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanDokter extends Model
{
    const STATUS = ['Pending', 'Ongoing', 'Approved', 'Rejected', 'Cancelled'];
    const LIST_ALASAN = ['Cekal', 'HB Rendah', 'HB Tinggi', 'Tensi Rendah', 'Tensi Tinggi', 'Lain-lain'];
    const LIST_JENIS_KANTONG = ['Apheresis', 'Double Besar', 'Double Kecil', 'Pediatrix', 'Quadruple', 'Quintruple', 'Single', 'Triple'];
    const LIST_TIPE_JENIS_KANTONG = [
        'Apheresis' => ['AP', 'AP2', 'AP3', 'APc', 'APk', 'APp'],
        'Double Besar' => ['DB', 'DJ'],
        'Double Kecil' => ['DK'],
        'Pediatrix' => ['PD'],
        'Quadruple' => ['QD', 'QR', 'QW'],
        'Quintruple' => ['QT'],
        'Single' => ['SG'],
        'Triple' => ['TJ', 'TR']
    ];
    const LIST_KUISIONER = [
        ['name' => 'merasa_sehat', 'label' => '1. Merasa sehat pada hari ini?', 'section' => null],
        ['name' => 'sedang_minum_antibiotik', 'label' => '2. Sedang minum antibiotik?', 'section' => null],
        ['name' => 'sedang_minum_obat_lain', 'label' => '3. Sedang minum obat lain untuk infeksi?', 'section' => null],
        ['name' => 'sedang_minum_aspirin', 'label' => '4. Apakah anda sedang minum aspirin atau obat yang mengandung aspirin?', 'section' => 'Dalam waktu 48 jam terakhir'],
        ['name' => 'sakit_kepala_dan_demam', 'label' => '5. Apakah anda mengalami sakit kepala dan demam bersamaan?', 'section' => 'Dalam waktu 1 minggu terakhir'],
        ['name' => 'donor_hamil', 'label' => '6. Untuk donor wanita: apakah anda saat ini sedang hamil?', 'section' => 'Dalam waktu 6 minggu terakhir'],
        ['name' => 'donor_transfusi_komponen', 'label' => '7. Apakah anda mendonorkan darah, trombosit atau plasma?', 'section' => 'Dalam waktu 8 minggu terakhir'],
        ['name' => 'menerima_vaksinasi_suntikan', 'label' => '8. Apakah anda menerima vaksinasi atau suntikan lainnya?', 'section' => null],
        ['name' => 'kontak_vaksin_smallpox', 'label' => '9. Apakah anda kontak dengan orang yang menerima vaksinasi smallpox?', 'section' => null],
        ['name' => 'donor_sel_darah_merah_aferesis', 'label' => '10. Apakah anda mendonorkan 2 kantong sel darah merah melalui proses aferesis?', 'section' => 'Dalam waktu 16 minggu terakhir'],
        ['name' => 'terima_transfusi_darah', 'label' => '11. Apakah anda pernah menerima transfusi darah?', 'section' => 'Dalam waktu 12 bulan terakhir'],
        ['name' => 'terima_transplantasi', 'label' => '12. Apakah anda pernah mendapat transplantasi organ, jaringan atau sumsum tulang?', 'section' => null],
        ['name' => 'cangkok_tulang_atau_kulit', 'label' => '13. Apakah anda pernah mencangkok tulang atau kulit?', 'section' => null],
        ['name' => 'tertusuk_jarum_medis', 'label' => '14. Apakah anda pernah tertusuk jarum medis?', 'section' => null],
        ['name' => 'hubungan_seksual_hiv_aids', 'label' => '15. Apakah anda pernah berhubungan seksual dengan orang dengan HIV/AIDS?', 'section' => null],
        ['name' => 'hubungan_seksual_pekerja_seks', 'label' => '16. Apakah anda pernah berhubungan seksual dengan pekerja seks komersial?', 'section' => null],
        ['name' => 'hubungan_seksual_pengguna_narkoba', 'label' => '17. Apakah anda pernah berhubungan seksual dengan pengguna narkoba suntik?', 'section' => null],
        ['name' => 'hubungan_seksual_konsentrat_pembekuan', 'label' => '18. Apakah anda pernah berhubungan seksual dengan pengguna konsentrat faktor pembekuan?', 'section' => null],
        ['name' => 'donor_wanita_biseksual', 'label' => '19. Donor wanita: apakah anda pernah berhubungan seksual dengan laki-laki yang biseksual?', 'section' => null],
        ['name' => 'hubungan_seksual_penderita_hepatitis', 'label' => '20. Apakah anda pernah berhubungan seksual dengan penderita hepatitis?', 'section' => null],
        ['name' => 'tinggal_bersama_penderita_hepatitis', 'label' => '21. Apakah anda tinggal bersama penderita hepatitis?', 'section' => null],
        ['name' => 'memiliki_tato', 'label' => '22. Apakah anda memiliki tato?', 'section' => null],
        ['name' => 'memiliki_tindik', 'label' => '23. Apakah anda memiliki tindik telinga atau bagian tubuh lainnya?', 'section' => null],
        ['name' => 'pengobatan_sifilis_go', 'label' => '24. Apakah anda sedang atau pernah mendapat pengobatan sifilis atau GO (kencing nanah)?', 'section' => null],
        ['name' => 'pernah_dipenjara', 'label' => '25. Apakah anda pernah ditahan dipenjara untuk waktu lebih dari 72 jam?', 'section' => 'Dalam waktu 3 tahun'],
        ['name' => 'berada_di_luar_indonesia', 'label' => '26. Apakah anda pernah berada di luar wilayah Indonesia?', 'section' => null],
        ['name' => 'menerima_uang_seks', 'label' => '27. Apakah anda menerima uang, obat atau pembayaran lainnya untuk seks?', 'section' => 'Tahun 1977 hingga sekarang'],
        ['name' => 'pria_hubungan_seksual_pria', 'label' => '28. Laki-laki: Apakah anda pernah berhubungan seksual dengan laki-laki, walaupun sekali?', 'section' => null],
        ['name' => 'tinggal_eropa_5_tahun', 'label' => '29. Apakah anda tinggal selama 5 tahun atau lebih di Eropa?', 'section' => 'Tahun 1980 hingga sekarang'],
        ['name' => 'transfusi_darah_inggris', 'label' => '30. Apakah anda menerima transfusi darah di Inggris?', 'section' => null],
        ['name' => 'tinggal_inggris_3_bulan', 'label' => '31. Apakah anda tinggal selama 3 bulan atau lebih di Inggris?', 'section' => 'Tahun 1980 hingga 1996'],
        ['name' => 'hasil_positif_hiv_aids', 'label' => '32. Mendapat hasil positif untuk tes HIV/AIDS?', 'section' => 'Apakah anda pernah'],
        ['name' => 'menggunakan_jarum_suntik_obat', 'label' => '33. Menggunakan jarum suntik untuk obat-obatan, Steroid yang tidak diresepkan dokter?', 'section' => null],
        ['name' => 'menggunakan_konsentrat_pembekuan', 'label' => '34. Menggunakan konsentrat faktor pembekuan?', 'section' => null],
        ['name' => 'menderita_hepatitis', 'label' => '35. Menderita Hepatitis?', 'section' => null],
        ['name' => 'menderita_malaria', 'label' => '36. Menderita Malaria?', 'section' => null],
        ['name' => 'menderita_kanker_leukemia', 'label' => '37. Menderita kanker termasuk leukemia?', 'section' => null],
        ['name' => 'bermasalah_jantung_paru', 'label' => '38. Bermasalah dengan jantung dan paru-paru?', 'section' => null],
        ['name' => 'pendarahan_penyakit_darah', 'label' => '39. Menderita pendarahan atau penyakit berhubungan dengan darah?', 'section' => null],
        ['name' => 'hubungan_seksual_tinggal_afrika', 'label' => '40. Berhubungan seksual dengan orang yang tinggal di Afrika?', 'section' => null],
        ['name' => 'tinggal_di_afrika', 'label' => '41. Tinggal di Afrika?', 'section' => null],
    ];

    use SoftDeletes;

    protected $table = 'pemeriksaan_dokter';

    protected $fillable = [
        'kode', 'log_donor_id', 'dokter_id', 'donor_id', 'status', 'puasa',
        'alasan', 'berat_badan', 'data_kuisioner', 'diastole', 'ecg',
        'jenis_kantong', 'keterangan', 'nadi', 'nomor_ruangan', 'sampling',
        'sistole', 'suhu', 'tinggi_badan', 'tipe_jenis_kantong','cc_ambil',
        'tipe_kantong_id',
    ];

    public function logDonor()
    {
        return $this->belongsTo(LogDonor::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Petugas::class, 'dokter_id');
    }

    public function tipeKantong()
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
    
}
