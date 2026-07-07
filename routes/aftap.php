<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.unit.index');
Route::name('.')->group(function () {

    Route::prefix('aftap')->name('aftap.')->group(function () {
        Route::get('/', [App\Http\Controllers\Aftap\AftapController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Aftap\AftapController::class, 'search'])->name('search');
        Route::post('/log_donor/search', [App\Http\Controllers\Aftap\AftapController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Aftap\AftapController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\Aftap\AftapController::class, 'panggil'])->name('log_donor.panggil');
        Route::post('/log_donor/{id}/tipe_kantong', [App\Http\Controllers\Aftap\AftapController::class, 'update_tipe_kantong'])->name('log_donor.update_tipe_kantong');
Route::get('/tipe_kantong/search', [App\Http\Controllers\Aftap\AftapController::class, 'search_tipe_kantong'])->name('tipe_kantong.search');
        Route::get('/display-antrian', [App\Http\Controllers\Aftap\AftapController::class, 'display_antrian'])->name('display_antrian');
        Route::get('/display-antrian/data', [App\Http\Controllers\Aftap\AftapController::class, 'display_antrian_data'])->name('display_antrian_data');
        Route::get('/asal_darah/search', [App\Http\Controllers\Aftap\AftapController::class, 'search_asal_darah'])->name('asal_darah.search');
        Route::post('/log_donor/{id}/asal_darah', [App\Http\Controllers\Aftap\AftapController::class, 'update_asal_darah'])->name('log_donor.update_asal_darah');
        Route::get('/petugas/search', [App\Http\Controllers\Aftap\AftapController::class, 'search_petugas'])->name('petugas.search');
        Route::post('/log_donor/scan', [App\Http\Controllers\Aftap\AftapController::class, 'log_donor_scan'])->name('log_donor.scan');
        Route::post('/scan_kantong', [App\Http\Controllers\Aftap\AftapController::class, 'scan_kantong'])->name('scan_kantong');
        Route::get('/{id}', [App\Http\Controllers\Aftap\AftapController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Aftap\AftapController::class, 'update'])->name('update');
    });

    Route::prefix('penerimaan_kantong')->name('penerimaan.')->group(function () {
    Route::get('/', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'index'])->name('index');
    Route::post('store', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'store'])->name('store');
    Route::post('scan', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'scan'])->name('scan');
    Route::get('next-no-transaksi', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'nextNoTransaksi'])->name('next_no_transaksi');
    Route::get('search-no-keluar', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'searchNoKeluar'])->name('search_no_keluar');
    Route::get('search-no-permintaan', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'searchNoPermintaan'])->name('search_no_permintaan');
 
    Route::post('kantong-by-no-keluar', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'kantongByNoKeluar'])->name('kantong_by_no_keluar');
    Route::post('kantong-by-no-permintaan', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'kantongByNoPermintaan'])->name('kantong_by_no_permintaan');
    Route::post('get-jumlah', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'getJumlah'])->name('get_jumlah');
 
    Route::get('{id}/edit', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'edit'])->name('edit');
 
    Route::put('{id}', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'update'])->name('update');
    Route::delete('{id}', [App\Http\Controllers\Aftap\PenerimaanKantongController::class, 'destroy'])->name('destroy');
 
});
     Route::prefix('pengembalian_kantong')->name('pengembalian_kantong.')->group(function () {
        Route::get('/select2/asal-darah', [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'selectAsalDarah'])->name('select2_asal_darah');
        Route::get('/',   [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'index'])      ->name('index');
        Route::get('/create',[App\Http\Controllers\Aftap\PengembalianKantongController::class, 'create'])     ->name('create');
        Route::post('/scan_kantong',[App\Http\Controllers\Aftap\PengembalianKantongController::class, 'scanKantong'])->name('scan_kantong');
        Route::post('/',[App\Http\Controllers\Aftap\PengembalianKantongController::class, 'store'])      ->name('store');
        Route::get('/{id}',            [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'show'])       ->name('show');
        Route::get('/{id}/edit',       [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'edit'])       ->name('edit');
        Route::put('/{id}',            [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'update'])     ->name('update');
        Route::delete('/{id}',         [App\Http\Controllers\Aftap\PengembalianKantongController::class, 'destroy'])    ->name('destroy');
    });
    Route::prefix('pengiriman_sample')->name('pengiriman_sample.')->group(function () {

        Route::get('/',    [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'index'])  ->name('index');
        Route::post('/scan',  [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'scan'])   ->name('scan');
        Route::post('/store', [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'store'])  ->name('store');
        Route::post('/{pengirimanSample}/kirim_fpd', [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'kirimFpd'])->name('kirim_fpd');
        Route::get('/{pengirimanSample}',    [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'show'])    ->name('show');
        Route::put('/{pengirimanSample}',    [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'update'])  ->name('update');
        Route::delete('/{pengirimanSample}', [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'destroy']) ->name('destroy');
        Route::post('/detail/{detail}/tolak', [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'toggleTolak'])->name('detail.tolak');

    });
    
    Route::prefix('pengeluaran_mobile_unit')->name('pengeluaran_mobile_unit.')->group(function () {

            Route::get('/',   [App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'index'])->name('index');
            Route::post('/',  [App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'store'])->name('store');
            Route::post('/scan-kantong',[App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'scanKantong'])->name('scan-kantong');
            Route::delete('/remove-kantong', [App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'removeKantong'])->name('remove-kantong');
            Route::get('/{id}/edit',[App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'edit'])->name('edit');
            Route::put('/{id}',     [App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'update'])->name('update');
            Route::delete('/{id}',  [App\Http\Controllers\Aftap\PengeluaranKantongMobileUnitController::class,'destroy'])->name('destroy');
        });

     Route::prefix('penyisihan_kantong_aftap')->name('penyisihan_kantong_aftap.')->group(function () {
        Route::get('/', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'create'])->name('create');
        Route::post('/scan_kantong', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'scanKantong'])->name('scan_kantong');
        Route::post('/', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'store'])->name('store');
        Route::get('/{penyisihan_kantong_aftap}', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'show'])->name('show');
        Route::get('/{penyisihan_kantong_aftap}/edit', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'edit'])->name('edit');
        Route::put('/{penyisihan_kantong_aftap}', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'update'])->name('update');
        Route::delete('/{penyisihan_kantong_aftap}', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'destroy'])->name('destroy');
    
        // Aksi per-baris kantong di dalam grid (dipakai via AJAX di halaman create/edit)
        Route::post('/{penyisihan_kantong_aftap}/detail', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'addDetail'])->name('detail.add');
        Route::put('/detail/{detail}/alasan', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'updateAlasanDetail'])->name('detail.update_alasan');
        Route::delete('/detail/{detail}', [App\Http\Controllers\Aftap\PenyisihanKantongAftapController::class, 'removeDetail'])->name('detail.remove');
    });
     Route::get('riwayat_pengiriman_sample', [App\Http\Controllers\Aftap\PengirimanSampleController::class, 'riwayat'])->name('riwayat_pengiriman_sample');


         Route::get('permintaan_kantong/next-no', [\App\Http\Controllers\Aftap\PermintaanKantongController::class, 'nextNo'])->name('permintaan_kantong.next_no');
         Route::put('permintaan_kantong/{id}', [\App\Http\Controllers\Aftap\PermintaanKantongController::class, 'update'])->name('permintaan_kantong.update');
         Route::get('permintaan_kantong/{id}/edit', [\App\Http\Controllers\Aftap\PermintaanKantongController::class, 'edit'])->name('permintaan_kantong.edit');

        // ioRouteResource('permintaan_kantong', \App\Http\Controllers\Aftap\PermintaanKantongController::class);
  

});