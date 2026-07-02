<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.kantong_darah.index');
Route::name('.')->group(function () {

    ioRouteResource('permintaan_kantong', App\Http\Controllers\KantongDarah\PermintaanKantongController::class);
    Route::put('permintaan_kantong/{id}/confirm', [App\Http\Controllers\KantongDarah\PermintaanKantongController::class, 'confirm']);
    Route::prefix('permintaan_kantong/{permintaan_kantong}/detail')->name('permintaan_kantong.detail.')->group(function () {
        Route::post('/', [App\Http\Controllers\KantongDarah\PermintaanKantongDetailController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\KantongDarah\PermintaanKantongDetailController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\KantongDarah\PermintaanKantongDetailController::class, 'destroy'])->name('destroy');
    });

    ioRouteResource('pengembalian_kantong', App\Http\Controllers\KantongDarah\PengembalianKantongController::class);
    Route::put('pengembalian_kantong/{id}/confirm', [App\Http\Controllers\KantongDarah\PengembalianKantongController::class, 'confirm']);
    Route::prefix('pengembalian_kantong/{pengembalian_kantong}/detail')->name('pengembalian_kantong.detail.')->group(function () {
        Route::post('/', [App\Http\Controllers\KantongDarah\PengembalianKantongDetailController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\KantongDarah\PengembalianKantongDetailController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\KantongDarah\PengembalianKantongDetailController::class, 'destroy'])->name('destroy');
    });

    Route::get('/persediaan_kantong', [App\Http\Controllers\KantongDarah\PersediaanKantongController::class, 'index'])->name('persediaan_kantong.index');

    ioRouteResource('rencana_produksi', App\Http\Controllers\KantongDarah\RencanaProduksiController::class);
     Route::get('rencana_produksi/petugas/by-kode', [App\Http\Controllers\KantongDarah\RencanaProduksiController::class, 'petugasByKode'])->name('rencana_produksi.petugas_by_kode');
     Route::get('rencana_produksi/pengiriman-sample/{id}', [App\Http\Controllers\KantongDarah\RencanaProduksiController::class, 'pengirimanSampleInfo'])->name('rencana_produksi.pengiriman_sample_info');
     Route::prefix('rencana_produksi/{rencana_produksi}/detail')->name('rencana_produksi.detail.')->group(function () {
        Route::get('/aftap-info', [App\Http\Controllers\KantongDarah\RencanaProduksiDetailController::class, 'aftapInfo'])->name('aftap_info');
        Route::post('/', [App\Http\Controllers\KantongDarah\RencanaProduksiDetailController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\KantongDarah\RencanaProduksiDetailController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\KantongDarah\RencanaProduksiDetailController::class, 'destroy'])->name('destroy');
    });

     ioRouteResource('produksi_rilis', App\Http\Controllers\KantongDarah\ProduksiRilisController::class);


});
