<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnosa extends Model
{
    use SoftDeletes;
    protected $table = 'diagnosa';
    protected $fillable = ['kode', 'nama'];
    protected $guarded = ['id'];
}
