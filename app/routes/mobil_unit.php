<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.mobil_unit.index');
Route::name('.')->group(function () {

        Route::prefix('donor')->name('donor.')->group(function () {
       
        Route::get('/generate_kode', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'generate_kode'])->name('generate_kode');
        // API: hitung donor_ke dari no_ktp
        Route::get('/get_donor_ke', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'get_donor_ke'])->name('get_donor_ke');

            Route::get('/select2/wilayah',         [App\Http\Controllers\MobilUnit\DonorController::class, 'select2Wilayah'])->name('select2.wilayah');
            Route::get('/select2/kecamatan',       [App\Http\Controllers\MobilUnit\DonorController::class, 'select2Kecamatan'])->name('select2.kecamatan');
            Route::get('/select2/pekerjaan',       [App\Http\Controllers\MobilUnit\DonorController::class, 'select2Pekerjaan'])->name('select2.pekerjaan');
            Route::get('/select2/kewarganegaraan', [App\Http\Controllers\MobilUnit\DonorController::class, 'select2Kewarganegaraan'])->name('select2.kewarganegaraan');
            Route::get('/select2/asal-darah',      [App\Http\Controllers\MobilUnit\DonorController::class, 'select2AsalDarah'])->name('select2.asal_darah');
            Route::post(
            '/check-fpup',
            [App\Http\Controllers\MobilUnit\DonorController::class, 'checkFpup'])->name('check_fpup');


        });


   Route::prefix('pendaftaran_mobil')->name('pendaftaran_mobil.')->group(function () {
        Route::get('/', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'search'])->name('search');
        Route::post('/search_donor', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'search_donor'])->name('search_donor');
        Route::post('/', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\MobilUnit\PendaftaranMobilController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pemeriksaan_mobil')->name('pemeriksaan_mobil.')->group(function () {
        Route::get('/', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'search'])->name('search');
        Route::get('/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'update'])->name('update');
        Route::post('/log_donor/search', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilController::class, 'log_donor_show'])->name('log_donor.show');
    });
    
     Route::prefix('pemeriksaan_mobil_hb')->name('pemeriksaan_mobil_hb.')->group(function () {
        Route::get('/', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'search'])->name('search');   
        Route::get('/display-antrian',       [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data',  [App\Http\Controllers\LauncherController::class, 'display_antrian_data'])->name('display_antrian_data');
        
        Route::post('/log_donor/search', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'panggil'])->name('log_donor.panggil');
        Route::post('/log_donor/{id}/assign_ruangan', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'assign_ruangan'])->name('log_donor.assign_ruangan');

        Route::get('/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\MobilUnit\PemeriksaanMobilHbController::class, 'update'])->name('update');
    
   
    });
     Route::prefix('aftap_mobil')->name('aftap_mobil.')->group(function () {
        Route::get('/', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'search'])->name('search');
        Route::post('/log_donor/search', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'panggil'])->name('log_donor.panggil');

        Route::get('/display-antrian', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'display_antrian'])->name('display_antrian');
        Route::get('/display-antrian/data', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'display_antrian_data'])->name('display_antrian_data');
        
        Route::post('/scan_kantong', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'scan_kantong'])->name('scan_kantong');
        Route::get('/{id}', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\MobilUnit\AftapMobilController::class, 'update'])->name('update');
       
    });

 Route::prefix('permintaan_mobil_unit')->name('permintaan_mobil_unit.')->group(function () {
 
        Route::get('/generate-nomor', [App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'generateNomor'])->name('generate_nomor');
        Route::get('/',          [App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'index'])->name('index');
        Route::get('/create',    [App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'create'])->name('create');
        Route::post('/',         [App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'store'])->name('store');
        Route::get('/{permintaan_mobil_unit}',[App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'show'])->name('show');
        Route::get('/{permintaan_mobil_unit}/edit',[App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'edit'])->name('edit');
        Route::put('/{permintaan_mobil_unit}',[App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'update'])->name('update');
        Route::delete('/{permintaan_mobil_unit}',[App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'destroy'])->name('destroy');
        Route::patch('/{permintaan_mobil_unit}/flag',[App\Http\Controllers\MobilUnit\PermintaanMobileUnitController::class, 'updateFlag'])->name('update_flag');
    });


    ioRouteResource('donor', App\Http\Controllers\MobilUnit\DonorController::class);
   

});