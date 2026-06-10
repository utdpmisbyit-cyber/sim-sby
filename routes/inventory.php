<?php

use Illuminate\Support\Facades\Route;

Route::view('/','app.inventory.index');
Route::name('.')->group(function () {
    ioRouteResource('pengajuan_barang', App\Http\Controllers\Inventory\PermintaanController::class);
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
});
