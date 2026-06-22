<?php

namespace App\Services;

use App\Models\PengembalianDarahReferal;
use App\Models\PengembalianDarahReferalDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PengembalianDarahReferalService
{
    // ─── Utility ──────────────────────────────────────────────────────────────

    private function getAllTables(): array
    {
        $rows = DB::select('SHOW TABLES');
        $out  = [];
        foreach ($rows as $row) {
            $row   = (array) $row;
            $out[] = array_values($row)[0];
        }
        return $out;
    }

    private function findTable(array $keywords): ?string
    {
        $all = $this->getAllTables();
        // exact match first
        foreach ($keywords as $kw) {
            if (in_array($kw, $all)) return $kw;
        }
        // partial match
        foreach ($keywords as $kw) {
            foreach ($all as $t) {
                if (str_contains(strtolower($t), strtolower($kw))) return $t;
            }
        }
        return null;
    }

    /**
     * Dari array baris database, ambil nilai kolom pertama yang ditemukan.
     */
    private function pick(array $row, array $candidates): mixed
    {
        foreach ($candidates as $col) {
            if (array_key_exists($col, $row) && $row[$col] !== null && $row[$col] !== '') {
                return $row[$col];
            }
        }
        return null;
    }

    /**
     * Ambil hanya 10 karakter pertama (YYYY-MM-DD) dari nilai datetime.
     */
    private function dateOnly(?string $val): ?string
    {
        if (! $val) return null;
        return substr(str_replace('/', '-', $val), 0, 10);
    }

    // ─── Scan FPUP ────────────────────────────────────────────────────────────

    public function scanFpup(string $noFpup): array
    {
        try {
            $data = [
                'no_fpup'             => $noFpup,
                'tgl_fpup'            => null,
                'no_stock'            => null,
                'kode_rumah_sakit'    => null,
                'nama_rumah_sakit'    => null,
                'tgl_pemberian'       => null,
                'umur_hari_pemberian' => null,
            ];
            $found = false;

            // ══════════════════════════════════════════════════════════════════
            // TAHAP 1 — pelayanan_crosstest
            // Ambil SEMUA kolom → ekstrak di PHP (tidak pakai DB::raw SELECT)
            // ══════════════════════════════════════════════════════════════════
            if (Schema::hasTable('pelayanan_crosstest_referal')) {
                $cols     = Schema::getColumnListing('pelayanan_crosstest_referal');
                $orderCol = in_array('created_at', $cols) ? 'created_at'
                    : (in_array('id', $cols) ? 'id' : $cols[0]);

                $row = DB::table('pelayanan_crosstest_referal')
                    ->where('no_fpup', $noFpup)
                    ->orderByDesc($orderCol)
                    ->first();

                if ($row) {
                    $found = true;
                    $r = (array) $row;

                    $data['tgl_fpup'] = $this->dateOnly(
                        $this->pick($r, ['tgl_fpup','tanggal_fpup','tgl_permintaan','tgl_referal','tgl_order'])
                    );
                    $data['no_stock'] = $this->pick($r, ['no_stock','kode_stock','nomor_stock']);
                    $data['kode_rumah_sakit'] = $this->pick($r, ['kode_rumah_sakit','kode_rs','id_rs','kd_rs']);
                    $data['nama_rumah_sakit'] = $this->pick($r, ['nama_rumah_sakit','nama_rs','rs_name','nama_rs_tujuan','rumah_sakit']);
                    $data['tgl_pemberian']    = $this->dateOnly(
                        $this->pick($r, ['tgl_pemberian','tanggal_pemberian','tgl_transfusi','tgl_serahterima','tgl_distribusi'])
                    );

                    Log::info('[scanFpup] TAHAP1 pelayanan_crosstest_referal', [
                        'no_fpup'          => $noFpup,
                        'tgl_fpup'         => $data['tgl_fpup'],
                        'no_stock'         => $data['no_stock'],
                        'kode_rs'          => $data['kode_rumah_sakit'],
                        'nama_rs'          => $data['nama_rumah_sakit'],
                        'tgl_pemberian'    => $data['tgl_pemberian'],
                        'raw_tgl_fpup'     => $r['tgl_fpup'] ?? '(not found)',
                    ]);
                }
            }

            // ══════════════════════════════════════════════════════════════════
            // TAHAP 2 — pemberian_darah_referal (lengkapi data yang null)
            // ══════════════════════════════════════════════════════════════════
            $tabelPemberian = $this->findTable([
                'pemberian_darah_referal',
                'pemberian_darah',
                'pemberian_referal',
                'pemberian',
            ]);

            if ($tabelPemberian) {
                $cols2    = Schema::getColumnListing($tabelPemberian);
                $fpupCol  = null;
                foreach (['no_fpup','fpup','nomor_fpup','kode_fpup'] as $c) {
                    if (in_array($c, $cols2)) { $fpupCol = $c; break; }
                }

                if ($fpupCol) {
                    $orderCol2 = in_array('created_at', $cols2) ? 'created_at'
                        : (in_array('id', $cols2) ? 'id' : $cols2[0]);

                    $row2 = DB::table($tabelPemberian)
                        ->where($fpupCol, $noFpup)
                        ->orderByDesc($orderCol2)
                        ->first();

                    if ($row2) {
                        $found = true;
                        $r2    = (array) $row2;

                        // Isi hanya kolom yang masih null dari TAHAP 1
                        if (! $data['tgl_fpup']) {
                            $data['tgl_fpup'] = $this->dateOnly(
                                $this->pick($r2, ['tgl_fpup','tanggal_fpup'])
                            );
                        }
                        if (! $data['no_stock']) {
                            $data['no_stock'] = $this->pick($r2, ['no_stock','kode_stock','nomor_stock','no_kantong']);
                        }
                        if (! $data['kode_rumah_sakit']) {
                            $data['kode_rumah_sakit'] = $this->pick($r2, ['kode_rumah_sakit','kode_rs','id_rs','kd_rs']);
                        }
                        if (! $data['nama_rumah_sakit']) {
                            $data['nama_rumah_sakit'] = $this->pick($r2, ['nama_rumah_sakit','nama_rs','rs_name','nama_rs_tujuan']);
                        }
                        if (! $data['tgl_pemberian']) {
                            // Coba banyak variasi nama, fallback ke created_at
                            $tglPbr = $this->pick($r2, [
                                'tgl_pemberian','tanggal_pemberian',
                                'tgl_transfusi','tanggal_transfusi',
                                'tgl_serahterima','tgl_distribusi',
                                'tgl_keluar','tgl_release',
                            ]);
                            if (! $tglPbr && isset($r2['created_at'])) {
                                $tglPbr = $r2['created_at']; // fallback
                            }
                            $data['tgl_pemberian'] = $this->dateOnly($tglPbr);
                        }

                        Log::info('[scanFpup] TAHAP2 ' . $tabelPemberian, [
                            'no_fpup'       => $noFpup,
                            'tgl_pemberian' => $data['tgl_pemberian'],
                            'no_stock'      => $data['no_stock'],
                        ]);
                    }
                }
            }

            // ══════════════════════════════════════════════════════════════════
            // TAHAP 3 — Hitung umur_hari_pemberian
            // ══════════════════════════════════════════════════════════════════
            if ($data['tgl_pemberian']) {
                try {
                    $data['umur_hari_pemberian'] = (int) Carbon::parse($data['tgl_pemberian'])
                        ->startOfDay()
                        ->diffInDays(Carbon::today());
                } catch (\Exception $e) {
                    $data['umur_hari_pemberian'] = null;
                }
            }

            // ══════════════════════════════════════════════════════════════════
            // TAHAP 4 — Cek apakah FPUP sudah pernah dikembalikan sebelumnya
            // ══════════════════════════════════════════════════════════════════
            if ($found) {
                $data['warning']         = false;
                $data['warning_message'] = null;

                if (Schema::hasTable('pengembalian_darah_referal')) {
                    $existingList = DB::table('pengembalian_darah_referal')
                        ->where('no_fpup', $noFpup)
                        ->whereNull('deleted_at')
                        ->orderByDesc('tanggal_kembali')
                        ->select('nomor_kembali', 'tanggal_kembali', 'status_kembali')
                        ->get();

                    if ($existingList->count() > 0) {
                        $last = $existingList->first();
                        $tgl  = $last->tanggal_kembali
                            ? substr($last->tanggal_kembali, 0, 10)
                            : '-';
                        $data['warning']         = true;
                        $data['warning_message'] =
                            "No. FPUP <strong>{$noFpup}</strong> sudah pernah dikembalikan "
                            . $existingList->count() . " kali. "
                            . "Terakhir: <strong>{$last->nomor_kembali}</strong> "
                            . "({$tgl}, {$last->status_kembali}).";
                        $data['existing_count']  = $existingList->count();
                    }
                }

                return ['found' => true, 'data' => $data];
            }

            return [
                'found'   => false,
                'message' => "No FPUP '{$noFpup}' tidak ditemukan.",
            ];

        } catch (\Throwable $e) {
            Log::error('[scanFpup] error: ' . $e->getMessage(), [
                'no_fpup' => $noFpup,
                'trace'   => $e->getTraceAsString(),
            ]);
            return [
                'found'   => false,
                'message' => 'Gagal mengambil data FPUP: ' . $e->getMessage(),
            ];
        }
    }

    // ─── Scan Stock ───────────────────────────────────────────────────────────

    public function scanStock(string $noStock): array
    {
        try {
            $tabel = $this->findTable(['stock_darah','stok_darah','stock','stok','kantong','darah']);

            if (! $tabel) {
                return [
                    'found'   => false,
                    'message' => 'Tabel stock darah tidak ditemukan. Tabel: ' . implode(', ', $this->getAllTables()),
                ];
            }

            $cols     = Schema::getColumnListing($tabel);
            $kolStock = null;
            foreach (['no_stock','kode_stock','nomor_stock','no_kantong','kode'] as $k) {
                if (in_array($k, $cols)) { $kolStock = $k; break; }
            }

            if (! $kolStock) {
                return [
                    'found'   => false,
                    'message' => "Tabel '{$tabel}' tidak punya kolom no_stock. Kolom: " . implode(', ', $cols),
                ];
            }

            $row = DB::table($tabel)->where($kolStock, $noStock)->first();

            if (! $row) {
                return [
                    'found'   => false,
                    'message' => "No Stock '{$noStock}' tidak ditemukan di tabel '{$tabel}'.",
                    'debug'   => ['tabel' => $tabel, 'kolom_key' => $kolStock, 'kolom' => $cols],
                ];
            }

            $r = (array) $row;

            // Log semua kolom agar mudah debug jika ada yang kosong
            Log::info('[scanStock] tabel=' . $tabel . ' kolom: ' . implode(', ', array_keys($r)));

            $kadaluarsa = $this->dateOnly($this->pick($r, [
                'kadaluarsa',
                'tgl_kadaluarsa',
                'exp_date',
                'expired',
                'tgl_exp',
                'expiry',
                'expiry_date',
                'tgl_expire',
                'tgl_habis',
                'tgl_kadalwarsa',         
                'tanggal_kadaluarsa',
            ]));

            $sts = $this->pick($r, [
                'sts',
                'status',
                'status_darah',
                'status_stock',
                'kode_status',
                'ket_sts',
                'keterangan_sts',
                'stat',
                'kondisi',
                'ket',
            ]);

            $stockData = [
                'no_stock'   => $noStock,
                'jenis_darah'=> $this->pick($r, ['jenis_darah','komponen','komponen_darah','jns_darah','jenis','nama_darah']),
                'gol_darah'  => $this->pick($r, ['gol_darah','golongan_darah','golongan','gol','gol_drh']),
                'rhesus'     => $this->pick($r, ['rhesus','rh','rhesus_darah','rh_factor']),
                'sts'        => $sts,
                'tgl_aftap'  => $this->dateOnly($this->pick($r, ['tgl_aftap','tanggal_aftap','tgl_ambil','tgl_pengambilan','tgl_donor','tanggal_donor'])),
                'kadaluarsa' => $kadaluarsa,
                'warning'         => false,
                'warning_message' => null,
            ];

            // ── Cek apakah no_stock sudah pernah dikembalikan ────────────────
            if (Schema::hasTable('pengembalian_darah_referal_detail')
                && Schema::hasTable('pengembalian_darah_referal')) {

                $existingReturn = DB::table('pengembalian_darah_referal_detail as d')
                    ->join('pengembalian_darah_referal as p', 'p.id', '=', 'd.pengembalian_id')
                    ->where('d.no_stock', $noStock)
                    ->whereNull('p.deleted_at')
                    ->orderByDesc('p.tanggal_kembali')
                    ->select('p.nomor_kembali', 'p.tanggal_kembali', 'p.no_fpup')
                    ->get();

                if ($existingReturn->count() > 0) {
                    $last = $existingReturn->first();
                    $tgl  = $last->tanggal_kembali
                        ? substr($last->tanggal_kembali, 0, 10)
                        : '-';
                    $stockData['warning']         = true;
                    $stockData['warning_message'] =
                        "No. Stock <strong>{$noStock}</strong> sudah pernah dikembalikan "
                        . $existingReturn->count() . " kali. "
                        . "Terakhir pada: <strong>{$tgl}</strong> "
                        . "(No. Kembali: {$last->nomor_kembali}).";
                    $stockData['existing_count']  = $existingReturn->count();
                }
            }

            return ['found' => true, 'data' => $stockData];

        } catch (\Throwable $e) {
            Log::error('[scanStock] error: ' . $e->getMessage(), ['no_stock' => $noStock]);
            return ['found' => false, 'message' => 'Gagal mengambil data stock: ' . $e->getMessage()];
        }
    }

    // ─── Scan Petugas ─────────────────────────────────────────────────────────

    public function scanPetugas(string $kodePetugas): array
    {
        try {
            $tabel = $this->findTable(['petugas','users','karyawan','pegawai','user']);

            if (! $tabel) {
                return ['found' => false, 'message' => 'Tabel petugas tidak ditemukan.'];
            }

            $cols    = Schema::getColumnListing($tabel);
            $kolKode = null;
            foreach (['kode','kode_petugas','nik','nip','username','id_petugas'] as $k) {
                if (in_array($k, $cols)) { $kolKode = $k; break; }
            }

            if (! $kolKode) {
                return ['found' => false, 'message' => "Tabel '{$tabel}': kolom kode tidak ditemukan."];
            }

            $row = DB::table($tabel)->where($kolKode, $kodePetugas)->first();

            if (! $row) {
                return ['found' => false, 'message' => "Kode petugas '{$kodePetugas}' tidak ditemukan."];
            }

            $r = (array) $row;

            return ['found' => true, 'data' => [
                'kode' => $kodePetugas,
                'nama' => $this->pick($r, ['nama','name','nama_petugas','nama_lengkap','full_name']) ?? $kodePetugas,
            ]];

        } catch (\Throwable $e) {
            Log::error('[scanPetugas] error: ' . $e->getMessage(), ['kode' => $kodePetugas]);
            return ['found' => false, 'message' => 'Gagal mengambil data petugas: ' . $e->getMessage()];
        }
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function store(array $data): PengembalianDarahReferal
    {
        return DB::transaction(function () use ($data) {
            $data['nomor_kembali'] = PengembalianDarahReferal::generateNomor();

            $pengembalian = PengembalianDarahReferal::create([
                'nomor_kembali'        => $data['nomor_kembali'],
                'tanggal_kembali'      => $data['tanggal_kembali'],
                'kode_petugas'         => $data['kode_petugas']        ?? null,
                'nama_petugas'         => $data['nama_petugas']        ?? null,
                'no_fpup'              => $data['no_fpup']             ?? null,
                'tgl_fpup'             => $data['tgl_fpup']            ?? null,
                'no_stock'             => $data['no_stock']            ?? null,
                'kode_rumah_sakit'     => $data['kode_rumah_sakit']   ?? null,
                'nama_rumah_sakit'     => $data['nama_rumah_sakit']   ?? null,
                'alasan_kembali'       => $data['alasan_kembali']     ?? null,
                'status_kembali'       => $data['status_kembali']     ?? 'Baik',
                'tgl_pemberian'        => $data['tgl_pemberian']      ?? null,
                'umur_hari_pemberian'  => $data['umur_hari_pemberian'] ?? null,
                'yang_mengembalikan'   => $data['yang_mengembalikan']  ?? null,
                'keterangan'           => $data['keterangan']         ?? null,
            ]);

            if (! empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $pengembalian->details()->create([
                        'no_stock'       => $detail['no_stock'],
                        'jenis_darah'    => $detail['jenis_darah']    ?? null,
                        'gol_darah'      => $detail['gol_darah']      ?? null,
                        'rhesus'         => $detail['rhesus']          ?? null,
                        'sts'            => $detail['sts']             ?? null,
                        'status_kembali' => $detail['status_kembali'] ?? 'Baik',
                        'alasan_kembali' => $detail['alasan_kembali'] ?? null,
                        'tgl_aftap'      => $detail['tgl_aftap']      ?? null,
                        'kadaluarsa'     => $detail['kadaluarsa']     ?? null,
                        'jumlah'         => $detail['jumlah']         ?? 1,
                        'keterangan'     => $detail['keterangan']     ?? null,
                    ]);
                }
            }

            return $pengembalian->load('details');
        });
    }

    public function update(PengembalianDarahReferal $pengembalian, array $data): PengembalianDarahReferal
    {
        return DB::transaction(function () use ($pengembalian, $data) {
            $pengembalian->update([
                'tanggal_kembali'      => $data['tanggal_kembali'],
                'kode_petugas'         => $data['kode_petugas']        ?? $pengembalian->kode_petugas,
                'nama_petugas'         => $data['nama_petugas']        ?? $pengembalian->nama_petugas,
                'no_fpup'              => $data['no_fpup']             ?? $pengembalian->no_fpup,
                'tgl_fpup'             => $data['tgl_fpup']            ?? $pengembalian->tgl_fpup,
                'no_stock'             => $data['no_stock']            ?? $pengembalian->no_stock,
                'kode_rumah_sakit'     => $data['kode_rumah_sakit']   ?? $pengembalian->kode_rumah_sakit,
                'nama_rumah_sakit'     => $data['nama_rumah_sakit']   ?? $pengembalian->nama_rumah_sakit,
                'alasan_kembali'       => $data['alasan_kembali']     ?? $pengembalian->alasan_kembali,
                'status_kembali'       => $data['status_kembali']     ?? $pengembalian->status_kembali,
                'tgl_pemberian'        => $data['tgl_pemberian']      ?? $pengembalian->tgl_pemberian,
                'umur_hari_pemberian'  => $data['umur_hari_pemberian'] ?? $pengembalian->umur_hari_pemberian,
                'yang_mengembalikan'   => $data['yang_mengembalikan']  ?? $pengembalian->yang_mengembalikan,
                'keterangan'           => $data['keterangan']         ?? $pengembalian->keterangan,
            ]);

            if (isset($data['details']) && is_array($data['details'])) {
                $pengembalian->details()->delete();
                foreach ($data['details'] as $detail) {
                    $pengembalian->details()->create([
                        'no_stock'       => $detail['no_stock'],
                        'jenis_darah'    => $detail['jenis_darah']    ?? null,
                        'gol_darah'      => $detail['gol_darah']      ?? null,
                        'rhesus'         => $detail['rhesus']          ?? null,
                        'sts'            => $detail['sts']             ?? null,
                        'status_kembali' => $detail['status_kembali'] ?? 'Baik',
                        'alasan_kembali' => $detail['alasan_kembali'] ?? null,
                        'tgl_aftap'      => $detail['tgl_aftap']      ?? null,
                        'kadaluarsa'     => $detail['kadaluarsa']     ?? null,
                        'jumlah'         => $detail['jumlah']         ?? 1,
                        'keterangan'     => $detail['keterangan']     ?? null,
                    ]);
                }
            }

            return $pengembalian->load('details');
        });
    }

    public function destroy(PengembalianDarahReferal $pengembalian): bool
    {
        return DB::transaction(function () use ($pengembalian) {
            $pengembalian->details()->delete();
            return (bool) $pengembalian->delete();
        });
    }

    public function getList(array $filters = [], int $perPage = 15)
    {
        $query = PengembalianDarahReferal::with('details')->orderByDesc('tanggal_kembali');

        if (! empty($filters['search']))         $query->search($filters['search']);
        if (! empty($filters['tanggal_dari']))   $query->whereDate('tanggal_kembali', '>=', $filters['tanggal_dari']);
        if (! empty($filters['tanggal_sampai'])) $query->whereDate('tanggal_kembali', '<=', $filters['tanggal_sampai']);
        if (! empty($filters['status_kembali'])) $query->where('status_kembali', $filters['status_kembali']);

        return $query->paginate($perPage)->withQueryString();
    }
}