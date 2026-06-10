<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanHB extends Model
{
    const STATUS = ['Pending', 'Ongoing', 'Approved', 'Rejected', 'Cancelled'];
    const GOLONGAN_DARAH = ['A', 'B', 'AB', 'O'];
    const RHESUS = ['+', '-'];
    const LENGAN = ['Kiri/Kanan', 'Kiri', 'Kanan'];
    const METODE = ['HB Meter', 'HB Cupri'];

    use SoftDeletes;

    protected $table = 'pemeriksaan_hb';

    protected $fillable = [
        'kode', 'log_donor_id', 'dokter_id', 'donor_id', 'status',
        'alasan_ditolak', 'eritrosit', 'golongan_darah', 'hb_meter',
        'hematocrit', 'lecosit', 'lengan', 'metode', 'rhesus',
        'sampling', 'screening', 'trombosit',
    ];

    public function logDonor()
    {
        return $this->belongsTo(LogDonor::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Petugas::class, 'dokter_id');
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
