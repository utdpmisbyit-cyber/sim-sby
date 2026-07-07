<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenyisihanKantongAftap extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyisihan_kantong_aftap';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PenyisihanKantongAftapDetail::class, 'penyisihan_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Generate nomor transaksi otomatis, format: PSH/YYYYMMDD/0001
     */
    public static function generateNoTransaksi(\DateTimeInterface $tanggal): string
    {
        $prefix = 'PSH/' . $tanggal->format('Ymd') . '/';

        $last = static::withTrashed()
            ->where('no_transaksi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $urut = 1;
        if ($last) {
            $urut = (int) substr($last->no_transaksi, strlen($prefix)) + 1;
        }

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
}