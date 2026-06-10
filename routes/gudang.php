<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.gudang.index');
Route::name('.')->group(function () {

    Route::get('pendataan_kantong/next-seq',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'nextSeq'])->name('pendataan_kantong.next-seq');
    Route::post('pendataan_kantong/store-batch',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'storeBatch'])->name('pendataan_kantong.store-batch');
    Route::post('pendataan-kantong/print-direct',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'printDirect'])->name('pendataan_kantong.print-direct');
    Route::get('stok_kantong/find',[App\Http\Controllers\Gudang\StokKantongController::class, 'find'])->name('stok_kantong.find');
    Route::get('stok_kantong/find-keluar',[App\Http\Controllers\Gudang\StokKantongController::class, 'findKeluar'])->name('stok_kantong.find_keluar');
    Route::get('stok_kantong/summary',[App\Http\Controllers\Gudang\StokKantongController::class, 'summary'])->name('stok_kantong.summary');
    Route::post('stok_kantong/save',[App\Http\Controllers\Gudang\StokKantongController::class, 'save'])->name('stok_kantong.save');
    Route::post('stok_kantong/save-kembali',[App\Http\Controllers\Gudang\StokKantongController::class, 'saveKembali'])->name('stok_kantong.save_kembali');
    Route::get('stok_kantong/list',[App\Http\Controllers\Gudang\StokKantongController::class, 'list'])->name('stok_kantong.list');
    Route::get('pengeluaran_kantong/find',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'find'])->name('pengeluaran_kantong.find');
    Route::post('pengeluaran_kantong/save',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'save'])->name('pengeluaran_kantong.save');
    Route::get('pengeluaran_kantong/list',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'list'])->name('pengeluaran_kantong.list');
    Route::get('permintaan_kantong/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'getPermintaan']);
    Route::get('pengeluaran_kantong/get/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'show']);
    Route::delete('pengeluaran_kantong/delete/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'delete']);
    Route::prefix('cetak_barcode')->name('cetak_barcode.')->group(function () {
        Route::get('/',     [App\Http\Controllers\Gudang\CetakUlangBarcodeController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Gudang\CetakUlangBarcodeController::class, 'data'])->name('data');
    });

    
    ioRouteResource('pendataan_kantong',   App\Http\Controllers\Gudang\PendataanKantongController::class);
    ioRouteResource('stok_kantong',        App\Http\Controllers\Gudang\StokKantongController::class);
    ioRouteResource('pengeluaran_kantong', App\Http\Controllers\Gudang\PengeluaranKantongController::class);
    ioRouteResource('konfirmasi_permintaan', App\Http\Controllers\Gudang\KonfirmasiPermintaanController::class);
    

});