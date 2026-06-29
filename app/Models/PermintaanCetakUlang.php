<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanCetakUlang extends Model
{
    const STATUS = ['diajukan', 'disetujui', 'ditolak', 'selesai'];

    use SoftDeletes;

    protected $table = 'permintaan_cetak_ulang';

    protected $fillable = [
        'no_surat', 'tanggal_permohonan',
        'nama_pemohon', 'jabatan_pemohon', 'bagian_id',
        'pendataan_kantong_id', 'jumlah_cetak',
        'alasan', 'status',
        'nama_petugas_melayani', 'nama_kasi', 'tgl_disetujui', 'catatan',
        'user_input', 'user_proses',
    ];

    public function bagian()
    {
        return $this->belongsTo(BagianPetugas::class, 'bagian_id');
    }

    public function pendataanKantong()
    {
        return $this->belongsTo(PendataanKantong::class, 'pendataan_kantong_id');
    }
}