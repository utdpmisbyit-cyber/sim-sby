<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasMasuk extends Model
{
    use SoftDeletes;
    protected $table = 'kas_masuk';
    protected $fillable = [
        'kode', 'program_kerja', 'dokumen', 'ref_an', 'rekning_kas',
        'transaksi', 'nominal', 'keterangan', 'tgl', 'program_kerja_id', 'nama_akun',
    ];
     protected $casts = [
        'tgl' => 'datetime:Y-m-d',
    ];
    public function programKerja() { return $this->belongsTo(ProgramKerja::class,'program_kerja_id', 'id'); }
    public function coa() { return $this->belongsTo(Coa::class, 'nama_akun', 'nama_akun'); }
}
