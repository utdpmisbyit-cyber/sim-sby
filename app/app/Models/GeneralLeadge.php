<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralLeadge extends Model
{
    use SoftDeletes;
    protected $table = 'general_leadge';
    protected $fillable = [
        'kode', 'no_dokumen', 'program_kerja', 'referensi', 'coa_id',
        'nominal_debit', 'nominal_kredit', 'keterangan', 'saldo_awal',
        'dibayarkan_ke', 'rekening_kas', 'kode_transaksi', 'nominal_rp',
        'lawan_transaksi', 'terima_dari', 'bs', 'pl', 'inventory',
        'hutang', 'piutang', 'tgl', 'program_kerja_id', 'nama_akun',
        'penyesuaian_id', 'trial_balance_id',
    ];
    public function coa() { return $this->belongsTo(Coa::class, 'coa_id', 'kd_coa'); }
    public function programKerja() { return $this->belongsTo(ProgramKerja::class); }
    public function penyesuaian() { return $this->belongsTo(Penyesuaian::class); }
    public function trialBalance() { return $this->belongsTo(TrialBalance::class); }
}
