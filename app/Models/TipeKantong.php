<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKantong extends Model
{
    protected $table = 'tipe_kantong';
    protected $fillable = [
        'jenis_kantong_id',
        'nama'
    ];

    public function jenisKantong()
    {
        return $this->belongsTo(JenisKantong::class, 'jenis_kantong_id');
    }
    
}
