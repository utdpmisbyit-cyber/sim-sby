<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialBalance extends Model
{
    use SoftDeletes;
    protected $table = 'trial_balance';
    protected $fillable = [
        'kode', 'sa_debet', 'sa_kredit', 'debet', 'kredit',
        'laba_debet', 'laba_kredit', 'neraca_debet', 'neraca_kredit',
        'kategori1', 'kategori2', 'pos_saldo', 'pos_laporan', 'coa_id', 'nama_akun',
    ];
    public function coa() { return $this->belongsTo(Coa::class, 'coa_id', 'kd_coa'); }
    public function generalLeadge() { return $this->hasMany(GeneralLeadge::class); }
}
