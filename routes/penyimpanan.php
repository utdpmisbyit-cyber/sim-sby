<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.penyimpanan.index');
Route::name('.')->group(function () {


    Route::prefix('stok_darah')->name('stok_darah.')->group(function () {
        Route::get('/',                   [App\Http\Controllers\Penyimpanan\StokDarahInfoController::class, 'index'])->name('index');
        Route::get('/data',               [App\Http\Controllers\Penyimpanan\StokDarahInfoController::class, 'getData'])->name('data');
        Route::get('/summary',            [App\Http\Controllers\Penyimpanan\StokDarahInfoController::class, 'getSummary'])->name('summary');
        Route::get('/aliran/{noStok}',    [App\Http\Controllers\Penyimpanan\StokDarahInfoController::class, 'getAliran'])->name('aliran');
    });


    Route::prefix('penerimaan_prolis')->name('penerimaan_prolis.')->group(function () {
        Route::get('/', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'getData'])->name('data');
        Route::get('/pengiriman/{no}', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'getByNoPengiriman'])->name('pengiriman');
        Route::post('/cek-kapasitas', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'cekKapasitas'])->name('cekKapasitas');
        Route::get('/kapasitas', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'getKapasitas'])->name('kapasitas');
        Route::get('/stok/{no_stok}', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'getByNoStock'])->name('stok');
        Route::post('/', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Penyimpanan\PenerimaanProlisPenyimpananController::class, 'destroy'])->name('destroy');
    });

  
    Route::prefix('pengiriman_bank_darah_internal')->name('pengiriman_bank_darah_internal.')->group(function () {
        Route::get('/', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'getData'])->name('data');
        Route::get('/permintaan', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'getPermintaan'])->name('permintaan');
        Route::get('/permintaan/{id}', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'showPermintaan'])->name('showPermintaan');
        Route::get('/cari_stok', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'cariStok'])->name('cariStok');
        Route::get('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'update']);
        Route::post('/', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanBankDarahInternalController::class, 'destroy'])->name('destroy');
    });

   Route::prefix('permintaan_external')->name('permintaan_external.')->group(function () {
        Route::get('/', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'getData'])->name('data');
 
        Route::get('/next-nomor', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/jenis-darah', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'getJenisDarah'])->name('jenisDarah');
        Route::get('/petugas/search', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'searchPetugas'])->name('petugas.search');
        Route::get('/institusi/search', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'searchInstitusi'])->name('institusi.search');
        Route::get('/jenis-biaya',[App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'getJenisBiaya'])->name('jenisBiaya');
        Route::get('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'destroy'])->name('destroy');
        Route::put('/pemenuhan/{detailId}', [App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'updatePemenuhan'])->name('pemenuhan');
    });

    Route::prefix('pengiriman_darah_external')->name('pengiriman_darah_external.')->group(function () {
 
        Route::get('/', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'getData'])->name('data');
        Route::get('/jenis-biaya',[App\Http\Controllers\Penyimpanan\PermintaanDarahExternalController::class, 'getJenisBiaya'])->name('jenisBiaya');
        Route::get('/next-nomor', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/permintaan', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'getPermintaan'])->name('permintaan');
        Route::get('/cari-stok', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'cariStok'])->name('cariStok');
        Route::post('/', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'update'])->name('update');   
        Route::delete('/{id}', [App\Http\Controllers\Penyimpanan\PengirimanDarahExternalController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pengembalian_darah_external')->name('pengembalian_darah_external.')->group(function () {
    
        Route::get('/',            [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'index'])->name('index');
        Route::get('/data',        [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'getData'])->name('data');
        Route::get('/next-nomor',  [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/cari-stok',   [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'cariStok'])->name('cariStok');
        Route::get('/search-tujuan', [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'searchTujuanDarah'])->name('searchTujuan');
        Route::get('/search-petugas', [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'searchPetugas'])->name('searchPetugas');
        Route::get('/{id}',        [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'show'])->name('show');
        Route::post('/',           [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'store'])->name('store');
        Route::put('/{id}',        [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'update'])->name('update');
        Route::delete('/{id}',     [App\Http\Controllers\Penyimpanan\PengembalianDarahExternalController::class, 'destroy'])->name('destroy');
    });

    
    Route::prefix('penyisihan_darah_rusak')->name('penyisihan_darah_rusak.')->group(function () {
        Route::get('/',             [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'index'])->name('index');
        Route::get('/data',         [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'getData'])->name('data');
        Route::get('/next-nomor',   [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/cari-stok',    [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'cariStok'])->name('cariStok');
        Route::get('/{penyisihanDarahRusak}',    [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'show'])->name('show');
        Route::post('/',            [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'store'])->name('store');
        Route::put('/{penyisihanDarahRusak}',    [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'update'])->name('update');
        Route::put('/{penyisihanDarahRusak}/approve', [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'approve'])->name('approve');
        Route::delete('/{penyisihanDarahRusak}', [App\Http\Controllers\Penyimpanan\PenyisihanDarahRusakController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('opname_darah')->name('opname_darah.')->group(function () {
        Route::get('/',                      [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'index'])->name('index');
        Route::get('/data',                  [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'getData'])->name('data');
        Route::get('/next-nomor',            [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/cari-stok',             [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'cariStok'])->name('cariStok');
        Route::get('/cari-bagian',           [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'cariBagian'])->name('cariBagian');   // ← sebelum /{opnameDarah}
        Route::get('/cari-petugas',          [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'cariPetugas'])->name('cariPetugas'); // ← sebelum /{opnameDarah}

        Route::get('/{opnameDarah}',         [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'show'])->name('show');
        Route::get('/{opnameDarah}/summary', [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'summary'])->name('summary');
        Route::post('/',                     [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'store'])->name('store');
        Route::put('/{opnameDarah}',         [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'update'])->name('update');
        Route::put('/{opnameDarah}/selesai', [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'selesai'])->name('selesai');
        Route::delete('/{opnameDarah}',      [App\Http\Controllers\Penyimpanan\OpnameDarahController::class, 'destroy'])->name('destroy');
    });

   
   Route::prefix('fraksionasi_darah')->name('fraksionasi_darah.')->group(function () {
        Route::get('/',             [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'index'])->name('index');
        Route::get('/data',         [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'getData'])->name('data');
        Route::get('/summary',      [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'getSummary'])->name('summary');
        Route::get('/next-nomor',   [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'nextNomor'])->name('nextNomor');
        Route::get('/cari-stok',    [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'cariStok'])->name('cariStok');
        Route::get('/kantong',      [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'getKantong'])->name('kantong');
        Route::get('/search-petugas', [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'searchPetugas'])->name('searchPetugas');
        Route::get('/{fraksionasiDarah}',          [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'show'])->name('show');
        Route::post('/',                           [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'store'])->name('store');
        Route::put('/{fraksionasiDarah}',          [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'update'])->name('update');
        Route::put('/{fraksionasiDarah}/selesai',  [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'selesai'])->name('selesai');
        Route::delete('/{fraksionasiDarah}',       [App\Http\Controllers\Penyimpanan\FraksionasiDarahController::class, 'destroy'])->name('destroy');
    });

     Route::prefix('permintaan_darah_penyimpanan')->name('permintaan_darah_penyimpanan.')->group(function () {
        Route::get('next-no-permintaan', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'nextNoPermintaan'])->name('next-no');
        Route::get('search-bank-darah', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'searchBankDarah'])->name('search-bank-darah');
        Route::get('/', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'index'])->name('index');
        Route::get('jenis-darah',[App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'getJenisDarah']);
        Route::get('search-fpup', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'searchFpup']);
        Route::get('detail-fpup/{no_fpup}',[App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'detailFpup']);
        
        Route::post('/', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}', [App\Http\Controllers\Penyimpanan\PermintaanDarahPenyimpananController::class, 'destroy'])->name('destroy');
    });



});