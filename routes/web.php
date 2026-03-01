<?php

use App\Http\Controllers\DashController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', [DashController::class, 'view_dashboard'])->name('dashboard');
Route::get('/dokter', [DashController::class, 'view_dokter'])->name('dokter');
Route::get('/pasien', [DashController::class, 'view_tabel_pasien'])->name('tabel');
Route::get('/get-data', [DashController::class, 'getData'])->name('getData');
Route::get('/get-jadwal-dokter', [DashController::class, 'getJadwalDokter'])->name('getJadwalDokter');
Route::get('/', [DashController::class, 'view_dashboard'])
    ->name('dashboard');
Route::get('/get-jadwal-dokter-dash', [DashController::class, 'jadwalDokterHariIni'])
    ->name('dokter.hari_ini');
