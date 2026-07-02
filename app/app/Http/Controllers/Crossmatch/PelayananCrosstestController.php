<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\PelayananCrosstest;
use App\Services\PelayananCrosstestService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelayananCrosstestController extends Controller
{
    public function __construct(protected PelayananCrosstestService $service)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'hasil', 'tgl_from', 'tgl_to']);

        $pelayananList = $this->service->getList($filters, 15);

        return view('app.crossmatch.pelayanan_crosstest.index', [
            'pelayananList' => $pelayananList,
            'filters'       => $filters,
        ]);
    }

    public function show(PelayananCrosstest $pelayananCrosstest)
    {
        $pelayananCrosstest->load(['crossTest', 'permintaanFpup']);

        return response()->json([
            'success' => true,
            'data'    => $pelayananCrosstest,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $pelayanan = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Hasil crossmatch berhasil disimpan.',
            'data'    => $pelayanan,
        ]);
    }

    public function update(Request $request, PelayananCrosstest $pelayananCrosstest)
    {
        $data = $this->validateData($request);

        $this->service->update($pelayananCrosstest, $data);

        return response()->json([
            'success' => true,
            'message' => 'Hasil crossmatch berhasil diperbarui.',
            'data'    => $pelayananCrosstest,
        ]);
    }

    public function destroy(PelayananCrosstest $pelayananCrosstest)
    {
        $this->service->delete($pelayananCrosstest);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function scanFpup(Request $request)
    {
        $request->validate(['no_fpup' => 'required|string|max:30']);

        return response()->json(
            $this->service->scanFpup($request->input('no_fpup'))
        );
    }

    public function scanStock(Request $request)
    {
        $request->validate(['no_stock' => 'required|string|max:30']);
        
        // Ambil no_fpup dari request (dikirim dari frontend)
        $noFpup = $request->input('no_fpup');
    
        return response()->json(
            $this->service->scanStock(
                $request->input('no_stock'),
                $noFpup
            )
        );
    }
 

    public function scanPetugas(Request $request)
{
    // Terima 'nip' dari frontend (bisa berisi kode, nama, atau ID)
    // Untuk kompatibilitas dengan form yang sudah ada
    $request->validate(['nip' => 'required|string|max:100']);
 
    // Teruskan ke service dengan nama parameter 'keyword'
    return response()->json(
        $this->service->scanPetugas($request->input('nip'))
    );
}

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'cross_test_id'      => ['required', 'integer', 'exists:cross_tests,id'],
            'permintaan_fpup_id' => ['required', 'integer', 'exists:permintaan_fpup,id'],
            'no_fpup'            => ['required', 'string', 'max:30'],
            'no_stock'           => ['nullable', 'string', 'max:30'],
            'jns_darah'          => ['nullable', 'string', 'max:50'],
            'gol'                => ['nullable', 'string', 'max:10'],
            'rhesus'             => ['nullable', 'string', 'max:10'],
            'metode'             => ['required', Rule::in(['GEL', 'TUBE', 'COLUMN'])],
            'hasil'              => ['nullable', Rule::in(['Cocok', 'Tidak Cocok', 'Doubtful'])],
            'nat'                => ['nullable', 'boolean'],
            'skrining'           => ['nullable', Rule::in(['NEG', 'POS', '-'])],
            'keterangan'         => ['nullable', 'string'],
            'catatan'            => ['nullable', 'string'],
            'pemeriksa'          => ['nullable', 'string', 'max:100'],
            'status'             => ['nullable', Rule::in(['pending', 'proses', 'selesai', 'batal'])],
        ]);
    }
}