<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokKantongPenerimaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_kantong_penerimaan';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(StokKantongPenerimaanDetail::class, 'penerimaan_id');
    }
}