<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Petugas extends Model
{
    use SoftDeletes;

    protected $table = 'petugas';

    protected $fillable = [
        'kode', 'nama', 'alamat_1', 'alamat_2', 'kode_pos', 'user_id',
        'no_telp', 'cabang_id', 'jabatan_id', 'bagian_id', 'program_kerja_id',
        'tanda_tangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function bagian()
    {
        return $this->belongsTo(BagianPetugas::class, 'bagian_id');
    }

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class);
    }

    public function hakAkses()
    {
        return $this->hasOne(HakAksesPetugas::class, 'petugas_id');
    }

    public function logDonorRegistrasi()
    {
        return $this->hasMany(LogDonor::class, 'petugas_registrasi_id');
    }

    public function pemeriksaanDokterSebagaiDokter()
    {
        return $this->hasMany(PemeriksaanDokter::class, 'dokter_id');
    }

    public function pemeriksaanHbSebagaiDokter()
    {
        return $this->hasMany(PemeriksaanHB::class, 'dokter_id');
    }

    public function pemeriksaanKonselingSebagaiKonselor()
    {
        return $this->hasMany(PemeriksaanKonseling::class, 'konselor_id');
    }

    public function aftapSebagaiDokter()
    {
        return $this->hasMany(Aftap::class, 'dokter_id');
    }

    public function pengirimanSerologiSebagaiPengirim()
    {
        return $this->hasMany(PengirimanSerologi::class, 'pengirim_id');
    }

    public function pengirimanSerologiSebagaiPenerima()
    {
        return $this->hasMany(PengirimanSerologi::class, 'penerima_id');
    }

    public function pengirimanProduksiSebagaiPengirim()
    {
        return $this->hasMany(PengirimanProduksi::class, 'pengirim_id');
    }

    public function pengirimanProduksiSebagaiPenerima()
    {
        return $this->hasMany(PengirimanProduksi::class, 'penerima_id');
    }

    public function pengajuanBarang()
    {
        return $this->hasMany(PengajuanBarang::class);
    }

    public function opnameBarang()
    {
        return $this->hasMany(OpnameBarang::class);
    }

    public function pinjamBarang()
    {
        return $this->hasMany(PinjamBarang::class);
    }

    public function returPinjam()
    {
        return $this->hasMany(ReturPinjam::class);
    }
}
