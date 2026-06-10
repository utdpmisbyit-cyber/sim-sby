<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanBankDarahInternalDetail extends Model
{
    protected $table =
        'pengiriman_bank_darah_internal_detail';

    protected $guarded = [];

    public function header()
    {
        return $this->belongsTo(
            PengirimanBankDarahInternal::class,
            'pengiriman_bank_darah_internal_id'
        );
    }
}