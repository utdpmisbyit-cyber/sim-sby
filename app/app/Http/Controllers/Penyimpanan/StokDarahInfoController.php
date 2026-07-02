<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokDarahInfoController extends Controller
{
    /**
     * Halaman utama informasi stok
     */
    public function index()
    {
        $jenisOptions    = DB::table('stok_darah')->distinct()->orderBy('jenis_darah')->pluck('jenis_darah')->filter()->values();
        $golonganOptions = DB::table('stok_darah')->distinct()->orderBy('golongan_darah')->pluck('golongan_darah')->filter()->values();
        $rhesusOptions   = DB::table('stok_darah')->distinct()->orderBy('rhesus')->pluck('rhesus')->filter()->values();

        return view('app.penyimpanan.stok_darah.index', compact(
            'jenisOptions', 'golonganOptions', 'rhesusOptions'
        ));
    }

    /**
     * Data list stok untuk tabel utama
     */
    public function getData(Request $request): JsonResponse
    {
        $today = Carbon::today();

        $query = DB::table('stok_darah')
            ->whereNull('deleted_at');

        if ($request->jenis_darah)    $query->where('jenis_darah',    $request->jenis_darah);
        if ($request->golongan_darah) $query->where('golongan_darah', $request->golongan_darah);
        if ($request->rhesus)         $query->where('rhesus',         $request->rhesus);
        if ($request->status_stok)    $query->where('status_stok',    $request->status_stok);
        if ($request->no_penerimaan) {
            $query->where(function ($q) use ($request) {
                $q->where('no_stok', 'like', '%' . $request->no_penerimaan . '%')
                  ->orWhere('no_fpd', 'like', '%' . $request->no_penerimaan . '%');
            });
        }

        $rows = $query
            ->orderByRaw("FIELD(status_stok, 'tersedia', 'dipakai', 'kadaluarsa', 'dibuang')")
            ->orderBy('tgl_expired')
            ->get([
                'id', 'no_stok', 'no_kantong', 'no_fpd',
                'jenis_darah', 'golongan_darah', 'rhesus',
                'tgl_aftap', 'tgl_produksi', 'tgl_expired',
                'ruang', 'ml', 'gr', 'skrining', 'status_stok',
                'jumlah_masuk', 'jumlah_keluar', 'jumlah_kembali', 'saldo',
            ]);

        // Format tanggal + hitung status dinamis
        $data = $rows->map(function ($r) use ($today) {
            $tglExpired = $r->tgl_expired ? Carbon::parse($r->tgl_expired) : null;
            $sisaHari   = $tglExpired ? $today->diffInDays($tglExpired, false) : null;

            // Status dinamis berdasarkan tanggal (override jika sudah keluar/dibuang)
            $statusDinamis = $r->status_stok;
            if ($r->status_stok === 'tersedia') {
                if ($sisaHari !== null && $sisaHari < 0)  $statusDinamis = 'expired';
                elseif ($sisaHari !== null && $sisaHari <= 3) $statusDinamis = 'mendekati';
            }

            return [
                'id'             => $r->id,
                'no_stok'        => $r->no_stok,
                'no_kantong'     => $r->no_kantong,
                'no_fpd'         => $r->no_fpd,
                'jenis_darah'    => $r->jenis_darah,
                'golongan_darah' => $r->golongan_darah,
                'rhesus'         => $r->rhesus,
                'tgl_aftap'      => $r->tgl_aftap    ? Carbon::parse($r->tgl_aftap)->format('d/m/Y')    : '-',
                'tgl_produksi'   => $r->tgl_produksi ? Carbon::parse($r->tgl_produksi)->format('d/m/Y') : '-',
                'tgl_expired'    => $r->tgl_expired  ? Carbon::parse($r->tgl_expired)->format('d/m/Y')  : '-',
                'sisa_hari'      => $sisaHari,
                'ruang'          => $r->ruang,
                'ml'             => $r->ml,
                'gr'             => $r->gr,
                'skrining'       => $r->skrining,
                'status'         => $statusDinamis,   // untuk render frontend
                'status_stok'    => $r->status_stok,  // nilai asli DB
                // ── Aliran ──
                'jumlah_masuk'   => $r->jumlah_masuk,
                'jumlah_keluar'  => $r->jumlah_keluar,
                'jumlah_kembali' => $r->jumlah_kembali,
                'saldo'          => $r->saldo,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Ringkasan aliran darah keseluruhan (summary cards atas)
     */
    public function getSummary(): JsonResponse
    {
        $today = Carbon::today();

        $totals = DB::table('stok_darah')
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total_kantong,
                SUM(jumlah_masuk)   as total_masuk,
                SUM(jumlah_keluar)  as total_keluar,
                SUM(jumlah_kembali) as total_kembali,
                SUM(saldo)          as total_saldo,
                SUM(CASE WHEN status_stok = 'tersedia' AND tgl_expired >= ? THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN status_stok = 'tersedia' AND tgl_expired >= ? AND tgl_expired <= ? THEN 1 ELSE 0 END) as mendekati,
                SUM(CASE WHEN status_stok = 'tersedia' AND tgl_expired < ? THEN 1 ELSE 0 END) as expired,
                SUM(CASE WHEN status_stok = 'dipakai' THEN 1 ELSE 0 END) as dipakai
            ", [
                $today->toDateString(),
                $today->toDateString(),
                $today->copy()->addDays(3)->toDateString(),
                $today->toDateString(),
            ])
            ->first();

        // Total transaksi dari tabel transaksi_stok_darah
        $transaksi = DB::table('transaksi_stok_darah')
            ->selectRaw("
                SUM(CASE WHEN jenis = 'masuk'   THEN jumlah ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis = 'keluar'  THEN jumlah ELSE 0 END) as keluar,
                SUM(CASE WHEN jenis = 'kembali' THEN jumlah ELSE 0 END) as kembali,
                COUNT(DISTINCT no_stok) as total_no_stok
            ")
            ->first();

        return response()->json([
            'kantong'        => $totals->total_kantong  ?? 0,
            'total_masuk'    => $totals->total_masuk    ?? 0,
            'total_keluar'   => $totals->total_keluar   ?? 0,
            'total_kembali'  => $totals->total_kembali  ?? 0,
            'total_saldo'    => $totals->total_saldo    ?? 0,
            'tersedia'       => $totals->tersedia        ?? 0,
            'mendekati'      => $totals->mendekati       ?? 0,
            'expired'        => $totals->expired         ?? 0,
            'dipakai'        => $totals->dipakai         ?? 0,
            // dari tabel transaksi
            'trx_masuk'      => $transaksi->masuk        ?? 0,
            'trx_keluar'     => $transaksi->keluar       ?? 0,
            'trx_kembali'    => $transaksi->kembali      ?? 0,
        ]);
    }

    /**
     * Riwayat aliran (transaksi) untuk 1 nomor stok — ditampilkan di modal
     */
    public function getAliran(string $noStok): JsonResponse
    {
        $stok = DB::table('stok_darah')->where('no_stok', $noStok)->first();

        if (!$stok) {
            return response()->json(['success' => false, 'message' => 'Stok tidak ditemukan.'], 404);
        }

        $transaksi = DB::table('transaksi_stok_darah')
            ->where('no_stok', $noStok)
            ->orderByDesc('created_at')
            ->get([
                'id', 'jenis', 'jumlah', 'no_referensi',
                'sumber', 'keterangan', 'created_at',
            ])
            ->map(fn($t) => [
                'id'           => $t->id,
                'jenis'        => $t->jenis,
                'jumlah'       => $t->jumlah,
                'no_referensi' => $t->no_referensi,
                'sumber'       => $t->sumber,
                'keterangan'   => $t->keterangan,
                'tanggal'      => Carbon::parse($t->created_at)->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'stok'    => [
                'no_stok'        => $stok->no_stok,
                'jenis_darah'    => $stok->jenis_darah,
                'golongan_darah' => $stok->golongan_darah,
                'rhesus'         => $stok->rhesus,
                'jumlah_masuk'   => $stok->jumlah_masuk,
                'jumlah_keluar'  => $stok->jumlah_keluar,
                'jumlah_kembali' => $stok->jumlah_kembali,
                'saldo'          => $stok->saldo,
                'status_stok'    => $stok->status_stok,
            ],
            'transaksi' => $transaksi,
        ]);
    }
}