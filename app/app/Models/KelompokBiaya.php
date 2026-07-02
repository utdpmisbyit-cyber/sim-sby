<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokBiaya extends Model
{
    use SoftDeletes;
    protected $table = 'kelompok_biaya';
    protected $fillable = ['kode', 'nama'];
    public function serviceCost() { return $this->hasMany(ServiceCost::class); }
}
