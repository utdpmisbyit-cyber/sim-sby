<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsalDarah extends Model
{
    use SoftDeletes;

    protected $table = 'asal_darah';

    protected $fillable = [
        'kode', 'nama', 'alamat_1', 'alamat_2', 'kode_pos', 'no_telp',
        'nama_sponsor', 'no_telp_sponsor',
    ];

    public function aftap()
    {
        return $this->hasMany(Aftap::class);
    }
    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }

}
