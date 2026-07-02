<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\PendataanKantongService;
use App\Models\PendataanKantong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendataanKantongController extends IoResourceController
{
    protected PendataanKantongService $kantongService;

    public function __construct()
    {
        $this->service        = new PendataanKantongService();
        $this->kantongService = $this->service;
        $this->viewPrefix     = 'app.gudang.pendataan_kantong';
        $this->itemVariable   = 'pendataan_kantong';
    }


    public function index()
    {
        $data = PendataanKantong::latest()->get();

        return view("{$this->viewPrefix}.index", [
            $this->itemVariable => $data,
            'merk_kantong'      => $this->kantongService->merk_kantong,
            'jenis_kantong'     => $this->kantongService->jenis_kantong,
            'type_kantong'      => $this->kantongService->type_kantong,
            'ukuran_kantong'    => $this->kantongService->ukuran_kantong,
            'jenis_type_map'    => PendataanKantong::JENIS_TYPE_MAP,
        ]);
    }

    /**
     * Return next available sequence number for the current month (yyMM).
     */
    public function nextSeq()
    {
        $prefixNow = date('y') . date('m');

        $lastKode = DB::table('pendataan_kantong')
            ->where('kode', 'like', $prefixNow . '%')
            ->orderByDesc('kode')
            ->value('kode');

        if (!$lastKode || strlen($lastKode) < 8) {
            return response()->json(['next_seq' => 1]);
        }

        $seqLast = (int) substr($lastKode, 4, 4);

        return response()->json(['next_seq' => $seqLast + 1]);
    }

    /**
     * BARU: Cek apakah ada no_kantong dari batch FE yang ternyata SUDAH
     * PERNAH dipakai (termasuk yang soft-deleted). Dipakai FE untuk
     * menampilkan warning di tabel preview SEBELUM user klik Simpan --
     * supaya konflik kelihatan lebih awal, bukan baru ketahuan lewat
     * pesan error mentah setelah storeBatch ditolak.
     *
     * Ini BUKAN pengganti validasi di storeBatch -- hanya early warning
     * di UI. storeBatch tetap wajib validasi ulang karena hasil endpoint
     * ini bisa basi kalau ada tab/user lain insert di antara waktu cek
     * dan waktu klik Simpan.
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'kodes'   => 'required|array|min:1',
            'kodes.*' => 'string',
        ]);

        $bentrok = DB::table('pendataan_kantong')
            ->whereIn('kode', $request->kodes)
            ->pluck('kode');

        return response()->json([
            'bentrok' => $bentrok->values(),
        ]);
    }

    /**
     * Batch store generated kantong rows.
     *
     * FIX: cek bentrok + insert sekarang dibungkus DB::transaction dengan
     * lockForUpdate. Sebelumnya, cek whereIn dan insert adalah dua query
     * terpisah tanpa lock -- kalau dua request storeBatch (dari dua tab/
     * user) jalan hampir bersamaan dengan kode yang sama, KEDUANYA bisa
     * lolos cek whereIn (karena belum ada yang ke-insert saat masing-
     * masing cek), lalu keduanya coba insert -> baru ketahuan bentrok
     * lewat error SQL unique constraint (kalau ada) atau malah berhasil
     * dobel (kalau kolom 'kode' TIDAK unique di DB).
     *
     * PENTING: locking ini baru benar-benar efektif kalau kolom 'kode'
     * punya UNIQUE INDEX di database. Tanpa unique index, lockForUpdate
     * pada baris yang BELUM ADA tidak menjamin apa-apa (tidak ada baris
     * untuk di-lock). Jadi WAJIB tambahkan migration:
     *
     *   Schema::table('pendataan_kantong', function (Blueprint $table) {
     *       $table->unique('kode');
     *       $table->unique('barcode');
     *   });
     *
     * Dengan unique index, kalaupun race condition tetap lolos sampai
     * insert, DB sendiri yang akan menolak salah satu insert (exception
     * QueryException 23000) -- jadi data TIDAK PERNAH dobel di tabel,
     * apapun yang terjadi di level aplikasi.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'rows'                  => 'required|array|min:1',
            'rows.*.merk'           => 'nullable|string',
            'rows.*.jenis'          => 'required|string',
            'rows.*.type'           => 'required|string',
            'rows.*.vol'            => 'required|string',
            'rows.*.no_lot'         => 'required|string',
            'rows.*.no_kantong'     => 'required|string',
            'rows.*.duplikat'       => 'required|integer|min:1',
        ]);

        $now  = now();
        $rows = collect($request->rows)->map(fn($r) => [
            'kode'          => $r['no_kantong'],
            'barcode'       => $r['no_kantong'],
            'merk_kantong'  => $r['merk'] ?? null,
            'jenis_kantong' => $r['jenis'],
            'type_kantong'  => $r['type'],
            'ukuran'        => $r['vol'],
            'no_lot'        => $r['no_lot'],
            'duplikat'      => $r['duplikat'],
            'status'        => 'aktif',
            'created_at'    => $now,
            'updated_at'    => $now,
        ])->toArray();

        $kodeBaru = array_column($rows, 'kode');

        try {
            DB::transaction(function () use ($rows, $kodeBaru) {
                // Lock baris yang kodenya match, supaya storeBatch lain yang
                // jalan bersamaan menunggu transaction ini selesai dulu
                // sebelum ikut mengecek -- mengecilkan window race condition.
                $existing = DB::table('pendataan_kantong')
                    ->whereIn('kode', $kodeBaru)
                    ->lockForUpdate()
                    ->pluck('kode');

                if ($existing->isNotEmpty()) {
                    throw new \RuntimeException(
                        'DUPLIKAT::' . $existing->implode(', ')
                    );
                }

                DB::table('pendataan_kantong')->insert($rows);
            });

            return response()->json(['success' => true, 'saved' => count($rows)]);

        } catch (\RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'DUPLIKAT::')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kantong berikut sudah pernah dipakai, silakan Run ulang: '
                        . substr($e->getMessage(), strlen('DUPLIKAT::')),
                ], 422);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);

        } catch (\Illuminate\Database\QueryException $e) {
            // Tertangkap di sini kalau race condition lolos lockForUpdate
            // tapi DITOLAK oleh unique constraint di level database
            // (error code 23000). Ini adalah pertahanan TERAKHIR.
            return response()->json([
                'success' => false,
                'message' => 'Sebagian nomor kantong bentrok saat menyimpan (kemungkinan disimpan bersamaan oleh user lain). Silakan Run ulang.',
            ], 422);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PRINT SATO LANGSUNG.
     */
    public function printDirect(Request $r)
    {
        $rows = $r->rows;

        $printerIp   = "192.168.1.200";
        $printerPort = 9100;

        $socket = fsockopen($printerIp, $printerPort, $errno, $errstr, 5);

        if (!$socket) {
            return response()->json([
                'success' => false,
                'message' => "Printer tidak terhubung: $errstr"
            ]);
        }

        foreach ($rows as $row) {

            $zpl = "
                <STX>A<ETX>
                <STX>H1<ETX>
                <STX>V10<ETX>
                <STX>L0202<ETX>
                <STX>BG020200" . $row['no_kantong'] . "<ETX>
                <STX>H4<ETX>
                <STX>V140<ETX>
                <STX>L0201<ETX>
                <STX>W" . $row['no_kantong'] . "<ETX>
                <STX>Z<ETX>
                ";

            fwrite($socket, $zpl);
        }

        fclose($socket);

        return response()->json([
            'success' => true,
            'sent'    => count($rows)
        ]);
    }
}