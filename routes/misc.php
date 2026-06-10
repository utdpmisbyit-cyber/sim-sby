<?php

use Illuminate\Support\Facades\Route;

 Route::view('/', 'app.misc.index');
    Route::name('.')->group(function () {

        Route::get('permintaan_kantong/next-no', [\App\Http\Controllers\Misc\PermintaanKantongController::class, 'nextNo'])
            ->name('permintaan_kantong.next_no');
         Route::put('permintaan_kantong/{id}', [\App\Http\Controllers\Misc\PermintaanKantongController::class, 'update'])
        ->name('permintaan_kantong.update');

        Route::get('permintaan_kantong/{id}/edit', [\App\Http\Controllers\Misc\PermintaanKantongController::class, 'edit'])
            ->name('permintaan_kantong.edit');

        ioRouteResource('permintaan_kantong', \App\Http\Controllers\Misc\PermintaanKantongController::class);
    });
