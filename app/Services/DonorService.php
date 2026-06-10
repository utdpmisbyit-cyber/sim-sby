<?php

namespace App\Services;

use App\Models\Donor;
use Illuminate\Support\Facades\DB;

class DonorService extends IoService
{
    public array $jenis_kelamin = Donor::JENIS_KELAMIN;
    public array $agama = Donor::AGAMA;
    public array $golongan_darah = Donor::GOLONGAN_DARAH;
    public array $rhesus = Donor::RHESUS;
    public array $golongan_darah_lain = Donor::GOLONGAN_DARAH_LAIN;

    public function __construct()
    {
        $this->model = new Donor();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = [
            'kode','nama','no_pendaftaran', 'jenis_kelamin', 'kewarganegaraan_id', 'wilayah_id',
            'kecamatan_id', 'pekerjaan_id', 'agama', 'golongan_darah', 'rhesus', 'skrining',
            'no_fpup',
            'fpup_id',
        ];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');

        $no_ktp = $params['no_ktp'] ?? '';
        if ($no_ktp !== '') $model = $model->where('no_ktp', 'like', '%' . $no_ktp . '%');

        $no_sim = $params['no_sim'] ?? '';
        if ($no_sim !== '') $model = $model->where('no_sim', 'like', '%' . $no_sim . '%');

        $search = $params['search'] ?? '';
        if ($search !== '') {
            $model = $model->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('no_ktp', 'like', '%' . $search . '%')
                    ->orWhere('kode', 'like', '%' . $search . '%')
                    ->orWhere('no_telp', 'like', '%' . $search . '%');
            });
        }

        $cekal = $params['cekal'] ?? '';
        if ($cekal !== '') $model = $model->whereNotNull('cekal');

        return $model;
    }

    /**
     * Generate kode donor: T + YY + MM + 4-digit urut
     * Contoh: T260400001, T260400002, dst.
     * Format T2604000 artinya T + 26(tahun) + 04(bulan) + 000(urut)
     */
    public function generateKode(): string
    {
        return DB::transaction(function () {
            $yymm = date('ym'); // misal: 2604 untuk April 2026
            $prefix = 'T' . $yymm;

            // Ambil kode terakhir dengan prefix bulan ini
            $last = $this->model->newQuery()
                ->lockForUpdate()
                ->where('kode', 'like', $prefix . '%')
                ->withTrashed()
                ->orderByDesc('kode')
                ->first();

            if (!$last) {
                $nextNumber = 1;
            } else {
                // Ambil angka urut di belakang prefix
                $suffix = substr($last->kode, strlen($prefix));
                $nextNumber = (int) preg_replace('/[^0-9]/', '', $suffix) + 1;
            }

            // Format: T2604 + 4 digit urut = T26040001
            return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate no_pendaftaran: A + YY + MM + DD + 4-digit urut
     * Contoh: A2604290001 untuk 29 April 2026
     */
    public function generateNoPendaftaran(): string
    {
        return DB::transaction(function () {
            $yymmdd = date('ymd'); // misal: 260429
            $prefix = 'A' . $yymmdd;

            $last = $this->model->newQuery()
                ->lockForUpdate()
                ->where('no_pendaftaran', 'like', $prefix . '%')
                ->withTrashed()
                ->orderByDesc('no_pendaftaran')
                ->first();

            if (!$last) {
                $nextNumber = 1;
            } else {
                $suffix = substr($last->no_pendaftaran, strlen($prefix));
                $nextNumber = (int) preg_replace('/[^0-9]/', '', $suffix) + 1;
            }

            // Format: A260429 + 5 digit urut = A2604290001
            return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Hitung donor_ke berdasarkan no_ktp.
     * Jika donor dengan no_ktp sudah ada, kembalikan donor_ke + 1.
     * Jika belum ada, kembalikan 1.
     * Juga kembalikan golongan_darah dan rhesus jika sudah ada.
     */
    public function getDonorKeByKtp(string $no_ktp): array
    {
        $existing = $this->model->newQuery()
            ->where('no_ktp', $no_ktp)
            ->withTrashed()
            ->orderByDesc('donor_ke')
            ->first();

        if (!$existing) {
            return [
                'donor_ke'      => 1,
                'golongan_darah' => null,
                'rhesus'         => null,
            ];
        }

        return [
            'donor_ke'       => ($existing->donor_ke ?? 0) + 1,
            'golongan_darah' => $existing->golongan_darah,
            'rhesus'         => $existing->rhesus,
        ];
    }
}