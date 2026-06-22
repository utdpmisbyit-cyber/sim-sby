<?php

namespace App\Services;

use App\Models\PelayananCrosstestReferal;
use App\Models\CrossTestReferal;
use App\Models\PermintaanFpupReferal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PelayananCrosstestReferalService
{
    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PelayananCrosstestReferal::query()
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

    public function find(int $id): PelayananCrosstestReferal
    {
        return PelayananCrosstestReferal::with(['crossTest', 'permintaanFpup'])->findOrFail($id);
    }

    /**
     * Scan No FPUP
     */
    public function scanFpup(string $noFpup): array
    {
        $fpup = PermintaanFpupReferal::where('no_fpup', $noFpup)->first();

        if (!$fpup) {
            return ['success' => false, 'message' => 'No FPUP tidak ditemukan.'];
        }

        // FIX #1: CrossTest -> CrossTestReferal (class sebelumnya tidak ada / tidak di-import)
        $crossTests = CrossTestReferal::where('no_fpup', $noFpup)
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

        $existing = PelayananCrosstestReferal::where('cross_test_id', $crossTest->id)
            ->orderBy('id')
            ->get();

        // ── jml_minta dari permintaan_fpup_referal_detail ──────────────────
        $jmlMinta = 0;
        try {
            $detail = DB::table('permintaan_fpup_referal_detail')
                ->where('permintaan_fpup_referal_id', $fpup->id)
                ->get();

            if ($detail->isNotEmpty()) {
                $firstRow = $detail->first();
                $qtyCol   = null;
                foreach (['jumlah','qty','jml','jml_kantong','jumlah_kantong','amount'] as $col) {
                    if (isset($firstRow->$col)) {
                        $qtyCol = $col;
                        break;
                    }
                }
                $jmlMinta = $qtyCol
                    ? (int) $detail->sum($qtyCol)
                    : $detail->count();
            } else {
                $jmlMinta = $crossTests->count();
            }
        } catch (\Exception $e) {
            $jmlMinta = $crossTests->count();
            Log::warning('scanFpup: gagal baca permintaan_fpup_referal_detail - ' . $e->getMessage());
        }
        // ─────────────────────────────────────────────────────────────────

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
            'permintaan_fpup_referal_id' => $fpup->id,
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
     * Scan No Stok → dari tabel cross_tests_referal
     */
    public function scanStock(string $noStock, string $noFpup = null): array
    {
        try {
            $stock = DB::table('cross_tests_referal')
                ->where('no_stock', $noStock)
                ->whereNull('deleted_at')
                ->latest('id')
                ->first();

            if (!$stock) {
                return ['success' => false, 'message' => "No Stok '{$noStock}' tidak ditemukan."];
            }

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

            // FIX #2: PermintaanFpup -> PermintaanFpupReferal
            $pasienGolRh = null;
            $pasienNorm  = null;
            if ($noFpup) {
                $fpup = PermintaanFpupReferal::where('no_fpup', $noFpup)->first();
                if ($fpup) {
                    $pasienGolRh = $fpup->gol_rh_os;            // contoh: "B+" ATAU "B Positif"
                    $pasienNorm  = $this->normalizeGolRh($pasienGolRh);
                }
            }

            // FIX #3: normalisasi kata ("Positif"/"Negatif") <-> simbol ("+"/"-")
            // supaya "B Positif" (sisi pasien) bisa dibandingkan dengan "B"+"+" (sisi kantong)
            $isCompatible = false;
            if ($pasienNorm && $stockGol && $stockRh) {
                $isCompatible = (strtoupper($stockGol) === $pasienNorm['gol'])
                             && ($stockRh === $pasienNorm['rhesus']);
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
     * Normalisasi golongan darah + rhesus.
     * Menerima "B+", "B-", "B Positif", "B Negatif", "B POS", "B NEG", dll.
     */
    private function normalizeGolRh(?string $raw): ?array
    {
        if (!$raw) return null;
        $raw = trim($raw);

        if (preg_match('/^(AB|A|B|O)\s*([+\-]|positif|negatif|pos|neg)?$/i', $raw, $m)) {
            $gol   = strtoupper($m[1]);
            $rhRaw = strtolower($m[2] ?? '');

            $rhesus = match (true) {
                in_array($rhRaw, ['+', 'positif', 'pos']) => '+',
                in_array($rhRaw, ['-', 'negatif', 'neg'])  => '-',
                default => null,
            };

            return ['gol' => $gol, 'rhesus' => $rhesus];
        }

        return null;
    }

    /**
     * Scan Petugas → cari di tabel petugas
     */
    public function scanPetugas(string $keyword): array
    {
        try {
            // FIX #4: bungkus kode/nama dalam closure supaya whereNull('deleted_at')
            // tidak "dibajak" operator precedence OR/AND
            $petugas = DB::table('petugas')
                ->where(function ($q) use ($keyword) {
                    $q->where('kode', $keyword)
                      ->orWhere('nama', 'like', "%{$keyword}%");
                })
                ->whereNull('deleted_at')
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
                    'name' => $petugas->nama,
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

    public function create(array $data): PelayananCrosstestReferal
    {
        return PelayananCrosstestReferal::create($this->mapPayload($data));
    }

    public function update(PelayananCrosstestReferal $pelayanan, array $data): PelayananCrosstestReferal
    {
        $pelayanan->update($this->mapPayload($data, $pelayanan));
        return $pelayanan;
    }

    public function delete(PelayananCrosstestReferal $pelayanan): void
    {
        $pelayanan->delete();
    }

    protected function mapPayload(array $data, ?PelayananCrosstestReferal $existing = null): array
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