<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Services\PermintaanDarahExternalService;
use App\Models\TujuanDarah;
use App\Models\Petugas;
use App\Models\JenisBiaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermintaanDarahExternalController extends Controller
{
    protected $service;

    public function __construct(PermintaanDarahExternalService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('app.penyimpanan.permintaan_darah_external.index');
    }
   public function getJenisBiaya()
    {
        try {

            $data = $this->service->getJenisBiaya();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {

            Log::error('Error getJenisBiaya : '.$e->getMessage());

            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }
    }
    public function getData(Request $request)
    {
        try {
            $filters = [
                'status' => $request->status,
                'search' => $request->search,
            ];

            $data = $this->service->getAll($filters);
            return response()->json([
                'success'    => true,
                'data'       => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = $this->service->findById($id);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
    }

    /**
     * [BARU] Return nomor permintaan berikutnya (preview sebelum simpan)
     * GET /penyimpanan/permintaan_external/next-nomor
     */
    public function nextNomor()
    {
        try {
            $nomor = \App\Models\PermintaanDarahExternal::generateNomorPermintaan();
            return response()->json(['success' => true, 'nomor' => $nomor]);
        } catch (\Exception $e) {
            Log::error('Error nextNomor: ' . $e->getMessage());
            return response()->json(['success' => false, 'nomor' => 'AUTO-GENERATED']);
        }
    }

    /**
     * [BARU] Return daftar jenis darah dari tabel jenis_darah
     * GET /penyimpanan/permintaan_external/jenis-darah
     */
    public function getJenisDarah()
{
    try {
        $data = \DB::table('jenis_darah')
            ->select('id', 'nama_pendek')
            ->whereNotNull('nama_pendek')
            ->where('nama_pendek', '!=', '')
            ->orderBy('nama_pendek')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    } catch (\Exception $e) {
        Log::error('Error getJenisDarah: ' . $e->getMessage());

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
    /**
     * Search petugas by name or code
     */
    public function searchPetugas(Request $request)
    {
        try {
            $search = $request->get('q', '');

            if (strlen($search) < 2) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $petugas = \App\Models\Petugas::query()
                ->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                          ->orWhere('nama', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get(['id', 'kode', 'nama']);

            return response()->json(['success' => true, 'data' => $petugas]);

        } catch (\Exception $e) {
            Log::error('Error searchPetugas', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    /**
     * Search institusi from TUJUAN_DARAH table
     */
    public function searchInstitusi(Request $request)
    {
        try {
            $search = $request->get('q', '');

            if (strlen($search) < 2) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $institusi = TujuanDarah::query()
                ->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%")
                ->limit(10)
                ->get();

            return response()->json(['success' => true, 'data' => $institusi]);

        } catch (\Exception $e) {
            Log::error('Error searchInstitusi', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_peminta'              => 'required|string|max:100',
                'petugas_kode'              => 'nullable|string|max:50',
                'petugas'                   => 'nullable|string|max:100',
                'institusi_lain'            => 'required|string|max:150',
                'jenis_biaya'               => 'required|string|max:150',
                'dropping'                  => 'nullable|in:AMBIL_SENDIRI,DIANTAR,KURIR',
                'tanggal_perlu'             => 'nullable|date',
                'keterangan'                => 'nullable|string',
                'details'                   => 'required|array|min:1',
                'details.*.jenis_darah'     => 'required|string|max:50',
                'details.*.gol_darah'       => 'required|in:A,B,O,AB',
                'details.*.rhesus'          => 'required|in:Positif,Negatif',
                'details.*.jumlah'          => 'required|integer|min:1',
                'details.*.donor_pengganti' => 'required|in:Ya,Tidak',
                'details.*.no_fpup'         => 'required_if:details.*.donor_pengganti,Ya|nullable|string',
                'details.*.keterangan'      => 'nullable|string',
                'details.*.tanggal_perlu'       => 'nullable|date',
            ]);

            $data = $this->service->create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dibuat',
                'data'    => $data,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Validasi gagal',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_peminta'              => 'sometimes|string|max:100',
                'petugas_kode'              => 'nullable|string|max:50',
                'petugas'                   => 'nullable|string|max:100',
                'institusi_lain'            => 'sometimes|string|max:150',
                'jenis_biaya'               => 'nullable|string|max:150',
                'dropping'                  => 'nullable|in:AMBIL_SENDIRI,DIANTAR,KURIR',
                'tanggal_perlu'             => 'nullable|date',
                'keterangan'                => 'nullable|string',
                'details'                   => 'sometimes|array',
                'details.*.jenis_darah'     => 'required|string|max:50',
                'details.*.gol_darah'       => 'required|in:A,B,O,AB',
                'details.*.rhesus'          => 'required|in:Positif,Negatif',
                'details.*.jumlah'          => 'required|integer|min:1',
                'details.*.donor_pengganti' => 'required|in:Ya,Tidak',
                'details.*.no_fpup'         => 'required_if:details.*.donor_pengganti,Ya|nullable|string',
            ]);

            $data = $this->service->update($id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil diupdate',
                'data'    => $data,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Validasi gagal',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate permintaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['success' => true, 'message' => 'Permintaan berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error('Error delete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permintaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updatePemenuhan(Request $request, $detailId)
    {
        try {
            $request->validate(['jumlah_dipenuhi' => 'required|integer|min:0']);
            $detail = $this->service->updatePemenuhan($detailId, $request->jumlah_dipenuhi);
            return response()->json([
                'success' => true,
                'message' => 'Pemenuhan berhasil diupdate',
                'data'    => $detail,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updatePemenuhan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pemenuhan: ' . $e->getMessage(),
            ], 500);
        }
    }
}