<?php

namespace App\Services;

use App\Models\PelayananCrosstest;
use App\Models\CrossTest;
use App\Models\PermintaanFpup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PelayananCrosstestService
{
    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PelayananCrosstest::query()
            ->with(['crossTest', 'permintaanFpup'])
            ->latest('tgl_periksa');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_fpup',    'like', "%{$search}%")
                  ->orWhere('no_stock', 'like', "%{$search}%")
                  ->orWhere('pemeriksa','like', "%{$search}%");
            });
        }

        if (!empty($filters['status']))   $query->where('status', $filters['status']);
        if (!empty($filters['hasil']))    $query->where('hasil',  $filters['hasil']);
        if (!empty($filters['tgl_from'])) $query->whereDate('tgl_periksa', '>=', $filters['tgl_from']);
        if (!empty($filters['tgl_to']))   $query->whereDate('tgl_periksa', '<=', $filters['tgl_to']);

        return $query->paginate($perPage)->withQueryString();
    }

    public function find(int $id): PelayananCrosstest
    {
        return PelayananCrosstest::with(['crossTest', 'permintaanFpup'])->findOrFail($id);
    }

    /**
     * Scan No FPUP
     * - jml_minta  : ambil dari permintaan_fpup_detail (jumlah yang diminta)
     * - existing   : kantong yang sudah selesai di-crossmatch (pelayanan_crosstest)
     * - cross_tests: kantong yang sudah diinput di cross_tests (referensi no_stock)
     */
    public function scanFpup(string $noFpup): array
    {
        $fpup = PermintaanFpup::where('no_fpup', $noFpup)->first();

        if (!$fpup) {
            return ['success' => false, 'message' => 'No FPUP tidak ditemukan.'];
        }

        // Ambil cross_tests untuk FPUP ini
        $crossTests = CrossTest::where('no_fpup', $noFpup)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        if ($crossTests->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Data Cross Test untuk FPUP ini belum ada. Silakan buat Cross Test terlebih dahulu.',
            ];
        }

        $crossTest = $crossTests->last();

        // Pelayanan yang sudah tersimpan
        $existing = PelayananCrosstest::where('cross_test_id', $crossTest->id)
            ->orderBy('id')
            ->get();

        // ── jml_minta dari permintaan_fpup_detail ──────────────────────────
        // Coba kolom jumlah / qty / jml di tabel permintaan_fpup_detail
        $jmlMinta = 0;
        try {
            $detail = DB::table('permintaan_fpup_detail')
                ->where('permintaan_fpup_id', $fpup->id)
                ->get();

            if ($detail->isNotEmpty()) {
                // Coba berbagai nama kolom jumlah
                $firstRow = $detail->first();
                $qtyCol   = null;
                foreach (['jumlah','qty','jml','jml_kantong','jumlah_kantong','amount'] as $col) {
                    if (property_exists($firstRow, $col) || isset($firstRow->$col)) {
                        $qtyCol = $col;
                        break;
                    }
                }
                $jmlMinta = $qtyCol
                    ? (int) $detail->sum($qtyCol)
                    : $detail->count();   // fallback: hitung baris
            } else {
                // Fallback: jumlah baris di cross_tests
                $jmlMinta = $crossTests->count();
            }
        } catch (\Exception $e) {
            // Tabel tidak ada / kolom berbeda → fallback
            $jmlMinta = $crossTests->count();
            Log::warning('scanFpup: gagal baca permintaan_fpup_detail - ' . $e->getMessage());
        }
        // ───────────────────────────────────────────────────────────────────

        $jmlPeriksa = $existing->whereNotNull('hasil')->count();
        $jmlCocok   = $existing->where('hasil', 'Cocok')->count();
        $sisa       = max(0, $jmlMinta - $jmlPeriksa);

        return [
            'success'            => true,
            'fpup'               => [
                'no_fpup'         => $fpup->no_fpup,
                'tgl_fpup'        => optional($fpup->created_at)->format('d/m/Y H:i'),
                'nama_pasien'     => $fpup->nama_pasien,
                'umur'            => $fpup->umur,
                'jenis_kelamin'   => $fpup->jenis_kelamin,
                'gol_rh_os'       => $fpup->gol_rh_os,
                'bagian'          => $fpup->bagian,
                'kelas_rawat'     => $fpup->kelas_rawat,
                'kelas_rs'        => $fpup->kelas_rs        ?? null,
                'nama_rs'         => $fpup->nama_rs,
                'no_reg'          => $fpup->no_reg          ?? $fpup->noform ?? null,
                'nama_dokter'     => $fpup->nama_dokter,
                'diagnosa_klinis' => $fpup->diagnosa_klinis,
                'jns_biaya'       => $fpup->jns_biaya,
                'cara_pembayaran' => $fpup->cara_pembayaran,
                'pasien_referal'  => (bool) $fpup->pasien_referal,
            ],
            'cross_test_id'      => $crossTest->id,
            'permintaan_fpup_id' => $fpup->id,
            'existing'           => $existing,
            'cross_tests'        => $crossTests->map(fn($ct) => [
                'id'             => $ct->id,
                'no_stock'       => $ct->no_stock,
                'jns_darah'      => $ct->jns_darah,
                'gol'            => $ct->gol,
                'rhesus'         => $ct->rhesus,
                'gol_rh_kantong' => $ct->gol_rh_kantong,
                'tgl_kadaluarsa' => $ct->tgl_kadaluarsa,
            ]),
            'summary'            => [
                'jml_minta'   => $jmlMinta,
                'jml_periksa' => $jmlPeriksa,
                'jml_cocok'   => $jmlCocok,
                'sisa'        => $sisa,
            ],
        ];
    }

    /**
     * Scan No Stok → dari tabel cross_tests
     * Return juga gol_rh_os pasien untuk auto-check kecocokan
     */
    public function scanStock(string $noStock, string $noFpup = null): array
    {
        try {
            $stock = DB::table('cross_tests')
                ->where('no_stock', $noStock)
                ->whereNull('deleted_at')
                ->latest('id')
                ->first();

            if (!$stock) {
                return ['success' => false, 'message' => "No Stok '{$noStock}' tidak ditemukan."];
            }

            // Parse gol_rh_kantong (contoh: "B+" → gol=B, rhesus=+)
            $golFallback = $rhesusFallback = null;
            if (!empty($stock->gol_rh_kantong)) {
                $raw = trim($stock->gol_rh_kantong);
                $rh  = substr($raw, -1);
                $gol = rtrim($raw, '+-');
                $golFallback    = in_array($gol, ['A','B','AB','O']) ? $gol : null;
                $rhesusFallback = in_array($rh,  ['+','-'])          ? $rh  : null;
            }

            $stockGol = $stock->gol ?? $golFallback;
            $stockRh  = $stock->rhesus ?? $rhesusFallback;

            // Ambil gol_rh_os pasien untuk pencocokan otomatis
            $pasienGolRh = null;
            if ($noFpup) {
                $fpup = PermintaanFpup::where('no_fpup', $noFpup)->first();
                if ($fpup) {
                    $pasienGolRh = $fpup->gol_rh_os;  // contoh: "B+"
                }
            }

            // Cek kecocokan: stok gol+rh vs pasien gol_rh_os
            $isCompatible = false;
            if ($pasienGolRh && $stockGol && $stockRh) {
                // Normalisasi untuk perbandingan (hapus spasi, lowercase)
                $stockFormatted = trim($stockGol . $stockRh);  // "B+"
                $pasienFormatted = strtoupper(str_replace(' ', '', $pasienGolRh));  // "B+"
                $isCompatible = ($stockFormatted === $pasienFormatted);
            }

            return [
                'success'       => true,
                'is_compatible' => $isCompatible,
                'pasien_gol_rh' => $pasienGolRh,
                'stock'         => [
                    'no_stock'       => $stock->no_stock,
                    'jns_darah'      => $stock->jns_darah      ?? null,
                    'gol'            => $stockGol,
                    'rhesus'         => $stockRh,
                    'gol_rh_kantong' => $stock->gol_rh_kantong  ?? null,
                    'tgl_produksi'   => $stock->tgl_produksi    ?? null,
                    'tgl_kadaluarsa' => $stock->tgl_kadaluarsa  ?? null,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('scanStock error', ['no_stock' => $noStock, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error scan stok: ' . $e->getMessage()];
        }
    }

    /**
     * Scan Petugas → cari di tabel petugas
     * Kolom: kode (identifier), nama (nama petugas)
     */
    public function scanPetugas(string $keyword): array
    {
        try {
            // Cari di tabel petugas berdasarkan kode atau nama
            $petugas = DB::table('petugas')
                ->where('kode', $keyword)              // Cari exact match di kode
                ->orWhere('nama', 'like', "%{$keyword}%")  // atau partial match di nama
                ->whereNull('deleted_at')              // Abaikan yang sudah dihapus
                ->first();

            if (!$petugas) {
                return [
                    'success' => false,
                    'message' => "Petugas '{$keyword}' tidak ditemukan di tabel petugas.",
                ];
            }

            return [
                'success' => true,
                'petugas' => [
                    'id'   => $petugas->id,
                    'kode' => $petugas->kode,
                    'name' => $petugas->nama,  // gunakan 'nama' dari petugas
                ],
            ];

        } catch (\Exception $e) {
            Log::error('scanPetugas error', [
                'keyword' => $keyword,
                'error'   => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mencari petugas: ' . $e->getMessage(),
            ];
        }
    }

    public function create(array $data): PelayananCrosstest
    {
        return PelayananCrosstest::create($this->mapPayload($data));
    }

    public function update(PelayananCrosstest $pelayanan, array $data): PelayananCrosstest
    {
        $pelayanan->update($this->mapPayload($data, $pelayanan));
        return $pelayanan;
    }

    public function delete(PelayananCrosstest $pelayanan): void
    {
        $pelayanan->delete();
    }

    protected function mapPayload(array $data, ?PelayananCrosstest $existing = null): array
    {
        return [
            'cross_test_id'      => $data['cross_test_id']      ?? $existing?->cross_test_id,
            'permintaan_fpup_id' => $data['permintaan_fpup_id'] ?? $existing?->permintaan_fpup_id,
            'no_fpup'            => $data['no_fpup']            ?? $existing?->no_fpup,
            'no_stock'           => $data['no_stock']           ?? null,
            'jns_darah'          => $data['jns_darah']          ?? null,
            'gol'                => $data['gol']                ?? null,
            'rhesus'             => $data['rhesus']             ?? null,
            'metode'             => $data['metode']             ?? 'GEL',
            'hasil'              => $data['hasil']              ?: null,
            'nat'                => (bool) ($data['nat']        ?? false),
            'skrining'           => $data['skrining']           ?? '-',
            'keterangan'         => $data['keterangan']         ?? null,
            'catatan'            => $data['catatan']            ?? null,
            'pemeriksa'          => $data['pemeriksa']          ?? null,
            'tgl_periksa'        => $existing?->tgl_periksa     ?? now(),
            'status'             => $data['status']             ?? ($existing?->status ?? 'pending'),
        ];
    }
}