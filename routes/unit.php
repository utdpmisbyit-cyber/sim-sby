<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.unit.index');
Route::name('.')->group(function () {

   
    Route::prefix('donor')->name('donor.')->group(function () {
        // API: generate kode & no_pendaftaran baru
        Route::get('/generate_kode', [App\Http\Controllers\Unit\PendaftaranController::class, 'generate_kode'])->name('generate_kode');
        // API: hitung donor_ke dari no_ktp
        Route::get('/get_donor_ke', [App\Http\Controllers\Unit\PendaftaranController::class, 'get_donor_ke'])->name('get_donor_ke');

            Route::get('/select2/wilayah',         [App\Http\Controllers\Unit\DonorController::class, 'select2Wilayah'])->name('select2.wilayah');
            Route::get('/select2/kecamatan',       [App\Http\Controllers\Unit\DonorController::class, 'select2Kecamatan'])->name('select2.kecamatan');
            Route::get('/select2/pekerjaan',       [App\Http\Controllers\Unit\DonorController::class, 'select2Pekerjaan'])->name('select2.pekerjaan');
            Route::get('/select2/kewarganegaraan', [App\Http\Controllers\Unit\DonorController::class, 'select2Kewarganegaraan'])->name('select2.kewarganegaraan');
            Route::get('/select2/asal-darah',      [App\Http\Controllers\Unit\DonorController::class, 'select2AsalDarah'])->name('select2.asal_darah');
            Route::post(
            '/check-fpup',
            [App\Http\Controllers\Unit\DonorController::class, 'checkFpup'])->name('check_fpup');


        });

    ioRouteResource('donor', App\Http\Controllers\Unit\DonorController::class);

    Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PendaftaranController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\PendaftaranController::class, 'search'])->name('search');
        Route::post('/search_donor', [App\Http\Controllers\Unit\PendaftaranController::class, 'search_donor'])->name('search_donor');
        Route::post('/', [App\Http\Controllers\Unit\PendaftaranController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Unit\PendaftaranController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\PendaftaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Unit\PendaftaranController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pemeriksaan_kesehatan')->name('pemeriksaan_kesehatan.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'index'])->name('index');
        Route::get('/display-antrian', [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data', [App\Http\Controllers\LauncherController::class, 'display_antrian_data']);
    
        Route::post('/search', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'search'])->name('search');
        Route::get('/{id}', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'update'])->name('update');
        Route::post('/nomor_ruangan', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'nomor_ruangan'])->name('nomor_ruangan');
        Route::post('/log_donor/search', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil',
        [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'panggil']
        )->name('log_donor.panggil');

        Route::post('/log_donor/{id}/assign_ruangan',
            [App\Http\Controllers\Unit\PemeriksaanKesehatanController::class, 'assign_ruangan']
        )->name('log_donor.assign_ruangan');

    });

    Route::prefix('pemeriksaan_hb')->name('pemeriksaan_hb.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'search'])->name('search');
        
        Route::get('/display-antrian',       [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data',  [App\Http\Controllers\LauncherController::class, 'display_antrian_data'])->name('display_antrian_data');
        
        Route::post('/log_donor/search', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'panggil'])->name('log_donor.panggil');
        Route::post('/log_donor/{id}/assign_ruangan', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'assign_ruangan'])->name('log_donor.assign_ruangan');

        Route::get('/{id}', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\PemeriksaanHbController::class, 'update'])->name('update');
    
   
    });

 
    
   



        
    Route::prefix('bank_darah')->name('bank_darah.')->group(function () {

       
        Route::prefix('pemberian_darah/api')->name('pemberian_darah.')->group(function () {
            Route::get('scan-fpup',  [App\Http\Controllers\Unit\PemberianDarahController::class, 'scanFpup'])  ->name('scan-fpup');
            Route::get('scan-stok',  [App\Http\Controllers\Unit\PemberianDarahController::class, 'scanStok'])  ->name('scan-stok');
            Route::post('/{pemberian_darah}/export-dropping', [App\Http\Controllers\Unit\PemberianDarahController::class, 'exportDropping'])->name('export-dropping');
        });
 
    
        Route::prefix('pemberian_darah')->name('pemberian_darah.')->group(function () {
            Route::get('/',                                [App\Http\Controllers\Unit\PemberianDarahController::class, 'index'])   ->name('index');
            Route::get('/create',                          [App\Http\Controllers\Unit\PemberianDarahController::class, 'create'])  ->name('create');
            Route::post('/',                               [App\Http\Controllers\Unit\PemberianDarahController::class, 'store'])   ->name('store');
            Route::get('/{pemberian_darah}/edit',          [App\Http\Controllers\Unit\PemberianDarahController::class, 'edit'])    ->name('edit');
            Route::put('/{pemberian_darah}',               [App\Http\Controllers\Unit\PemberianDarahController::class, 'update'])  ->name('update');
            Route::delete('/{pemberian_darah}',            [App\Http\Controllers\Unit\PemberianDarahController::class, 'destroy']) ->name('destroy');
            Route::get('/{pemberian_darah}',               [App\Http\Controllers\Unit\PemberianDarahController::class, 'show'])    ->name('show');
        });
    

    });

   
   
});