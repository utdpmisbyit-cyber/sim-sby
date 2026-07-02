<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorksheetUmumSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'worksheet_umum_serologi';

    protected $fillable = [
        'kode', 'jenis_periksa', 'plate_serologi_id', 'metode_serologi_id', 'reagen_serologi_id',
    ];

    public function plateSerologi()
    {
        return $this->belongsTo(PlateSerologi::class);
    }

    public function metodeSerologi()
    {
        return $this->belongsTo(MetodeSerologi::class);
    }

    public function reagenSerologi()
    {
        return $this->belongsTo(ReagenSerologi::class);
    }

    public function mappingSerologiResult()
    {
        return $this->hasMany(MappingSerologiResult::class);
    }
}
