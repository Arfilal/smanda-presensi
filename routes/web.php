<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\KesiswaanController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\GuruMapelController;
use App\Http\Controllers\SiswaController;


// ======================================================
// ROUTE TAMU / LOGIN
// ======================================================

Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// ======================================================
// ROUTE YANG MEMBUTUHKAN LOGIN
// ======================================================

Route::middleware(['auth'])->group(function () {

    // ==================================================
    // PORTAL SISWA
    // ==================================================

    Route::middleware(['role:siswa'])
        ->prefix('siswa')
        ->name('siswa.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [SiswaController::class, 'dashboard'])
                ->name('dashboard');


            // ==================================================
            // 1. PRESENSI
            // ==================================================

            Route::get('/presensi', [SiswaController::class, 'presensiForm'])
                ->name('presensi');

            Route::post('/presensi/store', [SiswaController::class, 'storePresensi'])
                ->name('presensi.store');


            // ==================================================
            // 2. IZIN / SAKIT
            // ==================================================

            Route::get('/izin', [SiswaController::class, 'izinForm'])
                ->name('izin');

            Route::post('/izin/store', [SiswaController::class, 'storeIzin'])
                ->name('izin.store');


            // ==================================================
            // 3. RIWAYAT
            // ==================================================

            Route::get('/riwayat', [SiswaController::class, 'riwayat'])
                ->name('riwayat');

            Route::get('/riwayat/detail/{id}', [SiswaController::class, 'detailPresensi'])
                ->name('riwayat.detail');


            // ==================================================
            // 4. PROFILE
            // ==================================================

            Route::get('/profile', [SiswaController::class, 'profile'])
                ->name('profile');

            Route::post('/profile/update', [SiswaController::class, 'updateProfile'])
                ->name('profile.update');
        });


    // ==================================================
    // PORTAL GURU MAPEL
    // ==================================================

    Route::middleware(['role:guru_mapel'])
        ->prefix('guru')
        ->name('gurumapel.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [GuruMapelController::class, 'dashboard'])
                ->name('dashboard');


            // ==================================================
            // 1. VERIFIKASI KBM
            // ==================================================

            Route::get('/verifikasi-kbm', [GuruMapelController::class, 'verifikasiKbm'])
                ->name('kbm');

            Route::post('/verifikasi-kbm/store', [GuruMapelController::class, 'storeVerifikasiKbm'])
                ->name('kbm.store');
        });


    // ==================================================
    // PORTAL WALI KELAS
    // ==================================================

    Route::middleware(['role:wali_kelas'])
        ->prefix('walikelas')
        ->name('walikelas.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [WaliKelasController::class, 'dashboard'])
                ->name('dashboard');


            // ==================================================
            // 1. IZIN / SAKIT
            // ==================================================

            Route::get('/izin-sakit', [WaliKelasController::class, 'izinSakit'])
                ->name('izin');

            Route::post('/izin-sakit/{id}/update', [WaliKelasController::class, 'updateIzin'])
                ->name('izin.update');


            // ==================================================
            // 2. REKAP KELAS
            // ==================================================

            Route::get('/rekap', [WaliKelasController::class, 'rekapKelas'])
                ->name('rekap');
        });


    // ==================================================
    // PORTAL KESISWAAN
    // ==================================================

    Route::middleware(['role:kesiswaan'])
        ->prefix('kesiswaan')
        ->name('kesiswaan.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [KesiswaanController::class, 'dashboard'])
                ->name('dashboard');


            // ==================================================
            // 1. DISPEN / IZIN / SAKIT
            // ==================================================

            Route::get('/dispen', [KesiswaanController::class, 'dispen'])
                ->name('dispen');

            Route::post('/dispen/{id}/update', [KesiswaanController::class, 'updateDispen'])
                ->name('dispen.update');


            // ==================================================
            // 2. MONITORING
            // ==================================================

            Route::get('/monitoring', [KesiswaanController::class, 'monitoring'])
                ->name('monitoring');
        });


    // ==================================================
    // PORTAL OPERATOR / ADMIN
    // ==================================================

    Route::middleware(['role:operator'])
        ->prefix('operator')
        ->name('operator.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [OperatorController::class, 'dashboard'])
                ->name('dashboard');


            // ==================================================
            // 1. MANAJEMEN USERS
            // ==================================================

            Route::get('/users', [OperatorController::class, 'users'])
                ->name('users');

            Route::post('/users', [OperatorController::class, 'storeUser'])
                ->name('users.store');


            // ==================================================
            // 2. DATA SISWA
            // ==================================================

            Route::get('/siswa', [OperatorController::class, 'siswa'])
                ->name('siswa');


            // ==================================================
            // 3. MAPPING GURU
            // ==================================================

            Route::get('/mapping-guru', [OperatorController::class, 'mappingGuru'])
                ->name('mapping.guru');

            Route::post('/mapping-guru', [OperatorController::class, 'storeMappingGuru'])
                ->name('mapping.guru.store');


            // ==================================================
            // 4. MAPPING WALI KELAS
            // ==================================================

            Route::get('/mapping-wali', [OperatorController::class, 'mappingWali'])
                ->name('mapping.wali');

            Route::post('/mapping-wali', [OperatorController::class, 'storeMappingWali'])
                ->name('mapping.wali.store');


            // ==================================================
            // 5. JADWAL
            // ==================================================

            Route::get('/jadwal', [OperatorController::class, 'jadwal'])
                ->name('jadwal');

            Route::post('/jadwal', [OperatorController::class, 'storeJadwal'])
                ->name('jadwal.store');


            // ==================================================
            // 6. HARI LIBUR
            // ==================================================

            Route::get('/hari-libur', [OperatorController::class, 'hariLibur'])
                ->name('libur');

            Route::post('/hari-libur', [OperatorController::class, 'storeHariLibur'])
                ->name('libur.store');
        });
});