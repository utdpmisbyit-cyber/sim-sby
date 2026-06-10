<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKantong extends Model
{
    protected $table = 'jenis_kantong';
    protected $fillable = [
        'nama',
        'list_satuan'
    ];
}
