<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramKerja extends Model
{
    use SoftDeletes;

    protected $table = 'program_kerja';

    protected $fillable = [
        'kode', 'nama_program', 'keterangan', 'pic_id',
    ];

    public function pic()
    {
        return $this->belongsTo(Petugas::class, 'pic_id');
    }

    public function petugas()
    {
        return $this->hasMany(Petugas::class);
    }

    public function coa()
    {
        return $this->belongsToMany(Coa::class, 'coa_program_kerja', 'program_kerja_id', 'coa_id');
    }

    public function kasKeluar()
    {
        return $this->hasMany(KasKeluar::class);
    }

    public function kasMasuk()
    {
        return $this->hasMany(KasMasuk::class);
    }

    public function penyesuaian()
    {
        return $this->hasMany(Penyesuaian::class);
    }

    public function generalLeadge()
    {
        return $this->hasMany(GeneralLeadge::class);
    }
}
