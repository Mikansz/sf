<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\LemburController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Karyawan routes - HRD & CEO only for CRUD
    Route::resource('karyawan', KaryawanController::class);
    
    // Absensi routes - All users
    Route::resource('absensi', AbsensiController::class);
    Route::post('absensi/{absensi}/clock-out', [AbsensiController::class, 'clockOut'])->name('absensi.clock-out');
    
    // Penggajian routes
    Route::resource('penggajian', PenggajianController::class)->except(['edit', 'update', 'destroy']);
    Route::post('penggajian/generate-bulk', [PenggajianController::class, 'generateBulk'])->name('penggajian.generate-bulk');
    Route::post('penggajian/{penggajian}/approve', [PenggajianController::class, 'approve'])->name('penggajian.approve');
    Route::post('penggajian/{penggajian}/pay', [PenggajianController::class, 'pay'])->name('penggajian.pay');
    Route::get('penggajian/{penggajian}/slip', [PenggajianController::class, 'slipGaji'])->name('penggajian.slip');
    
    // Tunjangan routes - HRD & CEO only
    Route::resource('tunjangan', TunjanganController::class);
    
    // Lembur routes
    Route::resource('lembur', LemburController::class);
    Route::post('lembur/{lembur}/approve', [LemburController::class, 'approve'])->name('lembur.approve');
    Route::post('lembur/{lembur}/reject', [LemburController::class, 'reject'])->name('lembur.reject');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
