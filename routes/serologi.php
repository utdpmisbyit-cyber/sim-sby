<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.serologi.index');
Route::name('.')->group(function () {
    ioRouteResource('transaksi_serologi', App\Http\Controllers\Serologi\SerologiController::class);
    Route::get('transaksi_serologi/petugas/by-kode', [App\Http\Controllers\Serologi\SerologiController::class, 'petugasByKode'])->name('transaksi_serologi.petugas_by_kode');
    Route::post('transaksi_serologi/{id}/duplicate', [App\Http\Controllers\Serologi\SerologiController::class, 'duplicate']);
    Route::prefix('transaksi_serologi/{serologi}/detail')->name('transaksi_serologi.detail.')->group(function () {
        Route::get('/aftap-info', [App\Http\Controllers\Serologi\SerologiDetailController::class, 'aftapInfo'])->name('aftap_info');
        Route::post('/', [App\Http\Controllers\Serologi\SerologiDetailController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Serologi\SerologiDetailController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Serologi\SerologiDetailController::class, 'destroy'])->name('destroy');
    });

    ioRouteResource('permintaan_supplier', App\Http\Controllers\Serologi\PermintaanSupplierController::class);
});
