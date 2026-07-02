<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerologiDetail extends Model
{
    protected $table = 'serologi_detail';

    protected $fillable = [
        'serologi_id',
        'aftap_id',
        'no_kantong',
        'status',
        'hasil',
        'keterangan',
    ];

    public function serologi()
    {
        return $this->belongsTo(Serologi::class, 'serologi_id');
    }

    public function aftap()
    {
        return $this->belongsTo(Aftap::class, 'aftap_id');
    }
}
