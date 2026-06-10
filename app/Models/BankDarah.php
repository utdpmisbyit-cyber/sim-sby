<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDarah extends Model
{
    const JENIS = ['Pemerintah', 'Swasta'];
    use SoftDeletes;
    protected $table = 'bank_darah';
    protected $fillable = ['kode', 'nama', 'alamat', 'jenis'];
}
