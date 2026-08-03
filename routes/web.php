<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\SlipGajiController;
use App\Http\Controllers\Admin\GajiImportController;
use App\Http\Controllers\Admin\RiwayatSlipController;
use App\Http\Controllers\Pegawai\SlipSayaController;
use App\Http\Controllers\Pegawai\RiwayatSlipController as PegawaiRiwayatSlipController;
use App\Http\Controllers\Pegawai\GantiPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard Default
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role === 'admin' || $user?->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->role === 'pegawai') {
        return redirect()->route('pegawai.dashboard');
    }

    return redirect('/login');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Route yang harus login
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Portal Pegawai
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:pegawai'])
        ->prefix('pegawai')
        ->group(function () {

            Route::get('/dashboard', [
                \App\Http\Controllers\Pegawai\DashboardController::class,
                'index'
            ])->name('pegawai.dashboard');

            Route::get('/slip', [
                \App\Http\Controllers\Pegawai\SlipSayaController::class,
                'index'
            ])->name('pegawai.slip');

            Route::get('/slip/{slip}', [
                \App\Http\Controllers\Pegawai\SlipSayaController::class,
                'show'
            ])->name('pegawai.slip.show');

            Route::get('/slip/{slip}/pdf', [
                \App\Http\Controllers\Pegawai\SlipSayaController::class,
                'pdf'
            ])->name('pegawai.slip.pdf');

            Route::get('/profil', [
                \App\Http\Controllers\Pegawai\ProfilController::class,
                'index'
            ])->name('pegawai.profil');

            Route::get(
                '/riwayat-slip',
                [PegawaiRiwayatSlipController::class, 'index']
            )->name('pegawai.riwayat');

            Route::get('/ganti-password', [
                GantiPasswordController::class,
                'index'
            ])->name('pegawai.ganti-password');

            Route::patch('/ganti-password', [
                GantiPasswordController::class,
                'update'
            ])->name('pegawai.ganti-password.update');

    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:admin'])->group(function () {

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        /*
        |--------------------------------------------------------------------------
        | CRUD Pegawai
        |--------------------------------------------------------------------------
        */

        Route::resource('pegawai', PegawaiController::class);

        /*
        |--------------------------------------------------------------------------
        | CRUD Slip Gaji
        |--------------------------------------------------------------------------
        */

        Route::resource('slip-gaji', SlipGajiController::class);

        Route::get('/slip-gaji/{slip_gaji}/pdf', [SlipGajiController::class, 'pdf'])
            ->name('slip-gaji.pdf');

        Route::get('/import-gaji', [GajiImportController::class, 'create'])
            ->name('gaji-imports.create');

        Route::post('/import-gaji', [GajiImportController::class, 'store'])
            ->name('gaji-imports.store');

        Route::get('/riwayat-slip', [RiwayatSlipController::class, 'index'])
            ->name('admin.riwayat-slip');
    });
});

require __DIR__.'/auth.php';
