<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AturanSatelit extends Model
{
    protected $table = 'aturan_satelit';
    protected $fillable = [
        'kdtype',
        'typektg',
        'jenisdarah',
        'satelit'
    ];

}