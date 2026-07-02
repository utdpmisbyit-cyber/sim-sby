<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlateMappingSerologi extends Model
{
    public $timestamps = false;

    protected $table = 'plate_mapping_serologi';

    protected $fillable = ['address', 'barcode', 'plate_serologi_id'];

    public function plateSerologi()
    {
        return $this->belongsTo(PlateSerologi::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->belongsTo(KantongDarahHasilProduksi::class, 'barcode', 'kode');
    }

    public function mappingSerologiResult()
    {
        return $this->hasMany(MappingSerologiResult::class);
    }
}
