<?php

namespace App\Http\Controllers\Finance\Laporan;

use App\Http\Controllers\IoResourceController;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsetNettoController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.laporan.aset_netto';
    protected $itemVariable = 'aset_netto';

    public function index()
{
    return $this->search(new Request());
}

public function search(Request $request)
{
    $tahunIni  = $request->filled('tanggal_akhir')
        ? Carbon::parse($request->tanggal_akhir)->year
        : Carbon::now()->year;

    $tahunLalu = $tahunIni - 1;
    $tglAwal   = $request->tanggal_awal ?: null;
    $tglAkhir  = $request->tanggal_akhir ?: null;

   $getData = function (string $posLaporan, int $tahun, ?string $tglAwal, ?string $tglAkhir) {

    $query = TrialBalance::query()
        ->whereYear('created_at', $tahun);

    // === MAPPING OTOMATIS BERDASARKAN kategori1 ===
    $query->where(function($q) use ($posLaporan) {
        
        if ($posLaporan === 'TIDAK_TERIKAT') {
            $q->where('kategori1', 'like', '%Pendapatan%')
              ->orWhere('kategori1', 'like', '%Beban%')
              ->orWhere('kategori1', 'like', '%Aset%')
              ->orWhere('kategori1', 'like', '%Liabilitas%');
        }

        if ($posLaporan === 'TERIKAT') {
            $q->where('kategori1', 'like', '%Terikat%')
              ->orWhere('kategori1', 'like', '%Dana Terikat%')
              ->orWhere('kategori1', 'like', '%Ekuitas Terikat%');
        }

    });

    // === FILTER TANGGAL ===
    if ($tglAwal) {
        $query->whereDate('created_at', '>=', $tglAwal);
    }
    if ($tglAkhir) {
        $query->whereDate('created_at', '<=', $tglAkhir);
    }

    return $query->get();
};

   $tidakTerikatCurrent  = $getData('TIDAK_TERIKAT', $tahunIni,  $tglAwal, $tglAkhir);
$tidakTerikatPrevious = $getData('TIDAK_TERIKAT', $tahunLalu, null, null);
$terikatCurrent       = $getData('TERIKAT', $tahunIni,  $tglAwal, $tglAkhir);
$terikatPrevious      = $getData('TERIKAT', $tahunLalu, null, null);

$tidakTerikat = [
    'saldo_awal'       => [
        'current'  => $this->hitungSaldoAwal($tidakTerikatCurrent),
        'previous' => $this->hitungSaldoAwal($tidakTerikatPrevious)
    ],

    'pendapatan_netto' => [
        'current'  => $this->hitungPendapatanNetto($tidakTerikatCurrent),
        'previous' => $this->hitungPendapatanNetto($tidakTerikatPrevious)
    ],
];

// ➤ SALDO AKHIR = SALDO AWAL + PENDAPATAN NETTO
$tidakTerikat['saldo_akhir'] = [
    'current'  => ($tidakTerikat['saldo_awal']['current'] ?? 0)
                + ($tidakTerikat['pendapatan_netto']['current'] ?? 0),

    'previous' => ($tidakTerikat['saldo_awal']['previous'] ?? 0)
                + ($tidakTerikat['pendapatan_netto']['previous'] ?? 0),
];

$terikat = [
    'saldo_awal'       => [
        'current'  => $this->hitungSaldoAwal($terikatCurrent),
        'previous' => $this->hitungSaldoAwal($terikatPrevious)
    ],

    'pendapatan_netto' => [
        'current'  => $this->hitungPendapatanNetto($terikatCurrent),
        'previous' => $this->hitungPendapatanNetto($terikatPrevious)
    ],
];

// ➤ SALDO AKHIR = SALDO AWAL + PENDAPATAN NETTO
$terikat['saldo_akhir'] = [
    'current'  => ($terikat['saldo_awal']['current'] ?? 0)
                + ($terikat['pendapatan_netto']['current'] ?? 0),

    'previous' => ($terikat['saldo_awal']['previous'] ?? 0)
                + ($terikat['pendapatan_netto']['previous'] ?? 0),
];

// ➤ TOTAL ASET NETTO
$totalNetto = [
    'current'  => $tidakTerikat['saldo_akhir']['current']
                + $terikat['saldo_akhir']['current'],

    'previous' => $tidakTerikat['saldo_akhir']['previous']
                + $terikat['saldo_akhir']['previous'],
];
    $viewName = $request->ajax()
        ? "$this->viewPrefix._table"
        : "$this->viewPrefix.index";

    return view($viewName, compact(
        'tidakTerikat',
        'terikat',
        'totalNetto',
        'tahunIni',
        'tahunLalu'
    ));

}

    /**
     * Saldo Awal = sa_debet - sa_kredit
     * Kolom sa_debet / sa_kredit adalah saldo awal sebelum mutasi.
     */
    private function hitungSaldoAwal($records): float
    {
        return $records->sum(function ($item) {
            return (float)($item->sa_debet ?? 0) - (float)($item->sa_kredit ?? 0);
        });
    }

    /**
     * Pendapatan Netto Periode Berjalan = kredit - debet (mutasi periode berjalan)
     * Untuk laporan aktivitas / perubahan aset netto, pendapatan > beban = surplus.
     */
    private function hitungPendapatanNetto($records): float
    {
        return $records->sum(function ($item) {
            return (float)($item->kredit ?? 0) - (float)($item->debet ?? 0);
        });
    }

    /**
     * Saldo Akhir = neraca_kredit - neraca_debet
     * atau bisa juga saldo_awal + pendapatan_netto tergantung standar yang dipakai.
     */
    private function hitungSaldoAkhir($records): float
    {
        return $records->sum(function ($item) {
            return (float)($item->neraca_kredit ?? 0) - (float)($item->neraca_debet ?? 0);
        });
    }
}