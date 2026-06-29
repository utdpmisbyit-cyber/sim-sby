<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Models\BagianPetugas;
use App\Models\PendataanKantong;
use App\Models\PermintaanCetakUlang;
use App\Services\PermintaanCetakUlangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanCetakUlangController extends IoResourceController
{
    protected $viewPrefix = 'app.gudang.permintaan_cetak_ulang';
    protected $itemVariable = 'permintaan';

    public function __construct()
    {
        $this->service = new PermintaanCetakUlangService();
    }

    public function index()
    {
        $counts = [
            'diajukan'  => PermintaanCetakUlang::where('status', 'diajukan')->count(),
            'disetujui' => PermintaanCetakUlang::where('status', 'disetujui')->count(),
            'ditolak'   => PermintaanCetakUlang::where('status', 'ditolak')->count(),
            'selesai'   => PermintaanCetakUlang::where('status', 'selesai')->count(),
        ];

        return view("{$this->viewPrefix}.index", [
            'counts' => $counts,
        ]);
    }

    public function create()
    {
        return view("{$this->viewPrefix}._form", [
            'bagian_options'  => $this->getBagianOptions(),
            'barcode_options' => $this->getBarcodeOptions(),
        ]);
    }

    public function edit($id)
    {
        $permintaan = $this->service->find($id);

        return view("{$this->viewPrefix}._form", [
            $this->itemVariable => $permintaan,
            'bagian_options'  => $this->getBagianOptions(),
            'barcode_options' => $this->getBarcodeOptions($permintaan->pendataan_kantong_id ?? null),
        ]);
    }

    /**
     * Simpan permintaan baru. Nomor surat di-generate otomatis,
     * status awal selalu 'diajukan'.
     */
    public function store(Request $request)
    {
        $request->merge([
            'no_surat'           => $this->generateNoSurat(),
            'status'             => 'diajukan',
            'user_input'         => Auth::id() ?? 0,
            'user_proses'        => Auth::id() ?? 0,
        ]);

        return parent::store($request);
    }

    /**
     * Update data permintaan. no_surat tidak diubah, hanya field lain.
     * Catatan: perubahan status approve/reject sebaiknya lewat endpoint
     * approve()/reject() di bawah supaya tercatat tgl_disetujui dengan benar.
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'user_proses' => Auth::id() ?? 0,
        ]);

        return parent::update($request, $id);
    }

    /**
     * Setujui permintaan cetak ulang.
     */
    public function approve(Request $request, $id)
    {
        $permintaan = PermintaanCetakUlang::findOrFail($id);

        if ($permintaan->status !== 'diajukan') {
            return response()->json([
                'message' => 'Permintaan ini sudah diproses sebelumnya (status: ' . $permintaan->status . ')'
            ], 400);
        }

        $permintaan->update([
            'status'                => 'disetujui',
            'nama_petugas_melayani' => $request->nama_petugas_melayani ?? auth()->user()->name ?? 'System',
            'nama_kasi'             => $request->nama_kasi,
            'tgl_disetujui'         => now(),
            'catatan'               => $request->catatan,
            'user_proses'           => Auth::id() ?? 0,
        ]);

        return response()->json(['message' => 'Permintaan berhasil disetujui', 'data' => $permintaan]);
    }

    /**
     * Tolak permintaan cetak ulang.
     */
    public function reject(Request $request, $id)
    {
        $permintaan = PermintaanCetakUlang::findOrFail($id);

        if ($permintaan->status !== 'diajukan') {
            return response()->json([
                'message' => 'Permintaan ini sudah diproses sebelumnya (status: ' . $permintaan->status . ')'
            ], 400);
        }

        $request->validate(['catatan' => 'required|string']);

        $permintaan->update([
            'status'                => 'ditolak',
            'nama_petugas_melayani' => $request->nama_petugas_melayani ?? auth()->user()->name ?? 'System',
            'tgl_disetujui'         => now(),
            'catatan'               => $request->catatan,
            'user_proses'           => Auth::id() ?? 0,
        ]);

        return response()->json(['message' => 'Permintaan ditolak', 'data' => $permintaan]);
    }

    /**
     * Tandai permintaan selesai (setelah label benar-benar dicetak ulang
     * lewat modul Cetak Ulang Barcode).
     */
    public function selesai($id)
    {
        $permintaan = PermintaanCetakUlang::findOrFail($id);

        if ($permintaan->status !== 'disetujui') {
            return response()->json([
                'message' => 'Hanya permintaan yang sudah disetujui yang bisa ditandai selesai'
            ], 400);
        }

        $permintaan->update([
            'status'      => 'selesai',
            'user_proses' => Auth::id() ?? 0,
        ]);

        return response()->json(['message' => 'Permintaan ditandai selesai', 'data' => $permintaan]);
    }

    /**
     * Cari barcode (pendataan_kantong) untuk dropdown form, dibatasi 20
     * data terbaru sebagai daftar awal/fallback saja. Pencarian utama
     * (ketika user mengetik) dilayani oleh findBarcode() via select2 ajax,
     * jadi tidak terbatas hanya ke data yang sudah di-load di awal.
     */
    private function getBarcodeOptions($includeId = null)
    {
        $query = PendataanKantong::orderByDesc('id')->limit(20);
        $items = $query->get();

        if ($includeId && !$items->pluck('id')->contains($includeId)) {
            $existing = PendataanKantong::find($includeId);
            if ($existing) $items->push($existing);
        }

        return $items->mapWithKeys(function ($item) {
            $label = $item->kode . ' — ' . $item->merk_kantong . ' ' . $item->jenis_kantong . ' (' . $item->type_kantong . ', ' . $item->ukuran . ')';
            return [$item->id => $label];
        })->toArray();
    }

    /**
     * Endpoint untuk select2 ajax: dipanggil setiap kali user mengetik di
     * kolom "No Barcode (Kode Kantong)". Mencari ke SELURUH tabel
     * pendataan_kantong berdasarkan kode, bukan cuma 20 data awal.
     */
    public function findBarcode(Request $request)
    {
        $q = $request->get('q', $request->get('term', ''));

        $data = PendataanKantong::when($q, function ($query) use ($q) {
                $query->where('kode', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'text' => $item->kode . ' — ' . $item->merk_kantong . ' ' . $item->jenis_kantong . ' (' . $item->type_kantong . ', ' . $item->ukuran . ')',
                ];
            });

        return response()->json($data);
    }

    private function getBagianOptions()
    {
        return BagianPetugas::orderBy('nama')->pluck('nama', 'id')->toArray();
    }

    /**
     * Generate nomor surat format: 0001/UML/{bulan_romawi}/{tahun}
     * Sequence reset setiap tahun.
     */
    private function generateNoSurat(): string
    {
        $tahun = date('Y');
        $bulanRomawi = $this->bulanToRomawi((int) date('n'));

        $count = PermintaanCetakUlang::whereYear('created_at', $tahun)->count();
        $seq = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "{$seq}/UML/{$bulanRomawi}/{$tahun}";
    }

    private function bulanToRomawi(int $bulan): string
    {
        $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romawi[$bulan - 1] ?? 'I';
    }
}