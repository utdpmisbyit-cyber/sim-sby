<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengembalianBiayaCrosstest extends Model
{
    use SoftDeletes;

    protected $table = 'pengembalian_biaya_crosstest';

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_fpup'    => 'date',
        'tgl_retur'   => 'datetime',
        'sub_total'   => 'decimal:2',
        'total_retur' => 'decimal:2',
    ];

    public function fpup()
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    public function jenisBiaya()
    {
        return $this->belongsTo(JenisBiaya::class, 'jenis_biaya_id');
    }

    public function details()
    {
        return $this->hasMany(PengembalianBiayaCrosstestDetail::class, 'pengembalian_biaya_crosstest_id');
    }
}