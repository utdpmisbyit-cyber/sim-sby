<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReagenSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'reagen_serologi';

    protected $fillable = ['kode', 'nama'];

    public function worksheetUmumSerologi()
    {
        return $this->hasMany(WorksheetUmumSerologi::class);
    }
}
