<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PermintaanDarahExternal extends Model
{
    protected $table = 'permintaan_darah_external';
    
    protected $fillable = [
        'nomor_permintaan',
        'tanggal',
        'petugas',
        'petugas_kode',
        'nama_peminta',
        'institusi_lain',
        'jenis_biaya',
        'dropping',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(PermintaanDarahExternalDetail::class, 'permintaan_id');
    }

    /**
     * Generate nomor permintaan format DX26050000001
     * DX + 2 digit tahun + 2 digit bulan + 7 digit urutan
     */
    public static function generateNomorPermintaan()
    {
        $year = date('y');      // 2 digit tahun (26 untuk 2026)
        $month = date('m');     // 2 digit bulan (05 untuk Mei)
        $prefix = 'DX' . $year . $month;
        
        // Cari nomor tertinggi dengan prefix yang sama
        $last = self::where('nomor_permintaan', 'like', $prefix . '%')
            ->orderBy('nomor_permintaan', 'desc')
            ->first();
        
        if ($last) {
            // Ambil 7 digit terakhir sebagai urutan
            $lastNumber = (int) substr($last->nomor_permintaan, -7);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format dengan leading zero 7 digit
        return $prefix . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
    }
}