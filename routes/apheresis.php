<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.apheresis.index');
Route::name('.')->group(function () {

   
    Route::prefix('donor')->name('donor.')->group(function () {
        // API: generate kode & no_pendaftaran baru
        Route::get('/generate_kode', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'generate_kode'])->name('generate_kode');
        // API: hitung donor_ke dari no_ktp
        Route::get('/get_donor_ke', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'get_donor_ke'])->name('get_donor_ke');

            Route::get('/select2/wilayah',         [App\Http\Controllers\Apheresis\DonorController::class, 'select2Wilayah'])->name('select2.wilayah');
            Route::get('/select2/kecamatan',       [App\Http\Controllers\Apheresis\DonorController::class, 'select2Kecamatan'])->name('select2.kecamatan');
            Route::get('/select2/pekerjaan',       [App\Http\Controllers\Apheresis\DonorController::class, 'select2Pekerjaan'])->name('select2.pekerjaan');
            Route::get('/select2/kewarganegaraan', [App\Http\Controllers\Apheresis\DonorController::class, 'select2Kewarganegaraan'])->name('select2.kewarganegaraan');
            Route::get('/select2/asal-darah',      [App\Http\Controllers\Apheresis\DonorController::class, 'select2AsalDarah'])->name('select2.asal_darah');
            Route::post('/check-fpup',[App\Http\Controllers\Apheresis\DonorController::class, 'checkFpup'])->name('check_fpup');


        });

    Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
        Route::get('/', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'search'])->name('search');
        Route::post('/search_donor', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'search_donor'])->name('search_donor');
        Route::post('/', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Apheresis\PendaftaranController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pemeriksaan_kesehatan')->name('pemeriksaan_kesehatan.')->group(function () {
        Route::get('/', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'index'])->name('index');
        Route::get('/display-antrian', [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data', [App\Http\Controllers\LauncherController::class, 'display_antrian_data']);
        Route::get('/antrian-apheresis', [App\Http\Controllers\LauncherController::class, 'antrian_apheresis'])
            ->name('antrian_apheresis');
        
        Route::get('/antrian-apheresis/data', [App\Http\Controllers\LauncherController::class, 'display_antrian_apheresis_data'])
            ->name('antrian_apheresis.data');
        Route::post('/search', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'search'])->name('search');
        Route::get('/{id}', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'update'])->name('update');
        Route::post('/nomor_ruangan', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'nomor_ruangan'])->name('nomor_ruangan');
        Route::post('/log_donor/search', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil',[App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'panggil'])->name('log_donor.panggil');

        Route::post('/log_donor/{id}/assign_ruangan',[App\Http\Controllers\Apheresis\PemeriksaanKesehatanController::class, 'assign_ruangan'])->name('log_donor.assign_ruangan');

    });

    Route::prefix('pemeriksaan_hb')->name('pemeriksaan_hb.')->group(function () {
        Route::get('/', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'search'])->name('search');
        
        Route::get('/display-antrian',       [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('display_antrian');
        Route::get('/display-antrian/data',  [App\Http\Controllers\LauncherController::class, 'display_antrian_data'])->name('display_antrian_data');

        Route::post('/log_donor/search', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'log_donor_search'])->name('log_donor.search');
        Route::get('/log_donor/{id}', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'log_donor_show'])->name('log_donor.show');
        Route::post('/log_donor/{id}/panggil', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'panggil'])->name('log_donor.panggil');
        Route::post('/log_donor/{id}/assign_ruangan', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'assign_ruangan'])->name('log_donor.assign_ruangan');

        Route::get('/{id}', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Apheresis\PemeriksaanHbController::class, 'update'])->name('update');
    
   
    });
    Route::prefix('sampling_pra_donor')->name('sampling_pra_donor.')->group(function () {
        Route::get('/', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'search'])->name('search');
 
        Route::get('/create', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'store'])->name('store');
 
        Route::get('/generate_kode', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'generateKode'])->name('generate_kode');
        Route::post('/search_donor', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'searchDonor'])->name('search_donor');
 
        Route::get('/{id}', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Apheresis\SamplingPraDonorController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('pengambilan_darah')->name('pengambilan_darah.')->group(function () {
        Route::get('/', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'index'])->name('index');
        Route::post('/search', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'search'])->name('search');
    
        Route::get('/create', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'store'])->name('store');
    
        Route::get('/generate_kode', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'generateKode'])->name('generate_kode');
        Route::post('/search_donor', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'searchDonor'])->name('search_donor');
        Route::post('/search_sampling', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'searchSampling'])->name('search_sampling');
        Route::get('/search_petugas', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'searchPetugas'])->name('search_petugas');
        Route::get('/{id}', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Apheresis\PengambilanDarahApheresisController::class, 'destroy'])->name('destroy');
    });


    ioRouteResource('donor', App\Http\Controllers\Apheresis\DonorController::class);





});