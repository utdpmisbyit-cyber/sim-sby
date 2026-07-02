<?php

namespace App\Http\Controllers\Referal;

use App\Http\Controllers\Controller;
use App\Models\JenisBiaya;
use App\Models\BagianRumahSakit;
use App\Models\Diagnosa;
use App\Models\Petugas;
use App\Models\JenisDarah;
use App\Models\RumahSakit;
use App\Models\KelompokRumahSakit;
use App\Models\TujuanDarah;
use App\Models\KelasTujuanDarah;
use App\Services\PermintaanFpupReferalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;


class PermintaanFpupReferalController extends Controller
{
    public function __construct(
        private readonly PermintaanFpupReferalService $service
    ) {}

    // ─── View Methods (HTML) ──────────────────────────────────────────────────

    /**
     * Tampilkan daftar permintaan referal (view).
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search', 'status', 'status_referal',
            'tgl_dari', 'tgl_sampai', 'jns_permintaan',
        ]);

        $items = $this->service->getAll($filters, $request->integer('per_page', 15));

        // Hitung statistik untuk stats-strip
        $stats = [
            'total'   => $this->service->countAll(),
            'baru'    => $this->service->countByStatus('baru'),
            'proses'  => $this->service->countByStatus('proses'),
            'selesai' => $this->service->countByStatus('selesai'),
        ];

        $options = $this->getFormOptions();

        return view('app.referal.permintaan_fpup.index', compact('items', 'stats', 'options'));
    }

    /**
     * Tampilkan form tambah (view).
     */
    public function create(): View
    {
        $noFpup  = $this->service->generateNoReferalPublic();
        $options = $this->getFormOptions();
        $record  = null;

        return view('app.referal.permintaan_fpup.form', compact('noFpup', 'options', 'record'));
    }

    /**
     * Simpan data baru, redirect ke index.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->service->store($request->all());

            return redirect()
                ->route('referal.permintaan_fpup.index')
                ->with('success', 'Data referal berhasil disimpan.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan detail (view, read-only).
     */
    public function show(int $id): View
    {
        $record   = $this->service->getById($id);
        $noFpup   = $record->no_referal ?? $record->no_fpup;
        $options  = $this->getFormOptions();
        $readOnly = true;

        return view('app.referal.permintaan_fpup.form', compact('record', 'noFpup', 'options', 'readOnly'));
    }

    /**
     * Tampilkan form edit (view).
     */
    public function edit(int $id): View
    {
        $record  = $this->service->getById($id);
        $noFpup  = $record->no_referal ?? $record->no_fpup;
        $options = $this->getFormOptions();

        return view('app.referal.permintaan_fpup.form', compact('record', 'noFpup', 'options'));
    }

    /**
     * Simpan perubahan, redirect ke index.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $this->service->update($id, $request->all());

            return redirect()
                ->route('referal.permintaan_fpup.index')
                ->with('success', 'Data referal berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus data, redirect ke index.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->destroy($id);

            return redirect()
                ->route('referal.permintaan_fpup.index')
                ->with('success', 'Data referal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('referal.permintaan_fpup.index')
                ->with('error', $e->getMessage());
        }
    }

    // ─── Aksi Khusus (JSON API) ───────────────────────────────────────────────

    /**
     * Jadikan pasien sebagai referal (AJAX/API).
     */
    public function jadikanReferal(Request $request, int $fpupId): JsonResponse
    {
        try {
            $extra = $request->only(['alasan_referal', 'alasan_referal_utama']);
            $data  = $this->service->jadikanReferal($fpupId, $extra);

            return response()->json([
                'success' => true,
                'message' => 'Pasien berhasil dijadikan referal.',
                'data'    => $data,
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Update status referal (AJAX/API).
     */
    public function updateStatusReferal(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status_referal' => ['required', 'in:pending,diterima,proses,selesai,ditolak'],
            ]);

            $data = $this->service->updateStatusReferal($id, $request->status_referal);

            return response()->json([
                'success' => true,
                'message' => "Status referal berhasil diubah menjadi [{$request->status_referal}].",
                'data'    => $data,
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

 public function cariRumahSakit(Request $request, string $kode): JsonResponse
{
    $rs = TujuanDarah::where('kode', $kode)
        ->orWhere('nama', 'like', "%{$kode}%")
        ->first();

    if (!$rs) {
        return response()->json(['success' => false], 404);
    }

    return response()->json([
        'success' => true,
        'kode' => $rs->kode,
        'nama' => $rs->nama,
    ]);
}
  public function searchRumahSakit(Request $request)
{
    $keyword = trim($request->keyword);

    $data = TujuanDarah::with('kelompokRumahSakit')
        ->where('kode', 'like', "%{$keyword}%")
        ->orWhere('nama', 'like', "%{$keyword}%")
        ->limit(15)
        ->get();

    return response()->json($data->map(function ($item) {

        return [
            'id' => $item->id,
            'kode' => $item->kode,
            'nama' => $item->nama,
            'jenis_rs' => $item->kelompok_rumah_sakit_id,
        ];

    }));
}
    private function getFormOptions(): array
    {
        return [
            'jenis_biaya' => JenisBiaya::orderBy('nama')->get(),
            'bagian'      => BagianRumahSakit::orderBy('nama')->get(),
            'diagnosa'    => Diagnosa::orderBy('nama')->get(),
            'petugas'     => Petugas::orderBy('nama')->get(),
            'jns_darah'   => JenisDarah::orderBy('nama_pendek')->get(),
            'jenis_rs' => KelompokRumahSakit::orderBy('nama')->get(),
            'kategori_rs' => KelasTujuanDarah::orderBy('nama')->get(),
            'kelas_rawat' => [
                'KLS 1',
                'KLS 2',
                'KLS 3',
                'VIP'
            ],
            'cara_pembayaran' => ['TAGIHAN','TUNAI','BPJS','BAYAR','LANGSUNG'],
            'serologi_hasil' => [
                    'Non Reaktif',
                    'Reaktif',
                ],
                'gol_rh_os' => [
                'A+',
                'A-',
                'B+',
                'B-',
                'AB+',
                'AB-',
                'O+',
                'O-',
            ],
            'status' => ['baru','proses','selesai'],
            'status_referal' => ['pending','diterima','proses','selesai','ditolak'],
            'jns_permintaan' => ['CITO','Biasa','Sewaktu'],
            'gol_darah' => ['A','B','AB','O'],
            'rhesus' => ['+','-'],
        ];
    }

    private function validationErrorResponse(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $e->errors(),
        ], 422);
    }

    private function errorResponse(\Exception $e): JsonResponse
    {
        $code = $e instanceof ModelNotFoundException ? 404 : 500;

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $code);
    }
}