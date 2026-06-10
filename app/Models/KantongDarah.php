<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KantongDarah extends Model
{
    use SoftDeletes;

    protected $table = 'kantong_darah';

    protected $fillable = [
        'kode', 'nama', 'stok', 'stok_produksi', 'stok_hasil_produksi',
        'stok_aftap', 'min_stok', 'cabang_id', 'duplikat_cetak',
        'harga_satuan', 'merk', 'tipe_jenis_kantong', 'jenis_kantong', 'ukuran_kantong',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function produksiKantongDarah()
    {
        return $this->hasMany(ProduksiKantongDarah::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->hasMany(KantongDarahHasilProduksi::class);
    }

    public function permintaanAftap()
    {
        return $this->belongsToMany(PermintaanAftap::class, 'kantong_darah_permintaan_aftap', 'kantong_darah_id', 'permintaan_aftap_id')
                    ->withPivot('jumlah');
    }
}
