<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaCrossTest extends Model
{
    use SoftDeletes;
    protected $table = 'biaya_cross_test';
    protected $fillable = ['kode', 'harga'];
}
