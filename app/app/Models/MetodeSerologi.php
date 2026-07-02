<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodeSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'metode_serologi';

    protected $fillable = ['kode', 'nama'];

    public function worksheetUmumSerologi()
    {
        return $this->hasMany(WorksheetUmumSerologi::class);
    }
}
