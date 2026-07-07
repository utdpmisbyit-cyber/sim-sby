<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use App\Models\PenyisihanKantongAftap;
use App\Models\PenyisihanKantongAftapDetail;
use App\Services\PenyisihanKantongAftapService;
use Illuminate\Http\Request;

class PenyisihanKantongAftapController extends Controller
{
    public function __construct(protected PenyisihanKantongAftapService $service)
    {
    }

    /**
     * Daftar transaksi penyisihan kantong aftap.
     */
    public function index(Request $request)
    {
        $penyisihan = PenyisihanKantongAftap::with('creator')
            ->withCount('details')
            ->when($request->q, function ($query, $q) {
                $query->where('no_transaksi', 'like', "%{$q}%");
            })
            ->when($request->tanggal, function ($query, $tanggal) {
                $query->whereDate('tanggal', $tanggal);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('app.aftap.penyisihan_kantong.index', compact('penyisihan'));
    }

    /**
     * Form tambah transaksi penyisihan baru.
     * View yang sama juga dipakai untuk mode lihat/edit (lihat method edit()).
     */
    public function create()
    {
        $daftarAlasan = PenyisihanKantongAftapService::DAFTAR_ALASAN;
        $penyisihan   = null;

        return view('app.aftap.penyisihan_kantong.form', compact('daftarAlasan', 'penyisihan'));
    }

    /**
     * Cari/scan kantong berdasarkan no_kantong (dipanggil via AJAX dari form scan).
     */
    public function scanKantong(Request $request)
    {
        $request->validate([
            'no_kantong' => 'required|string',
        ]);

        $kantong = $this->service->scanKantong($request->no_kantong);

        return response()->json([
            'success' => true,
            'data'    => $kantong,
        ]);
    }

    /**
     * Simpan transaksi penyisihan baru. Setiap kantong membawa alasan masing-masing.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'             => 'required|date',
            'keterangan'          => 'nullable|string',
            'kantong'             => 'required|array|min:1',
            'kantong.*.id'        => 'required|integer|exists:stok_kantong_penerimaan_detail,id',
            'kantong.*.alasan'    => 'required|string|max:100',
        ]);

        $penyisihan = $this->service->store($data);

        return redirect()
            ->route('aftap.penyisihan_kantong_aftap.edit', $penyisihan->id)
            ->with('success', "Penyisihan {$penyisihan->no_transaksi} berhasil disimpan.");
    }

    /**
     * Alias lama "show" - diarahkan ke halaman edit karena sekarang
     * lihat & edit digabung jadi satu view (create.blade.php).
     */
    public function show(PenyisihanKantongAftap $penyisihan_kantong_aftap)
    {
        return redirect()->route('aftap.penyisihan_kantong_aftap.edit', $penyisihan_kantong_aftap->id);
    }

    /**
     * Lihat & edit transaksi penyisihan yang sudah tersimpan.
     * Menggunakan view yang sama dengan create(); form otomatis jadi
     * mode edit karena variabel $penyisihan tidak null.
     */
    public function edit(PenyisihanKantongAftap $penyisihan_kantong_aftap)
    {
        $penyisihan_kantong_aftap->load('details', 'creator');
        $daftarAlasan = PenyisihanKantongAftapService::DAFTAR_ALASAN;

        return view('app.aftap.penyisihan_kantong.form', [
            'penyisihan'   => $penyisihan_kantong_aftap,
            'daftarAlasan' => $daftarAlasan,
        ]);
    }

    /**
     * Update header transaksi (tanggal, keterangan).
     */
    public function update(Request $request, PenyisihanKantongAftap $penyisihan_kantong_aftap)
    {
        $data = $request->validate([
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $this->service->update($penyisihan_kantong_aftap, $data);

        return redirect()
            ->route('aftap.penyisihan_kantong_aftap.edit', $penyisihan_kantong_aftap->id)
            ->with('success', 'Data penyisihan berhasil diperbarui.');
    }

    /**
     * Tambah satu kantong ke transaksi yang sudah tersimpan (dipakai saat
     * mode edit, scan kantong baru langsung disimpan live via AJAX).
     */
    public function addDetail(Request $request, PenyisihanKantongAftap $penyisihan_kantong_aftap)
    {
        $request->validate([
            'kantong_id' => 'required|integer|exists:stok_kantong_penerimaan_detail,id',
            'alasan'     => 'required|string|max:100',
        ]);

        $detail = $this->service->addDetail(
            $penyisihan_kantong_aftap,
            (int) $request->kantong_id,
            $request->alasan
        );

        return response()->json(['success' => true, 'data' => $detail]);
    }

    /**
     * Ubah alasan satu baris kantong ("Ubah Alasan" pada grid).
     */
    public function updateAlasanDetail(Request $request, PenyisihanKantongAftapDetail $detail)
    {
        $request->validate([
            'alasan' => 'required|string|max:100',
        ]);

        $this->service->updateAlasanDetail($detail, $request->alasan);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus satu baris kantong dari transaksi.
     */
    public function removeDetail(PenyisihanKantongAftapDetail $detail)
    {
        $this->service->removeDetail($detail);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus transaksi penyisihan & kembalikan status kantong ke 'tersedia'.
     */
    public function destroy(PenyisihanKantongAftap $penyisihan_kantong_aftap)
    {
        $this->service->destroy($penyisihan_kantong_aftap);

        return redirect()
            ->route('aftap.penyisihan_kantong_aftap.index')
            ->with('success', 'Transaksi penyisihan berhasil dihapus dan stok dikembalikan.');
    }
}