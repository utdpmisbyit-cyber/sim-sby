<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\KasMasuk;
use App\Models\Petugas;
use App\Models\ProgramKerja;
use App\Models\GeneralLeadge;
use App\Models\TrialBalance;
use App\Services\KasMasukService;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KasMasukController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.kas_masuk';
    protected $itemVariable = 'kas_masuk';

    public function __construct(KasMasukService $service)
    {
        $this->service = $service;
    }
    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = KasMasuk::query();

        if ($request->kode) {
            $data->where('kode', 'like', "%{$request->kode}%");
        }

        if ($request->program_kerja_id) {
            $data->where('program_kerja_id', $request->program_kerja_id);
        }

        if ($request->nama_akun) {
            $data->where('nama_akun', 'like', "%{$request->nama_akun}%");
        }

        $kas_masuk = $data->orderBy('tgl', 'DESC')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('kas_masuk'));
    }

    // =======================
    // CREATE FORM
    // =======================
    public function create()
    {
        $petugasList = Petugas::all();
        $programKerjaList = ProgramKerja::all();
        $coaList = Coa::all();

        // Generate kode otomatis
        $last = KasMasuk::orderBy('id', 'DESC')->first();
        $num = $last ? $last->id + 1 : 1;
        $kodeOtomatis = 'KM-' . str_pad($num, 4, '0', STR_PAD_LEFT);

        return view("$this->viewPrefix._form", compact(
            'petugasList', 'programKerjaList', 'coaList', 'kodeOtomatis'
        ));
    }

    // =======================
    // EDIT FORM
    // =======================
    public function edit($id)
    {
        $kas_masuk = KasMasuk::findOrFail($id);
        $petugasList = Petugas::all();
        $programKerjaList = ProgramKerja::all();
        $coaList = Coa::all();

        return view("$this->viewPrefix._form", compact(
            'kas_masuk', 'petugasList', 'programKerjaList', 'coaList'
        ));
    }

    // =======================
    // STORE
    // =======================
    public function store(Request $request)
    { 
        $data = $request->validate([
        'kode'             => 'required|unique:kas_masuk,kode',
        'program_kerja_id' => 'required|exists:program_kerja,id',
        'dokumen'          => 'nullable|string',
        'ref_an'           => 'nullable|string',
        'rekning_kas'      => 'nullable|string',
        'transaksi'        => 'nullable|string',
        'nominal'          => 'required|numeric',
        'keterangan'       => 'nullable|string',
        'tgl'              => 'required|date',
        'nama_akun'        => 'required|exists:coa,nama_akun',
    ]);

    $this->service->store($data);

    return back()->with('success', 'Data Kas Masuk berhasil ditambahkan.'); 
    

    }

    // =======================
    // UPDATE
    // =======================

    public function update(Request $request, $id)
    {
          $kas = KasMasuk::findOrFail($id);

    $data = $request->validate([
         'kode' => [
                        'required',
                        Rule::unique('kas_masuk', 'kode')->ignore($kas->id)
                    ],
        'program_kerja_id' => 'required|exists:program_kerja,id',
        'dokumen'          => 'nullable|string',
        'ref_an'           => 'nullable|string',
        'rekning_kas'      => 'nullable|string',
        'transaksi'        => 'nullable|string',
        'nominal'          => 'required|numeric',
        'keterangan'       => 'nullable|string',
        'tgl'              => 'required|date',
        'nama_akun'        => 'required|exists:coa,nama_akun',
    ]);

    $this->service->update($data, $kas);
        return back()->with('success', 'Data Kas Masuk berhasil diperbarui.');
    }
    // =======================
    // DELETE
    // =======================
     public function destroy($id)
    {
        $kas = KasMasuk::findOrFail($id);
        $this->service->delete($kas);

        return back()->with('success', 'Data Kas Masuk + GL + Trial Balance berhasil dihapus.');
    }
}