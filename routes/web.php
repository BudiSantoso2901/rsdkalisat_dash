<?php

use App\Http\Controllers\DashController;
use App\Http\Controllers\ExportController;
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
Route::get('/kunjungan-poli', [DashController::class, 'view_kunjungan_poli'])->name('kunjungan_poli');
Route::get('/get-data-poli', [DashController::class, 'getKunjunganPoli'])->name('getDataPoli');
Route::get('/get-jadwal-dokter', [DashController::class, 'getJadwalDokter'])->name('getJadwalDokter');
Route::get('/kunjungan-poli/{registrationId}/riwayat-soap', [DashController::class, 'getRiwayatSoap'])->name('kunjungan-poli.riwayat-soap');
Route::get('/kunjungan-poli/{registrationId}/riwayat-erm',[DashController::class, 'getRiwayatErm'])->name('kunjungan-poli.riwayat-erm');
Route::get('/kunjungan-poli/{registrationId}/riwayat-erm/{documentId}',[DashController::class, 'getDetailDokumenErm'])->name('kunjungan-poli.detail-erm');
// Route::get('/', [DashController::class, 'view_dashboard'])
//     ->name('dashboard');
Route::get('/', [DashController::class, 'jadwalDokterHariIni'])
    ->name('dashboard');
Route::get('/export/excel', [DashController::class, 'exportExcel'])->name('export.excel');
