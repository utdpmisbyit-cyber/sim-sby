<?php

namespace App\Services;

use App\Models\SamplingPraDonor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SamplingPraDonorService
{
   
    protected string $donorTable = 'donor';

    public function normalRanges(): array
    {
        return [
            'wbc'    => ['M' => [4.8, 10.8], 'F' => [4.8, 10.8]],
            'neut'   => [1.50, 7.00],
            'lymph'  => [1.00, 3.70],
            'mono'   => [0.16, 0.70],
            'eo'     => [0.00, 0.80],
            'baso'   => [0.00, 1.00],
            'ig'     => [0.00, 1.00],

            'rbc'    => ['M' => [4.7, 6.1], 'F' => [4.2, 5.4]],
            'hgb'    => ['M' => [14.0, 18.0], 'F' => [12.0, 16.0]],
            'hct'    => ['M' => [42.0, 52.0], 'F' => [37.0, 47.0]],
            'mcv'    => [81.0, 99.0],
            'mch'    => [27.0, 31.0],
            'mchc'   => [33.0, 37.0],
            'rdw_sd' => [35.0, 47.0],
            'rdw_cv' => [11.5, 14.5],

            'plt'    => [150, 450],
            'pdw'    => [9.0, 13.0],
            'mpv'    => [7.2, 11.1],
            'p_lcr'  => [15.0, 25.0],
            'pct'    => [4.8, 10.8],
        ];
    }

    public function list(?string $search, int $perPage = 15)
    {
        return SamplingPraDonor::search($search)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): SamplingPraDonor
    {
        return SamplingPraDonor::findOrFail($id);
    }

    /**
     * Generate No Transaksi baru, format: SML-YYMMDD + kode petugas + running number.
     * Contoh dari screenshot: SML-2606301C05
     */
    public function generateKode(): string
    {
        $tanggal = now()->format('ymd');
        $kodePetugas = Auth::user()->kode_petugas ?? 'C05';

        $prefix = "SML-{$tanggal}{$kodePetugas}";

        $last = SamplingPraDonor::where('no_transaksi', 'like', "{$prefix}%")
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $urut = 1;
        if ($last) {
            $urut = ((int) substr($last, -3)) + 1;
        }

        return $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);
    }

    public function create(array $data): SamplingPraDonor
    {
        return DB::transaction(function () use ($data) {
            $data['petugas_id'] = $data['petugas_id'] ?? Auth::id();
            $data['server_date'] = $data['server_date'] ?? now();

            return SamplingPraDonor::create($data);
        });
    }

    public function update(SamplingPraDonor $sampling, array $data): SamplingPraDonor
    {
        return DB::transaction(function () use ($sampling, $data) {
            $sampling->update($data);
            return $sampling->fresh();
        });
    }

    public function delete(SamplingPraDonor $sampling): bool
    {
        return (bool) $sampling->delete();
    }

    /**
     * Cari data donor dari tabel `donor` berdasarkan Kode Donor (hasil scan barcode/manual),
     * fallback ke No KTP kalau yang di-scan/diketik adalah nomor KTP.
     * Dipanggil dari endpoint pencarian donor untuk auto-fill form.
     */
    public function findDonorReference(string $kodeDonor): ?array
    {
        $donor = DB::table($this->donorTable)
            ->where('kode', $kodeDonor)
            ->orWhere('no_ktp', $kodeDonor)
            ->whereNull('deleted_at')
            ->latest('id')
            ->first();

        if (!$donor) {
            return null;
        }

        return [
            'donor_id'       => $donor->id,
            'no_donor'       => $donor->kode,
            'nama_donor'     => $donor->nama,
            'tgl_lahir'      => $donor->tanggal_lahir,
            'jenis_kelamin'  => $this->normalizeJenisKelamin($donor->jenis_kelamin),
            'golongan_darah' => $donor->golongan_darah,
            'rhesus'         => $donor->rhesus,
        ];
    }

    /**
     * Samakan nilai jenis kelamin dari tabel donor ('Pria'/'Wanita', 'L'/'P', dst)
     * ke format yang dipakai form ini: 'pria' / 'wanita'.
     */
    protected function normalizeJenisKelamin(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $v = strtolower(trim($value));

        return match (true) {
            in_array($v, ['l', 'laki-laki', 'laki laki', 'pria', 'male', 'm']) => 'pria',
            in_array($v, ['p', 'perempuan', 'wanita', 'female', 'f']) => 'wanita',
            default => null,
        };
    }
}