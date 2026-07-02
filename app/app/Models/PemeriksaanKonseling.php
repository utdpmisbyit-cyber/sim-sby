<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanKonseling extends Model
{
    const STATUS = ['Pending', 'Ongoing', 'Approved', 'Rejected', 'Cancelled'];

    use SoftDeletes;

    protected $table = 'pemeriksaan_konseling';

    protected $fillable = [
        'kode', 'log_donor_id', 'donor_id', 'konselor_id', 'status',
        'catatan', 'cekal', 'hasil_pantau', 'jenis_periksa', 'kesimpulan',
        'nilai_cov', 'nilai_od', 'status_periksa', 'tanggal_aftap',
    ];

    protected $casts = [
        'tanggal_aftap' => 'datetime',
        'cekal'         => 'boolean',
    ];

    public function logDonor()
    {
        return $this->belongsTo(LogDonor::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function konselor()
    {
        return $this->belongsTo(Petugas::class, 'konselor_id');
    }
}
