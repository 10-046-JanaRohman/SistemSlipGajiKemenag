<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\SlipGajiController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [AuthController::class, 'profile']);

});

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/pegawai', [PegawaiController::class, 'index']);
Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);

Route::get('/slip-gaji', [SlipGajiController::class, 'index']);
Route::get('/slip-gaji/{slipGaji}', [SlipGajiController::class, 'show']);
Route::get('/slip-gaji/{slipGaji}/pdf', [SlipGajiController::class, 'pdf']);

Route::get('/riwayat-slip', [SlipGajiController::class, 'riwayat']);
Route::patch('/ganti-password', [AuthController::class, 'gantiPassword']);

Route::get('/profil', [AuthController::class, 'profil']);
