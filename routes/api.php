<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\SlipGajiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GajiImportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\NotifikasiController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/slip-gaji/{slipGaji}/pdf-download', [SlipGajiController::class, 'pdfDownload'])
    ->middleware('signed:relative')
    ->name('slip-gaji.pdf-download');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/search', GlobalSearchController::class);
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::patch('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead']);
    Route::patch('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'markAsRead']);

    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::post('/pegawai', [PegawaiController::class, 'store']);
    Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);
    Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
    Route::patch('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
    Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy']);

    Route::get('/slip-gaji', [SlipGajiController::class, 'index']);
    Route::get('/slip-gaji/{slipGaji}', [SlipGajiController::class, 'show']);
    Route::get('/slip-gaji/{slipGaji}/pdf-url', [SlipGajiController::class, 'pdfUrl']);
    Route::get('/slip-gaji/{slipGaji}/pdf', [SlipGajiController::class, 'pdf']);

    Route::get('/riwayat-slip', [SlipGajiController::class, 'riwayat']);
    Route::patch('/ganti-password', [AuthController::class, 'gantiPassword']);
    Route::get('/profil', [AuthController::class, 'profil']);

    Route::get('/import-gaji', [GajiImportController::class, 'index']);
    Route::post('/import-gaji/preview', [GajiImportController::class, 'preview']);
    Route::delete('/import-gaji/preview', [GajiImportController::class, 'cancelReview']);
    Route::post('/import-gaji/reviewed', [GajiImportController::class, 'importReviewed']);
    Route::post('/import-gaji', [GajiImportController::class, 'store']);

    Route::get('/settings', [SettingController::class, 'show']);
    Route::patch('/settings', [SettingController::class, 'update']);

    Route::get('/pengumuman', [PengumumanController::class, 'index']);
    Route::post('/pengumuman', [PengumumanController::class, 'store']);
    Route::put('/pengumuman/{pengumuman}', [PengumumanController::class, 'update']);
    Route::delete('/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy']);
});
