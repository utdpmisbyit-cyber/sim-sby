<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.referal.index');
      Route::name('.')->group(function () {

   Route::prefix('permintaan_fpup')->name('permintaan_fpup.')->group(function () {

        Route::get('/', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'index'])->name('index');
        Route::get('/tambah', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'create'])->name('create');

        Route::get('/master/rumah-sakit/cari/{kode}', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'cariRumahSakit'])->name('cari-rs');
        Route::get('/master/rumah-sakit/search', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'searchRumahSakit'])->name('search-rs');

        Route::post('/', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'show'])->name('show');
        Route::get('/{id}/ubah', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'destroy'])->name('destroy');

        Route::prefix('pasien')->name('pasien.')->group(function () {
            Route::get('/cari', [App\Http\Controllers\Referal\FpupPasienController::class, 'search'])->name('cari');
            Route::post('/ocr-preview', [App\Http\Controllers\Referal\FpupPasienController::class, 'ocrPreview'])->name('ocr-preview');
            Route::post('/', [App\Http\Controllers\Referal\FpupPasienController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Referal\FpupPasienController::class, 'show'])->name('show');
        });

        Route::post('/fpup/{fpupId}/jadikan-referal', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'jadikanReferal'])->name('jadikan-referal');
        Route::patch('/{id}/status-referal', [App\Http\Controllers\Referal\PermintaanFpupReferalController::class, 'updateStatusReferal'])->name('status-referal');
    });
 
   Route::prefix('cross_test_referal')->name('cross_test_referal.')->group(function () {
        Route::get('/',               [App\Http\Controllers\Referal\CrossTestReferalController::class, 'index'])->name('index');
        Route::post('/scan',          [App\Http\Controllers\Referal\CrossTestReferalController::class, 'scan'])->name('scan');
        Route::post('/scan_stock',    [App\Http\Controllers\Referal\CrossTestReferalController::class, 'scanStock'])->name('scan_stock');
        Route::post('/store',         [App\Http\Controllers\Referal\CrossTestReferalController::class, 'store'])->name('store');
        Route::post('/petugas',       [App\Http\Controllers\Referal\CrossTestReferalController::class, 'scanPetugas'])->name('petugas');
        Route::get('/search',         [App\Http\Controllers\Referal\CrossTestReferalController::class, 'search'])->name('search');
        Route::get('/{crossTestReferal}',    [App\Http\Controllers\Referal\CrossTestReferalController::class, 'show'])->name('show');
        Route::put('/{crossTestReferal}',    [App\Http\Controllers\Referal\CrossTestReferalController::class, 'update'])->name('update');
        Route::delete('/{crossTestReferal}', [App\Http\Controllers\Referal\CrossTestReferalController::class, 'destroy'])->name('destroy');
    });
   Route::prefix('pelayanan_crosstest_referal')->name('pelayanan_crosstest_referal.')->group(function () {
       Route::post('/scan-fpup',    [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'scanFpup'])->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'scanStock'])->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'scanPetugas'])->name('scan_petugas');
        Route::get('/',                        [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'index'])->name('index');
        Route::post('/',                       [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'store'])->name('store');
        Route::get('/{pelayananCrosstest}',    [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'show'])->name('show');
        Route::put('/{pelayananCrosstest}',    [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'update'])->name('update');
        Route::delete('/{pelayananCrosstest}', [App\Http\Controllers\Referal\PelayananCrosstestReferalController::class, 'destroy'])->name('destroy');
    });
   Route::prefix('pemberian_darah')->name('pemberian_darah.')->group(function () {
        Route::post('/scan-fpup',    [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'scanFpup'])->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'scanStock'])->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'scanPetugas'])->name('scan_petugas');
        Route::get('/',                      [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'index'])->name('index');
        Route::get('/create',                [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'create'])->name('create');
        Route::post('/',                     [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'store'])->name('store');
        Route::get('/{pemberianDarah}',      [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'show'])->name('show');
        Route::get('/{pemberianDarah}/edit', [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'edit'])->name('edit');
        Route::put('/{pemberianDarah}',      [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'update'])->name('update');
        Route::delete('/{pemberianDarah}',   [App\Http\Controllers\Referal\PemberianDarahReferalController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('pengembalian_darah')->name('pengembalian_darah.')->group(function () {
 
        Route::post('/scan-fpup',    [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'scanFpup'])   ->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'scanStock'])  ->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'scanPetugas'])->name('scan_petugas');
        Route::get('/debug-tables', [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'debugTables'])->name('debug_tables');
 
        // Resource routes
        Route::get('/',                          [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'index']) ->name('index');
        Route::get('/create',                    [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'create'])->name('create');
        Route::post('/',                         [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'store']) ->name('store');
        Route::get('/{pengembalianDarah}',       [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'show'])  ->name('show');
        Route::get('/{pengembalianDarah}/edit',  [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'edit'])  ->name('edit');
        Route::put('/{pengembalianDarah}',       [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'update'])->name('update');
        Route::delete('/{pengembalianDarah}',    [App\Http\Controllers\Referal\PengembalianDarahReferalController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pemberian_awal_referal')->name('pemberian_awal_referal.')->group(function () {
 
        // AJAX pendukung form (cari FPUP & cari stok darah)
        Route::get('/cari-fpup',    [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'cariFpup'])   ->name('cari_fpup');
        Route::get('/search-stock', [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'searchStock'])->name('search_stock');
        Route::get('/search-barang', [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'searchBarang'])->name('search_barang');
 
        // Resource routes
        Route::get('/',                          [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'index'])  ->name('index');
        Route::get('/tambah',                    [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'create']) ->name('create');
        Route::post('/',                         [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'store'])  ->name('store');
        Route::get('/{pemberianAwalReferal}/ubah',   [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'edit'])   ->name('edit');
        Route::put('/{pemberianAwalReferal}',        [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'update']) ->name('update');
        Route::delete('/{pemberianAwalReferal}',     [App\Http\Controllers\Referal\PemberianAwalReferalController::class, 'destroy'])->name('destroy');
    });


});