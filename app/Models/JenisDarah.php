<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisDarah extends Model
{
    use SoftDeletes;
    protected $table = 'jenis_darah';
    protected $fillable = ['kode', 'nama', 'nama_pendek', 'umur_darah'];
}
