<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PenyisihanCrossmatch extends Model
{
    use SoftDeletes;

    protected $table = 'penyisihan_crossmatch';

    protected $fillable = [
        'no_penyisihan',
        'tanggal_penyisihan',
        'petugas',
        'jumlah',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penyisihan' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PenyisihanCrossmatchDetail::class, 'penyisihan_crossmatch_id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('no_penyisihan', 'like', "%{$term}%")
              ->orWhere('petugas', 'like', "%{$term}%");
        });
    }
}