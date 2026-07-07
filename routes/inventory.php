<?php

use Illuminate\Support\Facades\Route;

Route::view('/','app.inventory.index');
Route::name('.')->group(function () {

    Route::get('permintaan_barang_logistik/get-pengajuan/{id}', [App\Http\Controllers\Inventory\PermintaanBarangLogistikController::class, 'getPengajuan'])->name('permintaan_barang_logistik.get_pengajuan');
    Route::get('permintaan_barang_logistik/find-pengajuan', [App\Http\Controllers\Inventory\PermintaanBarangLogistikController::class, 'findPengajuan'])->name('permintaan_barang_logistik.find_pengajuan');
    Route::get('konfirmasi_pengembalian_barang/select-barang', [App\Http\Controllers\Inventory\PengembalianBarangController::class, 'selectBarang',])->name('konfirmasi_pengembalian_barang.select_barang');
 
// CRUD standar (index, create, store, show, edit, update, destroy)
    ioRouteResource('konfirmasi_pengembalian_barang', App\Http\Controllers\Inventory\PengembalianBarangController::class);
    ioRouteResource('barang', App\Http\Controllers\Inventory\BarangController::class);
    ioRouteResource('kelompok_barang', App\Http\Controllers\Inventory\KelompokBarangController::class);
    ioRouteResource('supplier', App\Http\Controllers\Inventory\SupplierController::class);
    ioRouteResource('stok', App\Http\Controllers\Inventory\StokController::class);
    ioRouteResource('purchase_order', App\Http\Controllers\Inventory\PurchaseOrderController::class);
    ioRouteResource('QC_barang_masuk', App\Http\Controllers\Inventory\QcBarangMasukController::class);
    ioRouteResource('permintaan_supplier', App\Http\Controllers\Inventory\PermintaanSupplierController::class);
    ioRouteResource('return_supplier', App\Http\Controllers\Inventory\ReturSupplierController::class);
    ioRouteResource('pengeluaran_barang', App\Http\Controllers\Inventory\PengeluaranBarangController::class);
    ioRouteResource('pemakaian_barang', App\Http\Controllers\Inventory\PemakaianBarangController::class);
    ioRouteResource('pinjam_barang', App\Http\Controllers\Inventory\PinjamBarangController::class);
    ioRouteResource('retur_pinjam', App\Http\Controllers\Inventory\ReturPinjamController::class);
    ioRouteResource('opname_barang', App\Http\Controllers\Inventory\OpnameBarangController::class);
    ioRouteResource('permintaan_barang_logistik', App\Http\Controllers\inventory\PermintaanBarangLogistikController::class);

});
