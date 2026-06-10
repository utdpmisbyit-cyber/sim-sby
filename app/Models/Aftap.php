<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aftap extends Model
{
    const STATUS = ['Pending', 'Ongoing', 'Approved', 'Rejected', 'Cancelled'];
    const CARA_AMBIL = ['Konvensional'];
    const JENIS_DONOR = ['Sukarela', 'Pengganti'];
    const REAKSI_DONOR = ['Normal', 'Muntah', 'Pingsan', 'Pusing', 'Lain-lain'];

    use SoftDeletes;

    protected $table = 'aftap';

    protected $fillable = [
        'kode','bed','log_donor_id', 'dokter_id', 'donor_id', 'asal_darah_id', 'status',
        'alamat_surat', 'bersedia_dikirim_surat', 'cara_ambil', 'cuci_tangan','lengan',
        'darah_lancar', 'donor_sewaktu_waktu', 'id_hemoscale', 'jam_mulai',
        'jam_selesai', 'jenis_donor', 'kantong_penuh', 'keterangan', 'lain_lain',
        'cc_ambil','satelit','durasi',
        'no_kantong', 'no_selang', 'penusukan_sulit', 'reaksi_donor', 'sample_darah', 'catatan',
    ];

    protected $casts = [
        'bersedia_dikirim_surat' => 'boolean',
        'cuci_tangan' => 'boolean',
        'darah_lancar' => 'boolean',
        'donor_sewaktu_waktu' => 'boolean',
        'penusukan_sulit' => 'boolean',
        'sample_darah' => 'boolean',
    ];

    public function logDonor()
    {
        return $this->belongsTo(LogDonor::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Petugas::class, 'dokter_id');
    }
     public function petugasPanggil()
    {
        return $this->belongsTo(Petugas::class, 'petugas_panggil_id');
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class);
    }
}
