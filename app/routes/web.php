<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\LauncherController::class, 'index'])->name('/');
    Route::get('antrian_dokter', [App\Http\Controllers\LauncherController::class, 'antrian_dokter'])->name('antrian_dokter');

    Route::middleware('io')->group(function () {
        Route::name('master')->prefix('master')->group(__DIR__ . '/master.php');
        Route::name('unit')->prefix('unit')->group(__DIR__ . '/unit.php');
        Route::name('mobil_unit')->prefix('mobil_unit')->group(__DIR__ . '/mobil_unit.php');
        Route::name('kantong_darah')->prefix('kantong_darah')->group(__DIR__ . '/kantong_darah.php');
        Route::name('inventory')->prefix('inventory')->group(__DIR__ . '/inventory.php');
        Route::name('finance')->prefix('finance')->group(__DIR__ . '/finance.php');
        Route::name('serologi')->prefix('serologi')->group(__DIR__ . '/serologi.php');
        Route::name('gudang')->prefix('gudang')->group(__DIR__ . '/gudang.php');
        Route::name('misc')->prefix('misc')->group(__DIR__ . '/misc.php');
        Route::name('aftap')->prefix('aftap')->group(__DIR__ . '/aftap.php');
        Route::name('produksi')->prefix('produksi')->group(__DIR__ . '/produksi.php');
        Route::name('penyimpanan')->prefix('penyimpanan')->group(__DIR__ . '/penyimpanan.php');
        Route::name('crossmatch')->prefix('crossmatch')->group(__DIR__ . '/crossmatch.php');
        Route::name('referal')->prefix('referal')->group(__DIR__ . '/referal.php');
        Route::name('apheresis')->prefix('apheresis')->group(__DIR__ . '/apheresis.php');


    });

    Route::get('pilih_cabang/{cabang_id}', function ($cabang_id) {
        if ($cabang_id === 'all') {
            session()->forget('active_cabang');
            return redirect()->back()->with('success', 'Diubah jadi semua cabang');
        } else {
            $cabangService = new App\Services\CabangService();
            $cabang = $cabangService->find($cabang_id);
            if (empty($cabang)) $branch = auth()->user()->petugas->cabang ?? [];
            if (empty($cabang)) abort(404);
            session(['active_cabang' => $cabang]);
            return redirect()->back()->with('success', 'Diubah jadi cabang ' . $cabang->nama);
        }
    });
    Route::get('/purchase_order/{id}/show_json', [App\Http\Controllers\Inventory\QcBarangMasukController::class, 'show_json'])->name('purchase_order.show_json');
    // Route::post('/pengajuan_barang/{id}/proses', [App\Http\Controllers\Inventory\PermintaanController::class, 'proses'])->name('pengajuan_barang.proses');
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('pengeluaran_barang', [App\Http\Controllers\Inventory\PengeluaranBarangController::class, 'laporan'])->name('pengeluaran_barang');
    });
});

Route::get('check', function () {
    dd(\Illuminate\Support\Str::plural('donor'));
});
