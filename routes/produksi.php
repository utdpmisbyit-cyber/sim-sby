<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.produksi.index');
Route::name('.')->group(function () {

    Route::prefix('pengiriman_darah_prolis')->name('pengiriman_darah_prolis.')->group(function () {

        Route::get('/',             [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'index'])->name('index');
        Route::get('/create',       [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'create'])->name('create');
        Route::post('/',            [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'store'])->name('store');
        Route::get('/scan_kantong', [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'scanKantong'])->name('scan_kantong');
        Route::get('/json/list',    [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'dataJson'])->name('data_json');
        Route::get('/search/petugas', [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'searchPetugas'])->name('search_petugas');
      
        Route::get('/{id}',             [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'show'])->name('show');
        Route::get('/{id}/edit',        [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'edit'])->name('edit');
        Route::put('/{id}',             [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'update'])->name('update');
        Route::delete('/{id}',          [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'destroy'])->name('destroy');
        Route::get('/json/{id}/show',   [App\Http\Controllers\Produksi\PengirimanDarahProlisController::class, 'showJson'])->name('show_json');
    });

});