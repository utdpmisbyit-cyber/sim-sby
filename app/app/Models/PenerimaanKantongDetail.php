<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class PenerimaanKantongDetail extends Model
{
    protected $table = 'penerimaan_kantong_detail';
    public $timestamps = true;
    protected $fillable = [
        'penerimaan_id','no_kantong','merk','jenis','ukuran','no_lot'
    ];
}