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

        Route::prefix('permintaan_fpup/api')->name('permintaan_fpup.')->group(function () {
            Route::get('next-no-fpup', [App\Http\Controllers\Unit\PermintaanFpupController::class, 'nextNoFpup'])
                ->name('next-no-fpup');
            Route::get('search-rs',   [App\Http\Controllers\Unit\PermintaanFpupController::class, 'searchRs'])
                ->name('search-rs');
            Route::get('rs-by-kode',  [App\Http\Controllers\Unit\PermintaanFpupController::class, 'rsByKode'])
                ->name('rs-by-kode');
            Route::get('diagnosa',    [App\Http\Controllers\Unit\PermintaanFpupController::class, 'listDiagnosa'])
                ->name('diagnosa');
        });

        Route::prefix('permintaan_fpup')->name('permintaan_fpup.')->group(function () {
            Route::get('/',                               [App\Http\Controllers\Unit\PermintaanFpupController::class, 'index'])        ->name('index');
            Route::get('/create',                         [App\Http\Controllers\Unit\PermintaanFpupController::class, 'create'])       ->name('create');
            Route::post('/',                              [App\Http\Controllers\Unit\PermintaanFpupController::class, 'store'])        ->name('store');
            Route::get('/permintaan-fpup/{id}/barcode',   [App\Http\Controllers\Unit\PermintaanFpupController::class, 'barcode'])->name('barcode');
            Route::get('/{permintaan_fpup}/edit',         [App\Http\Controllers\Unit\PermintaanFpupController::class, 'edit'])         ->name('edit');
            Route::put('/{permintaan_fpup}',              [App\Http\Controllers\Unit\PermintaanFpupController::class, 'update'])       ->name('update');
            Route::patch('/{permintaan_fpup}/status',     [App\Http\Controllers\Unit\PermintaanFpupController::class, 'updateStatus']) ->name('update-status');
            Route::delete('/{permintaan_fpup}',           [App\Http\Controllers\Unit\PermintaanFpupController::class, 'destroy'])      ->name('destroy');
            Route::get('/{permintaan_fpup}',              [App\Http\Controllers\Unit\PermintaanFpupController::class, 'show'])         ->name('show');
        });
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
    

Route::prefix('pelayanan_darah')->name('pelayanan_darah.')->group(function () {

    // ── API (harus di atas /{id} agar tidak konflik) ──────────────────────────
    Route::get('api/next-no-pelayanan',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'nextNoPelayanan'])
        ->name('next-no-pelayanan');

    Route::get('api/scan-pemberian',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'scanPemberian'])
        ->name('scan-pemberian');

    // Tambahan: daftar jenis biaya untuk dropdown (opsional, sudah di-pass via index)
    Route::get('api/jenis-biaya',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'jenisBiayaList'])
        ->name('jenis-biaya-list');

    // ── CRUD ──────────────────────────────────────────────────────────────────
    Route::get('/',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'index'])
        ->name('index');

    Route::post('/',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'store'])
        ->name('store');

    Route::patch('/{pelayananDarah}/status',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'updateStatus'])
        ->name('update-status');

    Route::put('/{pelayananDarah}',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'update'])
        ->name('update');

    Route::delete('/{pelayananDarah}',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'destroy'])
        ->name('destroy');

    // PENTING: /{id} harus paling bawah agar tidak menangkap 'api/*'
    Route::get('/{pelayananDarah}',
        [App\Http\Controllers\Unit\PelayananDarahController::class, 'show'])
        ->name('show');
});
 



    });

    Route::prefix('permintaan_darah_penyimpanan')->name('permintaan_darah_penyimpanan.')->group(function () {
        Route::get('next-no-permintaan', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'nextNoPermintaan'])->name('next-no');
        Route::get('search-bank-darah', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'searchBankDarah'])->name('search-bank-darah');
        Route::get('/', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'index'])->name('index');
        Route::get('jenis-darah',[App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'getJenisDarah']);
        Route::get('search-fpup', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'searchFpup']);
        Route::get('detail-fpup/{no_fpup}',[App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'detailFpup']);
        
        Route::post('/', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}', [App\Http\Controllers\Unit\PermintaanDarahPenyimpananController::class, 'destroy'])->name('destroy');
    });
   
});