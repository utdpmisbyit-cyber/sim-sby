<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pekerjaan extends Model
{
    use SoftDeletes;

    protected $table = 'pekerjaan';

    protected $fillable = ['kode', 'nama'];

    public function donor()
    {
        return $this->hasMany(Donor::class);
    }
}
