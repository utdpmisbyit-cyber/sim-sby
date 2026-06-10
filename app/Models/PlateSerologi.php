<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlateSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'plate_serologi';

    protected $fillable = ['kode'];

    public function worksheetUmumSerologi()
    {
        return $this->hasMany(WorksheetUmumSerologi::class);
    }

    public function plateMappingSerologi()
    {
        return $this->hasMany(PlateMappingSerologi::class);
    }
}
