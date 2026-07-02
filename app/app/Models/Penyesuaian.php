<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyesuaian extends Model
{
    const JENIS_SALDO = ['SaldoAwal', 'Lainnya'];

    use SoftDeletes;
    protected $table = 'penyesuaian';
    protected $fillable = [
        'kode', 'program_kerja', 'dokumen', 'ref_bayar', 'transaksi_coa',
        'nominal_debit', 'nominal_kredit', 'keterangan', 'jenis_saldo',
        'tgl', 'program_kerja_id', 'nama_akun',
    ];
    public function programKerja() { return $this->belongsTo(ProgramKerja::class); }
    public function coa() { return $this->belongsTo(Coa::class, 'nama_akun', 'nama_akun'); }
    public function generalLeadge() { return $this->hasMany(GeneralLeadge::class); }
}
