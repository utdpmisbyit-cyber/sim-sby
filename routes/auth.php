<?php

use Illuminate\Support\Facades\Route;

Route::get('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('login', [App\Http\Controllers\AuthController::class, 'login_process'])->name('login.process');
Route::get('logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
