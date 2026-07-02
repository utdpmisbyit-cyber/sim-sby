<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.gudang.index');
Route::name('.')->group(function () {

    Route::get('pendataan_kantong/next-seq',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'nextSeq'])->name('pendataan_kantong.next-seq');
    Route::post('pendataan_kantong/check-duplicate',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'checkDuplicate'])->name('pendataan_kantong.check-duplicate'); // ← BARU
    Route::post('pendataan_kantong/store-batch',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'storeBatch'])->name('pendataan_kantong.store-batch');
    Route::post('pendataan-kantong/print-direct',[App\Http\Controllers\Gudang\PendataanKantongController::class, 'printDirect'])->name('pendataan_kantong.print-direct');
    Route::get('stok_kantong/find',[App\Http\Controllers\Gudang\StokKantongController::class, 'find'])->name('stok_kantong.find');
    Route::get('stok_kantong/find-keluar',[App\Http\Controllers\Gudang\StokKantongController::class, 'findKeluar'])->name('stok_kantong.find_keluar');
    Route::get('stok_kantong/summary',[App\Http\Controllers\Gudang\StokKantongController::class, 'summary'])->name('stok_kantong.summary');
    Route::post('stok_kantong/save',[App\Http\Controllers\Gudang\StokKantongController::class, 'save'])->name('stok_kantong.save');
    Route::post('stok_kantong/save-kembali',[App\Http\Controllers\Gudang\StokKantongController::class, 'saveKembali'])->name('stok_kantong.save_kembali');
    Route::get('stok_kantong/list',[App\Http\Controllers\Gudang\StokKantongController::class, 'list'])->name('stok_kantong.list');
    Route::get('pengeluaran_kantong/find',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'find'])->name('pengeluaran_kantong.find');
    Route::post('pengeluaran_kantong/save',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'save'])->name('pengeluaran_kantong.save');
    Route::get('pengeluaran_kantong/list',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'list'])->name('pengeluaran_kantong.list');
    Route::get('permintaan_kantong/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'getPermintaan']);
    Route::get('pengeluaran_kantong/get/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'show']);
    Route::delete('pengeluaran_kantong/delete/{id}',[App\Http\Controllers\Gudang\PengeluaranKantongController::class, 'delete']);
    Route::prefix('cetak_barcode')->name('cetak_barcode.')->group(function () {
        Route::get('/',     [App\Http\Controllers\Gudang\CetakUlangBarcodeController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Gudang\CetakUlangBarcodeController::class, 'data'])->name('data');
         Route::get('/approved', [App\Http\Controllers\Gudang\CetakUlangBarcodeController::class, 'approvedRequests'])->name('approved'); 
    });
    Route::prefix('permintaan_cetak_ulang')->name('permintaan_cetak_ulang.')->group(function () {
        Route::get('find-barcode',  [App\Http\Controllers\Gudang\PermintaanCetakUlangController::class, 'findBarcode'])->name('find_barcode');
        Route::post('{id}/approve', [App\Http\Controllers\Gudang\PermintaanCetakUlangController::class, 'approve'])->name('approve');
        Route::post('{id}/reject',  [App\Http\Controllers\Gudang\PermintaanCetakUlangController::class, 'reject'])->name('reject');
        Route::post('{id}/selesai', [App\Http\Controllers\Gudang\PermintaanCetakUlangController::class, 'selesai'])->name('selesai');
    });

      Route::prefix('riwayat_barcode')->name('riwayat_barcode.')->group(function () {
        Route::get('/',        [App\Http\Controllers\Gudang\RiwayatBarcodeController::class, 'index'])->name('index');
        Route::get('/data',    [App\Http\Controllers\Gudang\RiwayatBarcodeController::class, 'data'])->name('data');
        Route::get('/summary', [App\Http\Controllers\Gudang\RiwayatBarcodeController::class, 'summary'])->name('summary');
        });
      Route::get('permintaan_barang_logistik/get-pengajuan/{id}', [App\Http\Controllers\Gudang\PermintaanBarangLogistikController::class, 'getPengajuan'])->name('permintaan_barang_logistik.get_pengajuan');
    Route::get('permintaan_barang_logistik/find-pengajuan', [App\Http\Controllers\Gudang\PermintaanBarangLogistikController::class, 'findPengajuan'])->name('permintaan_barang_logistik.find_pengajuan');


    ioRouteResource('pendataan_kantong',   App\Http\Controllers\Gudang\PendataanKantongController::class);
    ioRouteResource('stok_kantong',        App\Http\Controllers\Gudang\StokKantongController::class);
    ioRouteResource('pengeluaran_kantong', App\Http\Controllers\Gudang\PengeluaranKantongController::class);
    ioRouteResource('konfirmasi_permintaan', App\Http\Controllers\Gudang\KonfirmasiPermintaanController::class);
    // ioRouteResource('pengajuan_barang', App\Http\Controllers\Gudang\PermintaanController::class);
    ioRouteResource('permintaan_cetak_ulang', App\Http\Controllers\Gudang\PermintaanCetakUlangController::class);
    ioRouteResource('permintaan_barang_logistik', App\Http\Controllers\Gudang\PermintaanBarangLogistikController::class);
    
});