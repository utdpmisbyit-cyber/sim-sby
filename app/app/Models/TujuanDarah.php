<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TujuanDarah extends Model
{
    use SoftDeletes;
    protected $table = 'tujuan_darah';
    protected $fillable = ['kode', 'kelompok_rumah_sakit_id', 'nama'];
    protected $guarded = ['id'];
    
    public function kelompokRumahSakit() {
        return $this->belongsTo(KelompokRumahSakit::class,'kelompok_rumah_sakit_id');
    }
}
