<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCost extends Model
{
    const JENIS = ['Pemerintah', 'Swasta'];
    use SoftDeletes;
    protected $table = 'service_cost';
    protected $fillable = ['kode','nama' ,'jenis', 'biaya', 'jenis_biaya_id', 'kelompok_biaya_id'];
    public function jenisBiaya() { return $this->belongsTo(JenisBiaya::class, 'jenis_biaya_id'); }
    public function kelompokBiaya() { return $this->belongsTo(KelompokBiaya::class); }
     protected $casts = [
        'biaya' => 'decimal:2',
    ];

}
