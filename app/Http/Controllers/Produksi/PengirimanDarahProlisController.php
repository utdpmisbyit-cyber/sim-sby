<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Services\PengirimanDarahProlisService;
use App\Models\PengirimanSampleDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\JenisDarah;
use Illuminate\Support\Facades\Log;

class PengirimanDarahProlisController extends Controller
{
    public function __construct(
        protected PengirimanDarahProlisService $service
    ) {}

    // ── Pages ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $filters  = $request->only([
            'keyword', 'no_kantong', 'no_stok', 'data_barcode',
            'jenis_darah', 'golongan_darah', 'status',
            'tgl_dari', 'tgl_sampai',
        ]);

        $data     = $this->service->list($filters);
        $summary  = $this->service->summary($filters);
        $optionsJenis = $this->service->optionsJenis();

        return view('app.produksi.pengiriman_darah_prolis.index', compact(
            'data', 'filters', 'summary', 'optionsJenis'
        ));
    }

    public function create(): View
    {
        $record          = null;
        $optionsJenis    = $this->service->optionsJenis();
        $optionsGolongan = $this->service->optionsGolongan();
        $noPengiriman    = $this->service->generateNoPengiriman();
        
        // Ambil data jenis darah dari tabel jenis_darah column nama_pendek
        try {
            $jenisDarahList = JenisDarah::whereNull('deleted_at')
                ->orderBy('nama_pendek')
                ->pluck('nama_pendek', 'id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading jenis_darah: ' . $e->getMessage());
            $jenisDarahList = [];
        }

        return view('app.produksi.pengiriman_darah_prolis.form', compact(
            'record','optionsJenis', 'optionsGolongan', 'noPengiriman', 'jenisDarahList'
        ));
    }

   

    public function show(int $id): View
    {
        $record = $this->service->find($id);

        return view('app.produksi.pengiriman_darah_prolis.show', compact('record'));
    }

    public function edit(int $id): View
    {
        $record          = $this->service->find($id);
        $optionsJenis    = $this->service->optionsJenis();
        $optionsGolongan = $this->service->optionsGolongan();
        
        // Ambil data jenis darah dari tabel jenis_darah column nama_pendek
        try {
            $jenisDarahList = JenisDarah::whereNull('deleted_at')
                ->orderBy('nama_pendek')
                ->pluck('nama_pendek', 'id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading jenis_darah: ' . $e->getMessage());
            $jenisDarahList = [];
        }

        return view('app.produksi.pengiriman_darah_prolis.form', compact(
            'record', 'optionsJenis', 'optionsGolongan', 'jenisDarahList'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->rules());

            $record = $this->service->store($validated);

            if ($record) {
                // Cek apakah request AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Data {$record->no_pengiriman} berhasil disimpan.",
                        'data' => $record,
                        'redirect' => route('produksi.pengiriman_darah_prolis.index')
                    ]);
                }
                
                return redirect()
                    ->route('produksi.pengiriman_darah_prolis.index')
                    ->with('success', "Data {$record->no_pengiriman} berhasil disimpan.");
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan data. Silakan coba lagi.'
                    ], 500);
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Store error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate($this->rules($id));

            $record = $this->service->update($id, $validated);

            if ($record) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Data {$record->no_pengiriman} berhasil diperbarui.",
                        'data' => $record,
                        'redirect' => route('produksi.pengiriman_darah_prolis.index')
                    ]);
                }
                
                return redirect()
                    ->route('produksi.pengiriman_darah_prolis.index')
                    ->with('success', "Data {$record->no_pengiriman} berhasil diperbarui.");
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memperbarui data.'
                    ], 500);
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Gagal memperbarui data.');
            }
            
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $record = $this->service->find($id);
        $this->service->delete($id);

        return redirect()
            ->route('produksi.pengiriman_darah_prolis.index')
            ->with('success', "Data {$record->no_pengiriman} berhasil dihapus.");
    }

    // ── AJAX / JSON ───────────────────────────────────────────────────────────

    public function dataJson(Request $request): JsonResponse
    {
        $filters = $request->only([
            'keyword', 'no_kantong', 'no_stok', 'data_barcode',
            'jenis_darah', 'golongan_darah', 'status',
            'tgl_dari', 'tgl_sampai',
        ]);

        $data = $this->service->list($filters, $request->integer('per_page', 20));

        return response()->json($data);
    }

    public function showJson(int $id): JsonResponse
    {
        return response()->json($this->service->find($id));
    }
    
    // API untuk ambil data jenis darah
    public function getJenisDarahJson(): JsonResponse
    {
        try {
            $jenisDarah = JenisDarah::whereNull('deleted_at')
                ->select('id', 'kode', 'nama', 'nama_pendek', 'umur_darah')
                ->orderBy('nama_pendek')
                ->get();
            return response()->json(['success' => true, 'data' => $jenisDarah]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function rules(?int $id = null): array
    {
        return [
            'no_pengiriman'  => 'nullable|string|max:20',
            'tgl_pengiriman' => 'required|date',
            'no_stok'        => 'nullable|string|max:100',
            'no_kantong'     => 'nullable|string|max:100',
            'jenis_darah'    => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|string|max:50',
            'rhesus'         => 'nullable|string|max:50',
            'tgl_aftap'      => 'required|date',
            'tgl_produksi'   => 'required|date',
            'tgl_expired'    => 'required|date',
            'nama_asal_darah'=> 'nullable|string|max:255',
            'suhu'           => 'nullable|numeric|min:-50|max:50',
            'status'         => 'nullable|string|max:50',
            'gr'             => 'nullable|string|max:100',
            'ml'             => 'nullable|string|max:100',
            'jumlah'         => 'nullable|string|max:50',
            'skrining'       => 'nullable|string|max:150',
            'keterangan'     => 'nullable|string',
            'no_fpd'         => 'nullable|string|max:50',
            'asal_darah_id'  => 'nullable|integer',
            'petugas_id'     => 'nullable|integer',
            'suhu'           => 'nullable|numeric|min:-50|max:50',
        ];
    }
    
   public function scanKantong(Request $request): JsonResponse
{
    try {
        $noKantong = $request->input('no_kantong');
        $noStok    = $request->input('no_stok');

        if (!$noKantong && !$noStok) {
            return response()->json([
                'found' => false, 
                'message' => 'Input kosong.'
            ], 422);
        }

        $result = $this->service->findByKantong($noKantong, $noStok);

        if (!$result) {
            return response()->json([
                'found' => false, 
                'message' => "Data dengan no_kantong '{$noKantong}' tidak ditemukan."
            ], 404);
        }

        return response()->json([
            'found' => true, 
            'data' => $result
        ]);
        
    } catch (\Exception $e) {
        Log::error('Scan error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'found' => false,
            'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
        ], 500);
    }
}
public function searchPetugas(Request $request): JsonResponse
{
    try {
        $keyword = trim($request->input('q', ''));

        $rows = \DB::table('petugas')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('kode', 'like', "%{$keyword}%");
            })
            ->select('id', 'kode', 'nama')
            ->orderBy('nama')
            ->limit(10)
            ->get();

        $results = $rows->map(fn($p) => [
            'id'   => $p->id,
            'text' => "{$p->nama} ({$p->kode})",
            'name' => $p->nama,
            'kode' => $p->kode,
        ]);

        return response()->json(['results' => $results]);

    } catch (\Exception $e) {
        Log::error('searchPetugas: ' . $e->getMessage());
        return response()->json([
            'results' => [],
            'message' => $e->getMessage(),
        ], 500);
    }
}
    public function checkNoKantong(string $noKantong): ?array
{
    $detail = PengirimanSampleDetail::with(['pengirimanSample', 'donor'])
        ->where('no_kantong', $noKantong)
        ->first();
        
    if (!$detail) return null;
    
    $pengirimanSample = $detail->pengirimanSample;
    $donor = $detail->donor;
    
    return [
        'found' => true,
        'data' => [
            'no_kantong' => $detail->no_kantong,
            'no_fpd' => $pengirimanSample?->no_fpd,
            'tgl_aftap' => $detail->tanggal_aftap?->format('Y-m-d'),
            'tgl_produksi' => $pengirimanSample?->tanggal_fpd?->format('Y-m-d'),
            'jenis_darah' => $detail->jenis_kantong,
            'golongan_darah' => $detail->gol_darah ?? $donor?->golongan_darah,
            'rhesus' => $detail->rhesus ?? $donor?->rhesus,
            'nama_asal_darah' => $donor?->nama,
            'skrining' => $donor?->skrining ?? 'NEG',
            'suhu' => $detail->suhu,
        ]
    ];
}
}