<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app.master.index');
Route::name('.')->group(function () {
    ioRouteResource('user', App\Http\Controllers\Master\UserController::class);
    ioRouteResource('jenis_kantong', App\Http\Controllers\Master\JenisKantongController::class);
    ioRouteResource('tipe_kantong', App\Http\Controllers\Master\TipeKantongController::class);

    ioRouteResource('wilayah', App\Http\Controllers\Master\WilayahController::class);
    ioRouteResource('kecamatan', App\Http\Controllers\Master\KecamatanController::class);
    ioRouteResource('kewarganegaraan', App\Http\Controllers\Master\KewarganegaraanController::class);
    ioRouteResource('pekerjaan', App\Http\Controllers\Master\PekerjaanController::class);
    ioRouteResource('jabatan', App\Http\Controllers\Master\JabatanController::class);
    ioRouteResource('bagian_petugas', App\Http\Controllers\Master\BagianPetugasController::class);
    ioRouteResource('cabang', App\Http\Controllers\Master\CabangController::class);
    ioRouteResource('mobil_unit', App\Http\Controllers\Master\MobilUnitController::class);
    ioRouteResource('program_kerja', App\Http\Controllers\Master\ProgramKerjaController::class);
    ioRouteResource('petugas', App\Http\Controllers\Master\PetugasController::class);
    ioRouteResource('hak_akses', App\Http\Controllers\Master\HakAksesController::class);
    ioRouteResource('jenis_darah', App\Http\Controllers\Master\JenisDarahController::class);
    ioRouteResource('asal_darah', App\Http\Controllers\Master\AsalDarahController::class);
    ioRouteResource('tujuan_darah', App\Http\Controllers\Master\TujuanDarahController::class);
    ioRouteResource('kelas_tujuan_darah', App\Http\Controllers\Master\KelasTujuanDarahController::class);
    ioRouteResource('bank_darah', App\Http\Controllers\Master\BankDarahController::class);
    ioRouteResource('bagian_rumah_sakit', App\Http\Controllers\Master\BagianRumahSakitController::class);
    ioRouteResource('kelompok_rumah_sakit', App\Http\Controllers\Master\KelompokRumahSakitController::class);
    ioRouteResource('diagnosa', App\Http\Controllers\Master\DiagnosaController::class);
    ioRouteResource('jenis_periksa_serologi', App\Http\Controllers\Master\JenisPeriksaSerologiController::class);
    ioRouteResource('metode_serologi', App\Http\Controllers\Master\MetodeSerologiController::class);
    ioRouteResource('reagen_serologi', App\Http\Controllers\Master\ReagenSerologiController::class);
    ioRouteResource('jenis_biaya', App\Http\Controllers\Master\JenisBiayaController::class);
    ioRouteResource('kelompok_biaya', App\Http\Controllers\Master\KelompokBiayaController::class);
    ioRouteResource('service_cost', App\Http\Controllers\Master\ServiceCostController::class);
    ioRouteResource('biaya_cross_test', App\Http\Controllers\Master\BiayaCrossTestController::class);
    ioRouteResource('kelompok_barang', App\Http\Controllers\Master\KelompokBarangController::class);
    ioRouteResource('barang', App\Http\Controllers\Master\BarangController::class);
});
