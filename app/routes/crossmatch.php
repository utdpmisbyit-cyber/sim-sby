<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.crossmatch.index');
Route::name('.')->group(function () {

     Route::prefix('permintaan_fpup/api')->name('permintaan_fpup.')->group(function () {
        Route::get('next-no-fpup', [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'nextNoFpup'])
            ->name('next-no-fpup');
        Route::get('search-rs',   [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'searchRs'])
            ->name('search-rs');
        Route::get('rs-by-kode',  [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'rsByKode'])
            ->name('rs-by-kode');
        Route::get('diagnosa',    [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'listDiagnosa'])
            ->name('diagnosa');
    });

    Route::prefix('permintaan_fpup/pasien')->name('permintaan_fpup.pasien.')->group(function () {
        Route::get('cari',         [App\Http\Controllers\Crossmatch\PasienFpupController::class, 'cari'])->name('cari');
        Route::post('ocr-preview', [App\Http\Controllers\Crossmatch\PasienFpupController::class, 'ocrPreview'])->name('ocr-preview');
        Route::post('store',       [App\Http\Controllers\Crossmatch\PasienFpupController::class, 'store'])->name('store');
        Route::get('{id}',         [App\Http\Controllers\Crossmatch\PasienFpupController::class, 'show'])->name('show');
    });

    Route::prefix('permintaan_fpup')->name('permintaan_fpup.')->group(function () {
        Route::get('/',                             [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'index'])        ->name('index');
        Route::get('/create',                       [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'create'])       ->name('create');
        Route::post('/',                            [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'store'])        ->name('store');
        Route::get('/permintaan-fpup/{id}/barcode', [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'barcode'])->name('barcode');
        Route::get('/{permintaan_fpup}/edit',       [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'edit'])         ->name('edit');
        Route::put('/{permintaan_fpup}',            [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'update'])       ->name('update');
        Route::patch('/{permintaan_fpup}/status',   [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'updateStatus']) ->name('update-status');
        Route::delete('/{permintaan_fpup}',         [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'destroy'])      ->name('destroy');
        Route::get('/{permintaan_fpup}',             [App\Http\Controllers\Crossmatch\PermintaanFpupController::class, 'show'])         ->name('show');
    });

    Route::prefix('cross_test')->name('cross_test.')->group(function () {
        Route::get('/',              [App\Http\Controllers\Crossmatch\CrossTestController::class, 'index'])->name('index');
        Route::post('/scan',         [App\Http\Controllers\Crossmatch\CrossTestController::class, 'scan'])->name('scan');
        Route::post('/scan_stock',   [App\Http\Controllers\Crossmatch\CrossTestController::class, 'scanStock'])->name('scan_stock');
        Route::post('/store',        [App\Http\Controllers\Crossmatch\CrossTestController::class, 'store'])->name('store');
        Route::post('/petugas',      [App\Http\Controllers\Crossmatch\CrossTestController::class, 'scanPetugas'])->name('petugas');
        Route::get('/{crossTest}',   [App\Http\Controllers\Crossmatch\CrossTestController::class, 'show'])->name('show');
        Route::put('/{crossTest}',   [App\Http\Controllers\Crossmatch\CrossTestController::class, 'update'])->name('update');
        Route::delete('/{crossTest}',[App\Http\Controllers\Crossmatch\CrossTestController::class, 'destroy'])->name('destroy');
        Route::get('/search', [App\Http\Controllers\Crossmatch\CrossTestController::class, 'search'])->name('cross_test.search');
   
   
        });

    Route::prefix('pelayanan_crosstest')->name('pelayanan_crosstest.')->group(function () {
       Route::post('/scan-fpup',    [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'scanFpup'])->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'scanStock'])->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'scanPetugas'])->name('scan_petugas');
 
        Route::get('/',                        [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'index'])->name('index');
        Route::post('/',                       [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'store'])->name('store');
        Route::get('/{pelayananCrosstest}',    [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'show'])->name('show');
        Route::put('/{pelayananCrosstest}',    [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'update'])->name('update');
        Route::delete('/{pelayananCrosstest}', [App\Http\Controllers\Crossmatch\PelayananCrosstestController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('pemberian_darah')->name('pemberian_darah.')->group(function () {
        Route::post('/scan-fpup',    [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'scanFpup'])   ->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'scanStock'])  ->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'scanPetugas'])->name('scan_petugas');
        Route::get('/',                      [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'index'])  ->name('index');
        Route::get('/create',                [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'create']) ->name('create');
        Route::post('/',                     [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'store'])  ->name('store');
        Route::get('/{pemberianDarah}',      [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'show'])   ->name('show');
        Route::get('/{pemberianDarah}/edit', [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'edit'])   ->name('edit');
        Route::put('/{pemberianDarah}',      [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'update']) ->name('update');
        Route::delete('/{pemberianDarah}',   [App\Http\Controllers\Crossmatch\PemberianDarahCrossmatchController::class, 'destroy'])->name('destroy');
    });
     Route::prefix('pengembalian_darah')->name('pengembalian_darah.')->group(function () {
 
        Route::post('/scan-fpup',    [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'scanFpup'])   ->name('scan_fpup');
        Route::post('/scan-stock',   [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'scanStock'])  ->name('scan_stock');
        Route::post('/scan-petugas', [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'scanPetugas'])->name('scan_petugas');
 
        // DEBUG — hapus setelah selesai konfigurasi
        Route::get('/debug-tables', [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'debugTables'])->name('debug_tables');
 
        // Resource routes
        Route::get('/',                          [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'index']) ->name('index');
        Route::get('/create',                    [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'create'])->name('create');
        Route::post('/',                         [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'store']) ->name('store');
        Route::get('/{pengembalianDarah}',       [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'show'])  ->name('show');
        Route::get('/{pengembalianDarah}/edit',  [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'edit'])  ->name('edit');
        Route::put('/{pengembalianDarah}',       [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'update'])->name('update');
        Route::delete('/{pengembalianDarah}',    [App\Http\Controllers\Crossmatch\PengembalianDarahCrossmatchController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('riwayat_pasien_crossmatch')->name('riwayat_pasien_crossmatch.')->group(function () {
   
        Route::get('/', [App\Http\Controllers\Crossmatch\RiwayatPasienCrossmatchController::class, 'index'])->name('index');
        Route::get('/detail', [App\Http\Controllers\Crossmatch\RiwayatPasienCrossmatchController::class, 'riwayat'])->name('detail');
    });
    Route::prefix('penyisihan_crossmatch/api')->name('penyisihan_crossmatch.')->group(function () {
    Route::get('next-no-penyisihan', [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'nextNoPenyisihan'])
        ->name('next-no-penyisihan');
    Route::post('scan-stock', [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'scanStock'])
        ->name('scan-stock');
});
 
Route::prefix('penyisihan_crossmatch')->name('penyisihan_crossmatch.')->group(function () {
    Route::get('/',                [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'index'])  ->name('index');
    Route::get('/create',          [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'create'])->name('create');
    Route::post('/',               [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'store']) ->name('store');
    Route::get('/{penyisihanCrossmatch}/edit',  [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'edit'])  ->name('edit');
    Route::put('/{penyisihanCrossmatch}',       [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'update'])->name('update');
    Route::delete('/{penyisihanCrossmatch}',    [App\Http\Controllers\Crossmatch\PenyisihanCrossmatchController::class, 'destroy'])->name('destroy');
});

});