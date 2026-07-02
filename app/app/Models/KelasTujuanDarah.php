<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelasTujuanDarah extends Model
{
    use SoftDeletes;
    protected $table = 'kelas_tujuan_darah';
    protected $fillable = ['kode', 'nama'];
}
