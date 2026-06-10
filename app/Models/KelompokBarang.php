<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokBarang extends Model
{
    use SoftDeletes;
    protected $table = 'kelompok_barang';
    protected $fillable = ['kode', 'nama'];
}
