<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengembalianBiayaCrosstestDetail extends Model
{
    protected $table = 'pengembalian_biaya_crosstest_detail';

    protected $guarded = ['id'];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function header()
    {
        return $this->belongsTo(PengembalianBiayaCrosstest::class, 'pengembalian_biaya_crosstest_id');
    }

    public function fpupDetail()
    {
        return $this->belongsTo(PermintaanFpupDetail::class, 'permintaan_fpup_detail_id');
    }
}