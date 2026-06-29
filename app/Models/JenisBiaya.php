<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisBiaya extends Model
{
    use SoftDeletes;
    protected $table = 'jenis_biaya';
    protected $fillable = ['kode', 'nama'];
    public function serviceCost() { return $this->hasMany(ServiceCost::class, 'jenis_biaya_id'); }
}
