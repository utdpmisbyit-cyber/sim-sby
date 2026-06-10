<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coa extends Model
{
    const POSSALDO = ['Debit', 'Kredit'];
    const POSLAPORAN = ['Laba-Rugi', 'Neraca'];

    use SoftDeletes;
    protected $table = 'coa';
    protected $primaryKey = 'kd_coa';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['kd_coa', 'kategori_1', 'kategori_2', 'nama_akun', 'possaldo', 'poslaporan'];

    public function trialBalance() { return $this->hasOne(TrialBalance::class, 'coa_id', 'kd_coa'); }
    public function programKerja() {
        return $this->belongsToMany(ProgramKerja::class, 'coa_program_kerja', 'coa_id', 'program_kerja_id');
    }
    public function generalLeadge() { return $this->hasMany(GeneralLeadge::class, 'coa_id', 'kd_coa'); }
    public function kasKeluar() { return $this->hasMany(KasKeluar::class, 'nama_akun', 'nama_akun'); }
    public function kasMasuk() { return $this->hasMany(KasMasuk::class, 'nama_akun', 'nama_akun'); }
    public function penyesuaian() { return $this->hasMany(Penyesuaian::class, 'nama_akun', 'nama_akun'); }
}
