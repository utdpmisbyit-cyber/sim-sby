<?php

namespace App\Http\Controllers\Finance\Laporan;

use App\Http\Controllers\IoResourceController;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArusKasController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.laporan.arus_kas';
    protected $itemVariable = 'arus_kas';

    public function index()
{
    return $this->search(new Request());
}


public function search(Request $request)
{
    $tglAwal = $request->tgl_awal ?? date('Y-01-01');
    $tglAkhir = $request->tgl_akhir ?? date('Y-12-31');

    // Ambil semua trial balance dengan relasi COA
    $trial = TrialBalance::with('coa')->get();

    // Hitung total berdasarkan kategori_coa
    $penerimaan = $trial->where('coa.kategori_1', 'OPERASIONAL')
                        ->sum('debet');

    $pembayaran = $trial->where('coa.kategori_1', 'OPERASIONAL')
                        ->sum('kredit');

    $investasi = $trial->where('coa.kategori_1', 'INVESTASI')
                        ->sum('kredit');

    // saldo akhir = SA + mutasi
    $saldoAkhir = $trial->sum('sa_debet') - $trial->sum('sa_kredit')
                 + $trial->sum('debet') - $trial->sum('kredit');

    return view($this->viewPrefix.'.index', [
        'penerimaan' => $penerimaan,
        'pembayaran' => $pembayaran,
        'investasi'  => $investasi,
        'saldoAkhir' => $saldoAkhir,
        'trial'      => $trial,
        'tglAwal'    => $tglAwal,
        'tglAkhir'   => $tglAkhir,
    ]);
}
    public function create() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}

}