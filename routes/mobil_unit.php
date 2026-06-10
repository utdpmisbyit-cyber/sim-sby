<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.mobil_unit.index');
Route::name('.')->group(function () {

   Route::prefix('pendaftaran_mobil')->name('pendaftaran_mobil.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PendaftaranMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\PendaftaranMobilController::class, 'search'])->name('search');
        Route::post('/search_donor', [App\Http\Controllers\Unit\PendaftaranMobilController::class, 'search_donor'])->name('search_donor');
        Route::post('/', [App\Http\Controllers\Unit\PendaftaranMobilController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\Unit\PendaftaranMobilController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pemeriksaan_mobil')->name('pemeriksaan_mobil.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'search'])->name('search');
        Route::get('/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'update'])->name('update');
        Route::post('/log_donor/search', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilController::class, 'log_donor_show'])->name('log_donor.show');
    });
    
     Route::prefix('pemeriksaan_mobil_hb')->name('pemeriksaan_mobil_hb.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'search'])->name('search');   
        Route::get('/display-antrian',       [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data',  [App\Http\Controllers\LauncherController::class, 'display_antrian_data'])->name('display_antrian_data');
        
        Route::post('/log_donor/search', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'panggil'])->name('log_donor.panggil');
        Route::post('/log_donor/{id}/assign_ruangan', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'assign_ruangan'])->name('log_donor.assign_ruangan');

        Route::get('/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\PemeriksaanMobilHbController::class, 'update'])->name('update');
    
   
    });
     Route::prefix('aftap')->name('aftap.')->group(function () {
        Route::get('/', [App\Http\Controllers\Unit\AftapMobilController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Unit\AftapMobilController::class, 'search'])->name('search');
        Route::post('/log_donor/search', [App\Http\Controllers\Unit\AftapMobilController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Unit\AftapMobilController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\Unit\AftapMobilController::class, 'panggil'])->name('log_donor.panggil');

        Route::get('/display-antrian', [App\Http\Controllers\Unit\AftapMobilController::class, 'display_antrian'])->name('display_antrian');
        Route::get('/display-antrian/data', [App\Http\Controllers\Unit\AftapMobilController::class, 'display_antrian_data'])->name('display_antrian_data');
        
        Route::post('/scan_kantong', [App\Http\Controllers\Unit\AftapMobilController::class, 'scan_kantong'])->name('scan_kantong');
        Route::get('/{id}', [App\Http\Controllers\Unit\AftapMobilController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Unit\AftapMobilController::class, 'update'])->name('update');
       
    });

 Route::prefix('permintaan_mobil_unit')->name('permintaan_mobil_unit.')->group(function () {
 
        Route::get('/generate-nomor', [App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'generateNomor'])->name('generate_nomor');
        Route::get('/',          [App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'index'])->name('index');
        Route::get('/create',    [App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'create'])->name('create');
        Route::post('/',         [App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'store'])->name('store');
        Route::get('/{permintaanMobileUnit}',[App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'show'])->name('show');
        Route::get('/{permintaanMobileUnit}/edit',[App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'edit'])->name('edit');
        Route::put('/{permintaanMobileUnit}',[App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'update'])->name('update');
        Route::delete('/{permintaanMobileUnit}',[App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'destroy'])->name('destroy');
        Route::patch('/{permintaanMobileUnit}/flag',[App\Http\Controllers\Unit\PermintaanMobileUnitController::class, 'updateFlag'])->name('update_flag');
    });




});