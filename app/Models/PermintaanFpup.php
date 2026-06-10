<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanFpup extends Model
{
    use SoftDeletes;

    protected $table    = 'permintaan_fpup';
    protected $guarded  = ['id'];

    protected $casts = [
        'tgl_minta'              => 'date',
        'tgl_lahir'              => 'date',
        'tgl_terima'             => 'date',
        'tgl_registrasi_online'  => 'date',
        'transfusi_sebelumnya'   => 'boolean',
        'reaksi_transfusi'       => 'boolean',
        'pernah_serologi'        => 'boolean',
        'pasien_referal'         => 'boolean',
        'cetak_barcode'          => 'boolean',
        'hdn'                    => 'boolean',
    ];

    /* ─── Constants ─── */

    const JENIS_RS = ['SWASTA', 'PEMERINTAH', 'TNI/POLRI', 'KLINIK', 'LAINNYA'];

    const KATEGORI_RS = ['KEL', 'UTD Lain', 'RS Rujukan', 'Puskesmas', 'Klinik'];

    const BAGIAN = [
        'IGD', 'ICU', 'ICCU', 'NICU', 'PICU','ANAK',
        'Rawat Inap', 'Rawat Jalan', 'OK', 'VK', 'Lainnya',
    ];

    const KELAS_RAWAT = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'VIP', 'VVIP', 'Non Kelas'];

    const JNS_PERMINTAAN = ['CITO', 'Biasa','SEWAKTU' ,'Elektif', 'Darurat'];

    const DIAGNOSA = [
        'COMBUS', 'Anemia', 'Thalassemia', 'Leukemia',
        'DHF', 'Perdarahan Post Partum', 'Sepsis',
        'Trauma', 'CKD', 'GI Bleeding', 'Lainnya',
    ];

    const CARA_BAYAR = ['TAGIHAN', 'TUNAI', 'BPJS', 'JKN', 'JAMKESDA', 'GRATIS'];

    const JNS_BIAYA = [
        'NATBPPD', 'BPJS Kesehatan', 'JKN', 'Mandiri',
        'Perusahaan', 'Asuransi', 'Pemerintah',
    ];

    const JNS_DONOR = ['Sukarela', 'Pengganti'];

    const GOL_DARAH = ['A', 'B', 'AB', 'O'];

    const RHESUS = ['Positif', 'Negatif'];

    const JNS_DARAH = [
        'AHF',
        'BC',
        'FP',
        'PCR',
        'PCL',
        'PCLs',
        'TP',
        'WB',
        'PRC',
        'FFP',
        'TC',
        'LP',
        'WE',
        'CP',
    ];

    const STATUS = ['baru', 'proses', 'selesai', 'batal'];

    const KEBANGSAAN = ['INDONESIA', 'ASING'];

    /* ─── Relationships ─── */

    public function details()
    {
        return $this->hasMany(PermintaanFpupDetail::class, 'permintaan_fpup_id');
    }

    public function jenisBiaya()
    {
        return $this->belongsTo(JenisBiaya::class);
    }

    public function getGolRhLabelAttribute(): string
    {
        return $this->gol_rh_os ?? '—';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'baru'    => 'badge-baru',
            'proses'  => 'badge-proses',
            'selesai' => 'badge-selesai',
            'batal'   => 'badge-batal',
            default   => 'badge-baru',
        };
    }
}