<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengambilanDarahApheresis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'apheresis_pengambilan_darah';

    protected $fillable = [
        'no_transaksi', 'server_date', 'petugas_id',
        'no_sampling', 'no_donor', 'nama_donor', 'tgl_lahir',

        'type_mesin', 'no_mesin', 'operator', 'kode_disposable_kit',
        'type_ac_ratio', 'cairan_saline',
        'no_lot_1', 'kadaluarsa_lot_1',
        'no_lot_2', 'kadaluarsa_lot_2',
        'no_lot_3', 'kadaluarsa_lot_3',

        'golongan_darah', 'rhesus', 'jenis_kelamin',
        'riwayat_donor_sebelumnya', 'riwayat_donor_sebelumnya_kali',
        'riwayat_donor_apheresis', 'riwayat_donor_apheresis_kali',

        'tinggi_badan', 'berat_badan', 'hct', 'platelet_precount',
        'target_vol_plasma', 'target_platelet_yield', 'target_cycle',
        'target_waktu', 'estimasi_vol_plt',

        'waktu_mulai', 'waktu_selesai', 'durasi',
        'vol_wb_terproses', 'vol_ac_terpakai', 'vol_saline_terpakai',
        'draw_rate', 'return_rate', 'plt_hct_postcount',

        'platelet_total_vol_aktual', 'platelet_vol_plt',
        'platelet_vol_plasma_dlm_plt', 'platelet_ac_dlm_plt', 'platelet_yield_plt',

        'plasma_total_vol_aktual', 'plasma_vol_plasma', 'plasma_ac_dlm_plasma',

        'catatan', 'operator_akhir',
    ];

    protected $casts = [
        'server_date' => 'datetime',
        'tgl_lahir' => 'date',
        'kadaluarsa_lot_1' => 'date',
        'kadaluarsa_lot_2' => 'date',
        'kadaluarsa_lot_3' => 'date',
    ];

    public function siklus()
    {
        return $this->hasMany(PengambilanDarahSiklus::class, 'pengambilan_darah_id')
            ->orderBy('siklus_ke');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('no_transaksi', 'like', "%{$term}%")
              ->orWhere('no_donor', 'like', "%{$term}%")
              ->orWhere('nama_donor', 'like', "%{$term}%");
        });
    }
}