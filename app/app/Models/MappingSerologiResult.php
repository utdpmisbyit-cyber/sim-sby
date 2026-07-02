<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MappingSerologiResult extends Model
{
    use SoftDeletes;

    protected $table = 'mapping_serologi_result';

    protected $fillable = [
        'plate_mapping_serologi_id', 'hasil_tahapan', 'kesimpulan',
        'worksheet_umum_serologi_id', 'hasil_cov', 'hasil_od',
    ];

    public function plateMappingSerologi()
    {
        return $this->belongsTo(PlateMappingSerologi::class);
    }

    public function worksheetUmumSerologi()
    {
        return $this->belongsTo(WorksheetUmumSerologi::class);
    }
}
