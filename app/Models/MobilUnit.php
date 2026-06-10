<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobilUnit extends Model
{
    use SoftDeletes;
    protected $table = 'mobil_unit';
    protected $fillable = ['kode', 'merk_mobil', 'no_polisi', 'tahun_produksi', 'tahun_beli'];
}
