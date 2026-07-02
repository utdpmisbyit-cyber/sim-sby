<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    const JENIS_KELAMIN = ['Pria', 'Wanita'];
    const AGAMA = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'Konghucu'];
    const GOLONGAN_DARAH = ['A', 'B', 'AB', 'O'];
    const RHESUS = ['+', '-'];
    const GOLONGAN_DARAH_LAIN = ['C','D','Negatif','E','c','e','K','k','Fya','Fyb','Jka','Jkb','M','N','S','P1','Lea','Leb','LeaDanLeb','ColdAglutinin','NonDetected','AllPositifPnl'];

    use SoftDeletes;

    protected $table = 'donor';

    protected $fillable = [
        'kode','no_pendaftaran', 'nama', 'alamat_1', 'alamat_2', 'kode_pos', 'tanggal_lahir','usia',
        'jenis_kelamin', 'kewarganegaraan_id', 'wilayah_id', 'kecamatan_id',
        'pekerjaan_id', 'agama', 'no_telp', 'no_ktp', 'no_sim',
        'golongan_darah', 'rhesus', 'golongan_darah_lain', 'golongan_rhesus',
        'cekal', 'tanggal_cekal', 'penghargaan', 'no_cekal',
        'donor_ke','skrining','no_fpup','fpup_id' ,
        'asal_darah_id','nama_asal_darah',

        'counter_cekal', 'is_golongan_darah_locked',
    ];
    protected $attributes = [
        'skrining' => 'Negatif',
    ];
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_cekal' => 'datetime',
        'is_golongan_darah_locked' => 'boolean',
    ];

    public function kewarganegaraan()
    {
        return $this->belongsTo(Kewarganegaraan::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }
    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }
    public function logDonor()
    {
        return $this->hasMany(LogDonor::class, 'donor_id','id');
    }

    public function logDonorAftap()
    {
        return $this->hasMany(LogDonor::class,'donor_id','id')->whereNull('step');
    }

    public function pemeriksaanDokter()
    {
        return $this->hasMany(PemeriksaanDokter::class);
    }

    public function pemeriksaanHb()
    {
        return $this->hasMany(PemeriksaanHB::class);
    }

    public function pemeriksaanKonseling()
    {
        return $this->hasMany(PemeriksaanKonseling::class);
    }

    public function aftap()
    {
        return $this->hasMany(Aftap::class);
    }

    public function getRhesusCaptionAttribute()
    {
        return $this->rhesus === '+' ? 'Positif' : 'Negatif';
    }
}
