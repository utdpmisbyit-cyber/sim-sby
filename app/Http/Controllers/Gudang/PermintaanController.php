<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\PengajuanBarangService;
use App\Models\Barang;
use App\Models\Petugas;
use App\Models\Cabang;
use App\Models\Stok;
use App\Models\BagianPetugas;
use Illuminate\Http\Request;
use App\Models\PengajuanBarang;    
use App\Models\PengeluaranBarang; 

class PermintaanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PengajuanBarangService();
        $this->viewPrefix = 'app.gudang.pengajuan_barang';
        $this->itemVariable = 'pengajuan';

        // dropdown cabang dari table cabang
        view()->share('cabang_options', Cabang::pluck('nama','id'));

        // dropdown barang dari table barang
        view()->share('barang_options', Barang::pluck('nama','id'));


        view()->share('bagian_options', BagianPetugas::pluck('nama','id'));
        // jenis permintaan
        view()->share('jenis_pengajuan', [
            'medis' => 'Barang Medis',
            'perbaikan' => 'Perbaikan',
            'pembelian' => 'Pembelian',
            'non_medis' => 'Barang Non Medis'
        ]);
    }

    /**
     * Generate kode otomatis
     */
     private function generateKode()
    {
        $prefix = 'REQ-'.date('Ymd');

        $last = \App\Models\PengajuanBarang::whereDate('created_at', date('Y-m-d'))
            ->count();

        $no = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return $prefix.'-'.$no;
    }
     public function proses(Request $request, $id)
    {
        $pengajuan = PengajuanBarang::with(['barang','bagian'])->findOrFail($id);

        if ($pengajuan->status != 0) {
            return response()->json([
                'message' => 'Pengajuan sudah diproses sebelumnya'
            ], 400);
        }

        $barang = $pengajuan->barang;

        if (!$barang) {
            return response()->json([
                'message' => 'Data barang tidak ditemukan'
            ], 404);
        }

        $stok = $barang->stok ?? 0;
        $jml  = $pengajuan->jml_minta ?? 0;

        // 🚨 Validasi stok
        if ($stok < $jml && !$request->paksa) {
            return response()->json([
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        // =========================
        // 1. Kurangi stok barang
        // =========================
        $barang->stok = $stok - $jml;
        $barang->save();
         // =========================
        // 2. Update stok detail (qty_out)
        // =========================
        $stok = Stok::where('barang_id', $barang->id)->first();

        if ($stok) {
            $stok->qty_out = ($stok->qty_out ?? 0) + $jml;
            $stok->save();
        }
        // =========================
        // 2. Simpan ke pengeluaran
        // =========================
         $pengajuan->pengeluaranBarang()->create([
            'no_trans_keluar' => 'OUT-' . time(),
            'tgl_keluar'      => now(),
            'barang_id'       => $pengajuan->barang_id,
            'bagian_id'       => $pengajuan->bagian_id,
            'nama_barang'     => $pengajuan->barang->nama ?? '-',
            'no_lot'              => $stok->no_lot ?? '-',
            'tgl_expired'         => $stok->tgl_expired ?? now(),
            'qty_keluar'      => $pengajuan->jml_minta,
            'satuan'          => $pengajuan->satuan,
            'user_input'      => auth()->id() ?? 0,
            'user_proses'     => auth()->id() ?? 0,
            'stok_id'             => $stok->id ?? null,
        ]);

        // =========================
        // 3. Update status pengajuan
        // =========================
        $pengajuan->update([
            'status'       => 1,
            'user_proses'  => auth()->id() ?? 0,
        ]);

        return response()->json([
            'message' => 'Pengeluaran berhasil diproses'
        ]);
    }
    /**
     * Simpan data
     */
    public function store(Request $request)
    {
        $barang = Barang::find($request->barang_id);

        $petugas = Petugas::where('user_id', auth()->id())->first();
        // dd($petugas);
        if (!$petugas) {
            $petugas = Petugas::first();
        }
        $request->merge([ 
            'kode' => $this->generateKode(),
            'petugas_id' =>  $petugas->id,
            'bagian_id' => $request->bagian_id,
            'user_input'  => $petugas->id,
            'user_proses' => $petugas->id,
            'nama_barang' => $barang->nama ?? '-',
            'satuan' => $barang->satuan ?? null,
            'jml_minta' => $request->jml_minta ?? null,
        ]);
        // dd($request->all());
        return parent::store($request);
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::find($request->barang_id);
        $petugas = Petugas::where('user_id', auth()->id())->first();
         if (!$petugas) {
            $petugas = Petugas::first();
        }
        $request->merge([
             'petugas_id' =>  $petugas->id,
               'bagian_id' => $request->bagian_id,
            'user_input'  => $petugas->id,
            'user_proses' => $petugas->id,
            'nama_barang' => $barang->nama ?? null,
            'satuan' => $barang->satuan ?? null,
            'jml_minta' => $request->jml_minta ?? null,
        ]);

        return parent::update($request, $id);
    }
}