<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Services\PenerimaanProlisPenyimpananService;
use App\Models\PenerimaanProlisPenyimpanan;
use App\Models\PengirimanDarahProlis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenerimaanProlisPenyimpananController extends Controller
{
    public function __construct(
        protected PenerimaanProlisPenyimpananService $service
    ) {}

    public function index()
    {
        return view('app.penyimpanan.penerimaan_prolis.index', [
            'ruanganOptions'     => $this->service->getRuanganOptions(),
            'golonganOptions'    => $this->service->getGolonganDarahOptions(),
            'jenisOptions'       => $this->service->getJenisOptions(),
            'rhesusOptions'      => $this->service->getRhesusOptions(),
            'petugasNama'        => Auth::user()?->name ?? 'Administrator',
            'noPenerimaan'       => \App\Models\PenerimaanProlisPenyimpanan::generateNoPenerimaan(),
            'tglPenerimaan'      => now()->format('d/m/Y'),
        ]);
    }


    public function getData(Request $request): JsonResponse
    {
        $items = $this->service->getAll($request->only([
            'no_penerimaan', 'golongan_darah', 'jenis_darah', 'rhesus',
        ]));

        return response()->json([
            'data'   => $this->formatRows($items),
            'jumlah' => $items->count(),
        ]);
    }

    public function getByNoPengiriman(string $no): JsonResponse
    {
        $items = $this->service->getByNoPengiriman($no);

        return response()->json([
            'data'   => $this->formatRows($items),
            'jumlah' => $items->count(),
        ]);
    }

    public function cekKapasitas(Request $request): JsonResponse
    {
        $request->validate([
            'ruangan' => 'required|string',
        ]);

        $result = $this->service->cekKapasitas(
            $request->ruangan,
            $request->only(['golongan_darah', 'jenis_darah'])
        );

        return response()->json([
            'kapasitas_info' => $result['kapasitas_info'],
            'data'           => $this->formatRows($result['items']),
            'jumlah'         => $result['items']->count(),
        ]);
    }

    public function getKapasitas(Request $request): JsonResponse
    {
        $request->validate(['ruangan' => 'required|string']);

        return response()->json(
            $this->service->getKapasitas($request->ruangan)
        );
    }
     public function getByNoStock(string $noStock): JsonResponse
    {
        $items = $this->service->getByNoStock($noStock);

        return response()->json([
            'data'   => $this->formatRows($items),
            'jumlah' => $items->count(),
        ]);
    }
     public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'no_stok'    => 'required|string',
                'status'     => 'nullable|string|max:50',
                'ruang'      => 'nullable|string|max:50',
                'jumlah'     => 'nullable|integer',
                'keterangan' => 'nullable|string',
            ]);

            $pengiriman = PengirimanDarahProlis::where('no_stok', $request->no_stok)->first();

            if (!$pengiriman) {
                return response()->json(['message' => 'Data pengiriman darah tidak ditemukan'], 404);
            }

            // Siapkan data lengkap dari pengiriman + request
            $data = [
                'no_stok'        => $pengiriman->no_stok,
                'no_kantong'     => $pengiriman->no_kantong,
                'jenis_darah'    => $pengiriman->jenis_darah,
                'golongan_darah' => $pengiriman->golongan_darah,
                'rhesus'         => $pengiriman->rhesus,
                'tgl_aftap'      => $request->tgl_aftap
                    ? Carbon::createFromFormat('d/m/Y', $request->tgl_aftap)->format('Y-m-d')
                    : $pengiriman->tgl_aftap,
                'tgl_produksi'   => $request->tgl_produksi
                    ? Carbon::createFromFormat('d/m/Y', $request->tgl_produksi)->format('Y-m-d')
                    : $pengiriman->tgl_produksi,
                'tgl_expired'    => $request->tgl_expired
                    ? Carbon::createFromFormat('d/m/Y', $request->tgl_expired)->format('Y-m-d')
                    : $pengiriman->tgl_expired,
                'status'         => $request->status,
                'ruang'          => $request->ruang,
                'gr'             => $pengiriman->gr,
                'ml'             => $pengiriman->ml,
                'jumlah'         => $request->jumlah ?? 1,
                'skrining'       => $pengiriman->skrining,
                'keterangan'     => $request->keterangan,
                'no_fpd'         => $pengiriman->no_fpd,
                'asal_darah_id'  => $pengiriman->asal_darah_id,
            ];

            
            $record = $this->service->store($data);

            return response()->json([
                'message'       => 'Data berhasil disimpan',
                'data'          => $record,
                'no_penerimaan' => $record->no_penerimaan,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    
     public function update(Request $request, $id)
    {
        try {

            $item = PenerimaanProlisPenyimpanan::where(
                'no_stok',
                $request->no_stok
            )->first();

            if (!$item) {
                return response()->json([
                    'message' => 'Data penerimaan tidak ditemukan'
                ], 404);
            }

            $item->update([
                'no_kantong'     => $request->no_kantong,
                'jenis_darah'    => $request->jenis_darah,
                'golongan_darah' => $request->golongan_darah,
                'rhesus'         => $request->rhesus,
                'tgl_aftap'      => $request->tgl_aftap,
                'tgl_produksi'   => $request->tgl_produksi,
                'tgl_expired'    => $request->tgl_expired,
                'status'         => $request->status,
                'raung'          => $request->status,
                'gr'             => $request->gr,
                'ml'             => $request->ml,
                'jumlah'         => $request->jumlah,
                'skrining'       => $request->skrining,
                'keterangan'     => $request->keterangan,
                'no_fpd'         => $request->no_fpd,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    => $item
            ]);

        } catch (\Exception $e) {

            \Log::error($e);

            return response()->json([
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    private function formatRows($items): array
    {
        return $items->map(fn($item) => [
            'id'           => $item->id,
            'no_stok'      => $item->no_stok,
            'jenis_darah'        => $item->jenis_darah,
            'golongan_darah' => $item->golongan_darah,
            'rhesus'       => $item->rhesus,
            'tgl_aftap' => $item->tgl_aftap
                ? Carbon::parse($item->tgl_aftap)->format('d/m/Y')
                : '-',
            'tgl_produksi' => $item->tgl_produksi
                ? Carbon::parse($item->tgl_produksi)->format('d/m/Y')
                : '-',
            'tgl_expired' => $item->tgl_expired
                ? Carbon::parse($item->tgl_expired)->format('d/m/Y')
                : '-',
            'status'       => $item->status,
            'ruang'        => $item->ruang,
            'ml'           => $item->ml,
            'gr'           => $item->gr,
            'jumlah'       => $item->jumlah,
            'no_fpd'           => $item->no_fpd,
            'skrining'         => $item->skrining,
        ])->toArray();
    }
    public function stokIndex(): \Illuminate\View\View
    {
        return view('app.penyimpanan.stok.index', [
            'jenisOptions'    => $this->service->getJenisOptions(),
            'golonganOptions' => $this->service->getGolonganDarahOptions(),
            'rhesusOptions'   => $this->service->getRhesusOptions(),
            'petugasNama'     => \Illuminate\Support\Facades\Auth::user()?->name ?? 'Administrator',
        ]);
    }
}