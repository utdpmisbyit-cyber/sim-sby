<?php namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class HakAksesPetugas extends Pivot
{
    use SoftDeletes;

    protected $table = 'hak_akses_petugas';

    public $incrementing = true;

    protected $fillable = ['petugas_id', 'hak_akses_id'];

    public function hakAkses()
    {
        return $this->belongsTo(HakAkses::class, 'hak_akses_id');
    }
}
