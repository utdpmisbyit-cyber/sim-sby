<?php

use Illuminate\Support\Facades\Route;

Route::view('/','app.finance.index');
Route::name('.')->group(function () {
    ioRouteResource('coa', App\Http\Controllers\Finance\CoaController::class);
    ioRouteResource('anggaran', App\Http\Controllers\Finance\AnggaranController::class);
    ioRouteResource('kas_masuk', App\Http\Controllers\Finance\KasMasukController::class);
    ioRouteResource('kas_keluar', App\Http\Controllers\Finance\KasKeluarController::class);
    ioRouteResource('penyesuaian', App\Http\Controllers\Finance\PenyesuaianController::class);
    ioRouteResource('general_ledge', App\Http\Controllers\Finance\GeneralLedgeController::class);
    ioRouteResource('trial_balance', App\Http\Controllers\Finance\TrialBalanceController::class);
    ioRouteResource('buku_besar', App\Http\Controllers\Finance\BukuBesarController::class);


     ioRouteResource('pembayaran_dropping_external', App\Http\Controllers\Finance\Kasir\PembayaranDroppingExternalController::class);

    Route::post('pembayaran_dropping_external/cari-kiriman', [App\Http\Controllers\Finance\Kasir\PembayaranDroppingExternalController::class, 'cariKiriman'])
        ->name('pembayaran_dropping_external.cari_kiriman');


        
    Route::prefix('pelayanan_darah')->name('pelayanan_darah.')->group(function () {

        Route::get('api/next-no-pelayanan',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'nextNoPelayanan'])->name('next-no-pelayanan');
        Route::get('api/scan-pemberian',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'scanPemberian'])->name('scan-pemberian');
        Route::get('api/jenis-biaya',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'jenisBiayaList'])->name('jenis-biaya-list');
        Route::get('/',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'index'])->name('index');
        Route::post('/',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'store'])->name('store');
        Route::patch('/{pelayananDarah}/status',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'updateStatus'])->name('update-status');
        Route::put('/{pelayananDarah}',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'update'])->name('update');
        Route::delete('/{pelayananDarah}',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'destroy'])->name('destroy');
        Route::get('/{pelayananDarah}',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'show'])->name('show');
        Route::get('api/harga-satuan',[App\Http\Controllers\Finance\Kasir\PelayananDarahController::class, 'hargaSatuan'])->name('harga-satuan');
    });

   Route::prefix('pengembalian_biaya_crosstest')->name('pengembalian_biaya_crosstest.')->group(function () {
 
        Route::get('api/next-no-retur',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'nextNoRetur'])->name('next-no-retur');
        Route::get('api/scan-fpup',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'scanFpup'])->name('scan-fpup');
        Route::get('api/jenis-biaya',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'jenisBiayaList'])->name('jenis-biaya-list');
        Route::get('api/harga-satuan',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'hargaSatuan'])->name('harga-satuan');
        Route::get('api/cari-kasir',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'searchKasir'])->name('cari-kasir');
 
        Route::get('/',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'index'])->name('index');
        Route::get('/create',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'create'])->name('create');
        Route::post('/',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'store'])->name('store');
        Route::get('/{pengembalianBiayaCrosstest}',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'show'])->name('show');
        Route::get('/{pengembalianBiayaCrosstest}/edit',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'edit'])->name('edit');
        Route::put('/{pengembalianBiayaCrosstest}',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'update'])->name('update');
        Route::patch('/{pengembalianBiayaCrosstest}/status',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{pengembalianBiayaCrosstest}',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'destroy'])->name('destroy');
        Route::get('/{pengembalianBiayaCrosstest}/print',[App\Http\Controllers\Finance\Kasir\PengembalianBiayaCrosstestController::class, 'print'])->name('print');
    });


});
Route::prefix('laporan')->name('.laporan.')->group(function () {
    ioRouteResource('posisi_keuangan', App\Http\Controllers\Finance\Laporan\PosisiKeuanganController::class);
    ioRouteResource('aset_netto', App\Http\Controllers\Finance\Laporan\AsetNettoController::class);
    ioRouteResource('arus_kas', App\Http\Controllers\Finance\Laporan\ArusKasController::class);

    Route::post('posisi_keuangan/search-json', [App\Http\Controllers\Finance\Laporan\PosisiKeuanganController::class, 'searchJson'])->name('posisi_keuangan.search_json');
    Route::get('aset_netto/search', [App\Http\Controllers\Finance\Laporan\AsetNettoController::class, 'search'])->name('aset_netto.search');
});
