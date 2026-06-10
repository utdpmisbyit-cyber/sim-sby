<?php

use Illuminate\Support\Facades\Route;

Route::view('/','app.finance.index');
Route::name('.')->group(function () {
    ioRouteResource('coa', App\Http\Controllers\Finance\CoaController::class);
    ioRouteResource('anggaran', App\Http\Controllers\Finance\AnggaranController::class);
    ioRouteResource('kas_masuk', App\Http\Controllers\Finance\KasMasukController::class);
    ioRouteResource('kas_keluar', App\Http\Controllers\Finance\KasKeluarController::class);
    ioRouteResource('penyesuaian', App\Http\Controllers\Finance\PenyesuaianController::class);
    ioRouteResource('general_ledge', App\Http\Controllers\Finance\GeneralLedgeController::class);
    ioRouteResource('trial_balance', App\Http\Controllers\Finance\TrialBalanceController::class);
    ioRouteResource('buku_besar', App\Http\Controllers\Finance\BukuBesarController::class);

});
Route::prefix('laporan')->name('.laporan.')->group(function () {
    ioRouteResource('posisi_keuangan', App\Http\Controllers\Finance\Laporan\PosisiKeuanganController::class);
    ioRouteResource('aset_netto', App\Http\Controllers\Finance\Laporan\AsetNettoController::class);
    ioRouteResource('arus_kas', App\Http\Controllers\Finance\Laporan\ArusKasController::class);

    Route::post('posisi_keuangan/search-json', [App\Http\Controllers\Finance\Laporan\PosisiKeuanganController::class, 'searchJson'])->name('posisi_keuangan.search_json');
    Route::get('aset_netto/search', [App\Http\Controllers\Finance\Laporan\AsetNettoController::class, 'search'])->name('aset_netto.search');
});
