<?php

namespace App\Http\Controllers\Referal;

use App\Http\Controllers\Controller;
use App\Models\PemberianAwalReferal;
use App\Services\PemberianAwalReferalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PemberianAwalReferalController extends Controller
{
    public function __construct(private PemberianAwalReferalService $service)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['cari', 'status', 'tgl_dari', 'tgl_sampai']);

        return view('app.referal.pemberian_awal.index', [
            'data' => $this->service->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('app.referal.pemberian_awal.form', [
            'pemberian' => new PemberianAwalReferal(),
            'isEdit' => false,
        ]);
    }

    public function edit(PemberianAwalReferal $pemberianAwalReferal)
    {
        return view('app.referal.pemberian_awal.form', [
            'pemberian' => $pemberianAwalReferal,
            'isEdit' => true,
        ]);
    }

    public function store(Request $request)
    {
        $pemberian = $this->service->store($this->validasi($request));

        return redirect()
            ->route('referal.pemberian_awal_referal.edit', $pemberian->id)
            ->with('success', "Pemberian awal {$pemberian->no_pemberian} berhasil disimpan.");
    }

    public function update(Request $request, PemberianAwalReferal $pemberianAwalReferal)
    {
        $this->service->update($pemberianAwalReferal, $this->validasi($request));

        return redirect()
            ->route('referal.pemberian_awal_referal.edit', $pemberianAwalReferal->id)
            ->with('success', 'Perubahan berhasil disimpan.');
    }

    public function destroy(PemberianAwalReferal $pemberianAwalReferal)
    {
        $this->service->delete($pemberianAwalReferal);

        return redirect()
            ->route('referal.pemberian_awal_referal.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    /**
     * AJAX: dipanggil tombol "Cari [F4]" untuk mengisi header form dari data FPUP.
     */
    public function cariFpup(Request $request)
    {
        $request->validate(['no_fpup' => 'required|string']);

        try {
            $data = $this->service->cariFpup($request->no_fpup);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Pencarian FPUP belum terhubung ke model yang benar. Cek PemberianAwalReferalService::cariFpup() dan log aplikasi.',
            ], 500);
        }

        if (! $data) {
            return response()->json(['message' => 'Nomor FPUP tidak ditemukan.'], 404);
        }

        return response()->json($data);
    }

    /**
     * AJAX: cari stok darah yang sesuai untuk tabel "Detail Pemeriksaan Awal".
     */
    public function searchStock(Request $request)
    {
        $request->validate([
            'gol' => 'required|string',
            'rhesus' => 'required|string',
            'jns_darah' => 'nullable|string',
        ]);

        try {
            $stocks = $this->service->searchStock($request->only(['gol', 'rhesus', 'jns_darah']));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Pencarian stok darah belum terhubung ke tabel yang benar. Cek PemberianAwalReferalService::searchStock() dan log aplikasi.',
            ], 500);
        }

        return response()->json($stocks);
    }

    /**
     * AJAX: autocomplete kode/nama barang untuk "Rincian Biaya Lain".
     */
    public function searchBarang(Request $request)
    {
        $request->validate(['q' => 'required|string|min:1']);

        try {
            $items = $this->service->searchBarang($request->q);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Pencarian barang belum terhubung ke model yang benar. Cek PemberianAwalReferalService::searchBarang() dan log aplikasi.',
            ], 500);
        }

        return response()->json($items);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'fpup_id' => 'nullable|integer',
            'no_fpup' => 'nullable|string|max:30',
            'tgl_fpup' => 'nullable|string|max:30',
            'nofpup_dari_cm' => 'nullable|string|max:30',
            'cara_bayar' => ['required', Rule::in(['langsung_tunai', 'kredit'])],
            'identifikasi_antibodi' => 'boolean',
            'pasien_id' => 'nullable|integer',
            'nama_pasien' => 'required|string|max:255',
            'noktp_pasien' => 'nullable|string|max:20',
            'jenis_kelamin' => ['required', Rule::in(['pria', 'wanita'])],
            'alamat_pasien' => 'nullable|string',
            'kode_rs' => 'nullable|string|max:20',
            'nama_rs' => 'nullable|string|max:255',
            'no_reg' => 'nullable|string|max:30',
            'gol_darah' => 'required|string|max:3',
            'rhesus' => ['required', Rule::in(['Positif', 'Negatif'])],
            'pasien_karier' => 'boolean',
            'seleksi' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
            'status' => ['nullable', Rule::in(['draft', 'diproses', 'selesai', 'dibatalkan'])],

            'stocks' => 'nullable|array',
            'stocks.*.nostock' => 'required_with:stocks|string|max:30',
            'stocks.*.jns_darah' => 'required_with:stocks|string|max:10',
            'stocks.*.gol' => 'required_with:stocks|string|max:3',
            'stocks.*.rhesus' => ['required_with:stocks', Rule::in(['Positif', 'Negatif'])],
            'stocks.*.tgl_aftap' => 'nullable',
            'stocks.*.tgl_produksi' => 'nullable',
            'stocks.*.tgl_kadaluarsa' => 'nullable',
            'stocks.*.urutan_seleksi' => 'nullable|integer',

            'biaya_lain' => 'nullable|array',
            'biaya_lain.*.kode' => 'nullable|string|max:20',
            'biaya_lain.*.nama_layanan' => 'required_with:biaya_lain|string|max:255',
            'biaya_lain.*.qty' => 'required_with:biaya_lain|integer|min:1',
            'biaya_lain.*.harga' => 'required_with:biaya_lain|numeric|min:0',
            'biaya_lain.*.satuan' => 'nullable|string|max:20',
        ]);
    }
}