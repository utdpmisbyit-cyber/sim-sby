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
        // Ambil data langsung dari model (tidak pakai ->all())
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
     * Return next available sequence number for today.
     * No kantong CEK 
     */
    public function nextSeq()
    {
        $latest = DB::table('pendataan_kantong')
            ->orderBy('id', 'DESC')
            ->first();

        $nowYY = date('y'); // 26
        $nowMM = date('m'); // 04
        $prefixNow = $nowYY . $nowMM;

        // Jika belum ada data
        if (!$latest || empty($latest->kode)) {
            return response()->json(['next_seq' => 1]);
        }

        $lastCode = $latest->kode; 

        // Validasi panjang
        if (strlen($lastCode) < 8) {
            return response()->json(['next_seq' => 1]);
        }

        $prefixLast = substr($lastCode, 0, 4);
        $seqLast    = (int) substr($lastCode, 4, 4);

        // Jika beda bulan/tahun → reset
        if ($prefixLast != $prefixNow) {
            return response()->json(['next_seq' => 1]);
        }

        // Jika sama → lanjut
        return response()->json(['next_seq' => $seqLast + 1]);
    }

    /**
     * Batch store generated kantong rows.
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

        try {
            DB::table('pendataan_kantong')->insert($rows);
            return response()->json(['success' => true, 'saved' => count($rows)]);
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

        $printerIp   = "192.168.1.200"; // IP printer SATO CL4NX Plus
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