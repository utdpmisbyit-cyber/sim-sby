<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogDonor extends Model
{
    const NEXT_STEP = ['Registrasi', 'Kesehatan', 'HB', 'Aftap', 'Rejected'];

    use SoftDeletes;

    protected $table = 'log_donor';

    protected $fillable = [
        'kode', 'cabang_id', 'donor_id', 'petugas_registrasi_id', 'step','status','nomor_ruangan',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function petugasRegistrasi()
    {
        return $this->belongsTo(Petugas::class, 'petugas_registrasi_id');
    }

    public function pemeriksaanDokter()
    {
        return $this->hasOne(PemeriksaanDokter::class);
    }

    public function pemeriksaanHb()
    {
        return $this->hasOne(PemeriksaanHB::class,'log_donor_id');
    }

    public function pemeriksaanKonseling()
    {
        return $this->hasOne(PemeriksaanKonseling::class);
    }

    public function aftap()
    {
        return $this->hasOne(Aftap::class);
    }
    public function logDonorAftap()
    {
        return $this->hasMany(LogDonor::class, 'donor_id', 'donor_id')
            ->whereHas('aftap');
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->hasOne(KantongDarahHasilProduksi::class);
    }

}
