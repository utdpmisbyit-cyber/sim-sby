<?php

namespace App\Http\Controllers\Referal;

use App\Http\Controllers\Controller;
use App\Models\PemberianDarahReferal;
use App\Services\PemberianDarahReferalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PemberianDarahReferalController extends Controller
{
    public function __construct(
        protected PemberianDarahReferalService $service
    ) {}

    public function index(Request $request): View
    {
        $list = $this->service->getAll(
            $request->only(['search', 'tanggal_dari', 'tanggal_sampai', 'status'])
        );

        return view('app.referal.pemberian_darah.index', compact('list'));
    }

    public function create(): View
    {
        $noPemberian = PemberianDarahReferal::generateNoPemberian();

        return view('app.referal.pemberian_darah.form', [
            'noPemberian'    => $noPemberian,
            'pemberianDarah' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        try {
            $this->service->create($validated);

            return redirect()
                ->route('referal.pemberian_darah.index')
                ->with('success', 'Data pemberian darah berhasil disimpan.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(PemberianDarahReferal $pemberianDarah): View
    {
        $pemberianDarah->load('details');

        return view('referal.pemberian_darah.form', [
            'pemberianDarah' => $pemberianDarah,
            'noPemberian'    => $pemberianDarah->no_pemberian,
            'readonly'       => true,
        ]);
    }

    public function edit(PemberianDarahReferal $pemberianDarah): View
    {
        $pemberianDarah->load('details');

        return view('app.referal.pemberian_darah.form', [
            'pemberianDarah' => $pemberianDarah,
            'noPemberian'    => $pemberianDarah->no_pemberian,
            'readonly'       => false,
        ]);
    }

    public function update(Request $request, PemberianDarahReferal $pemberianDarah): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        try {
            $this->service->update($pemberianDarah, $validated);

            return redirect()
                ->route('referal.pemberian_darah.index')
                ->with('success', 'Data pemberian darah berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(PemberianDarahReferal $pemberianDarah): RedirectResponse
    {
        try {
            $this->service->delete($pemberianDarah);

            return redirect()
                ->route('referal.pemberian_darah.index')
                ->with('success', 'Data pemberian darah berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */
    /*  AJAX Scan Endpoints                                               */
    /* ------------------------------------------------------------------ */

    public function scanFpup(Request $request): JsonResponse
    {
        $request->validate(['no_fpup' => 'required|string|max:20']);

        $data = $this->service->scanFpup($request->no_fpup);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No. FPUP tidak ditemukan.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function scanStock(Request $request): JsonResponse
    {
        $request->validate(['nostock' => 'required|string|max:30']);

        $data = $this->service->scanStock($request->nostock);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Nostock tidak ditemukan atau stok habis.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function scanPetugas(Request $request): JsonResponse
    {
        $request->validate(['kode' => 'required|string|max:20']);

        $data = $this->service->scanPetugas($request->kode);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Kode petugas tidak ditemukan.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /* ------------------------------------------------------------------ */
    /*  Private Helpers                                                    */
    /* ------------------------------------------------------------------ */

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            // Header
            'tanggal'                => 'required|date',
            'petugas_kode'           => 'nullable|string|max:20',
            'petugas_nama'           => 'nullable|string|max:100',
            'nama_penerima'          => 'nullable|string|max:100',
            'alamat_penerima'        => 'nullable|string',
            // FPUP
            'no_fpup'                => 'nullable|string|max:20',
            'tgl_fpup'               => 'nullable|date',
            'dokter'                 => 'nullable|string|max:100',
            'kode_rs'                => 'nullable|string|max:20',
            'nama_rs'                => 'nullable|string|max:150',
            'pasien'                 => 'nullable|string|max:100',
            'jenis_rs'               => 'nullable|string|max:30',
            'kelas_rawat'            => 'nullable|string|max:30',
            'gol_darah_pasien'       => 'nullable|string|max:5',
            'rh_pasien'              => 'nullable|string|max:10',
            'kategori'               => 'nullable|string|max:5',
            'utdd_lain'              => 'nullable|string|max:50',
            'jns_biaya'              => 'nullable|string|max:50',
            // Kirim
            'jns_darah_kirim'        => 'nullable|string|max:20',
            'gol_darah_kirim'        => 'nullable|string|max:5',
            'rh_kirim'               => 'nullable|string|max:10',
            'jumlah_kantong'         => 'nullable|integer|min:0',
            'dilayani'               => 'nullable|integer|min:0',
            'kurir_rs'               => 'nullable|string|max:100',
            // Opsi — sesuai checkbox pada tampilan: "Kadaluarsa" & "Pasien Bayi"
            'is_kadaluarsa'          => 'nullable|boolean',
            'is_pasien_bayi'         => 'nullable|boolean',
            // Online
            'no_registrasi_online'   => 'nullable|string|max:50',
            'tgl_registrasi_online'  => 'nullable|date',
            'status'                 => 'nullable|string|in:draft,proses,selesai,batal',
            // Detail stok — metode/hasil/keterangan sekarang per kantong (sesuai tampilan)
            'details'                => 'nullable|array',
            'details.*.nostock'      => 'nullable|string|max:30',
            'details.*.jns_darah'    => 'nullable|string|max:20',
            'details.*.gol'          => 'nullable|string|max:5',
            'details.*.rh'           => 'nullable|string|max:10',
            'details.*.tgl_expired'  => 'nullable|date',
            'details.*.metode'       => 'nullable|string|max:50',
            'details.*.hasil'        => 'nullable|string|max:50',
            'details.*.keterangan'   => 'nullable|string',
        ]);
    }
}