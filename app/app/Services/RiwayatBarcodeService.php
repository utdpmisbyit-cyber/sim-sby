<?php

namespace App\Services;

use App\Models\PendataanKantong;
use Illuminate\Support\Facades\DB;

class RiwayatBarcodeService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PendataanKantong();
    }

    /**
     * Daftar semua data pendataan_kantong, dengan filter & pagination.
     * Params yang didukung: q, merk_kantong, jenis_kantong, type_kantong,
     * status, date_from, date_to, per_page.
     */
    public function search(array $params = [])
    {
        $query = $this->baseQuery($params);

        if (!empty($params['q'])) {
            $q = $params['q'];
            $query->where(function ($w) use ($q) {
                $w->where('kode', 'like', "%{$q}%")
                  ->orWhere('barcode', 'like', "%{$q}%")
                  ->orWhere('no_lot', 'like', "%{$q}%");
            });
        }

        if (!empty($params['type_kantong'])) {
            $query->where('type_kantong', $params['type_kantong']);
        }

        $query->orderByDesc('created_at');

        $perPage = $params['per_page'] ?? 20;

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Rekap jumlah baris per merk_kantong, mengikuti filter yang sama
     * dengan search(), tapi tanpa filter q supaya kartu rekap tetap
     * menunjukkan gambaran keseluruhan.
     */
    public function summaryByMerk(array $params = [])
    {
        return $this->baseQuery($params)
            ->select('merk_kantong', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('merk_kantong')
            ->orderByDesc('jumlah')
            ->get();
    }

    public function summaryByJenis(array $params = [])
    {
        return $this->baseQuery($params)
            ->select('jenis_kantong', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('jenis_kantong')
            ->orderByDesc('jumlah')
            ->get();
    }

    /**
     * Rekap jumlah per type_kantong (mis. SG, TR, DJ, dst).
     */
    public function summaryByType(array $params = [])
    {
        return $this->baseQuery($params)
            ->select('type_kantong', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('type_kantong')
            ->orderByDesc('jumlah')
            ->get();
    }

    public function totalGenerated(array $params = [])
    {
        return $this->baseQuery($params)->count();
    }

    /**
     * Query dasar: SEMUA baris di pendataan_kantong (tidak lagi dibatasi
     * harus punya barcode), difilter rentang tanggal & status kalau diisi.
     */
    protected function baseQuery(array $params = [])
    {
        $query = $this->model->newQuery();

        if (!empty($params['merk_kantong'])) {
            $query->where('merk_kantong', $params['merk_kantong']);
        }

        if (!empty($params['jenis_kantong'])) {
            $query->where('jenis_kantong', $params['jenis_kantong']);
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }

        return $query;
    }

    public function merkOptions()
    {
        return PendataanKantong::MERK_KANTONG;
    }

    public function jenisOptions()
    {
        return PendataanKantong::JENIS_KANTONG;
    }

    /**
     * Daftar Type Kantong. Kalau $jenis diisi dan ada di JENIS_TYPE_MAP,
     * hanya kembalikan type yang relevan untuk jenis itu saja
     * (dipakai untuk filter Type yang menyesuaikan pilihan Jenis).
     */
    public function typeOptions(?string $jenis = null)
    {
        if ($jenis && isset(PendataanKantong::JENIS_TYPE_MAP[$jenis])) {
            return PendataanKantong::JENIS_TYPE_MAP[$jenis];
        }

        return PendataanKantong::TYPE_KANTONG;
    }

    public function ukuranOptions()
    {
        return PendataanKantong::UKURAN;
    }

    public function jenisTypeMap()
    {
        return PendataanKantong::JENIS_TYPE_MAP;
    }

    /**
     * Model belum punya konstanta untuk status, jadi sementara tetap
     * diambil dari nilai unik yang ada di tabel.
     */
    public function distinctStatus()
    {
        return $this->model->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}