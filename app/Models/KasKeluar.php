<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasKeluar extends Model
{
    use SoftDeletes;
    protected $table = 'kas_keluar';
    protected $fillable = [
        'kode', 'program_kerja', 'dokumen', 'dibayar_ke', 'ref_an',
        'rekning_kas', 'transaksi', 'nominal', 'keterangan', 'tgl',
        'program_kerja_id', 'nama_akun',
    ];
    protected $casts = [
        'tgl' => 'datetime',
    ];
    public function programKerja() { return $this->belongsTo(ProgramKerja::class); }
    public function coa() { return $this->belongsTo(Coa::class, 'nama_akun', 'nama_akun'); }
}
