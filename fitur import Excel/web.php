<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanKaryawanController;
use App\Http\Controllers\ExportController;

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

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAtasan()) {
            return redirect()->route('atasan.dashboard');
        } else {
            return redirect()->route('karyawan.dashboard');
        }
    })->name('dashboard');

    Route::get('/atasan/dashboard', [DashboardController::class, 'adminIndex'])->name('atasan.dashboard');
    Route::get('/karyawan/dashboard', [DashboardController::class, 'kelompokIndex'])->name('karyawan.dashboard');
    
    // Laporan Routes
    Route::get('/kelompok/laporan', [LaporanKaryawanController::class, 'index'])->name('kelompok.laporan');
    
    // Pemantauan Laporan Routes (Admin/Atasan)
    Route::get('/atasan/pemantauan-laporan', [App\Http\Controllers\PemantauanLaporanController::class, 'index'])->name('atasan.pemantauan-laporan');
    Route::get('/atasan/pemantauan-laporan/export', [App\Http\Controllers\PemantauanLaporanController::class, 'exportExcel'])->name('atasan.pemantauan-laporan.export');
    
    // Atasan Management Routes
    Route::get('/atasan/manajemen', [ManajemenController::class, 'index'])->name('atasan.manajemen');
    Route::get('/atasan/kelompok', [ManajemenController::class, 'kelompok'])->name('atasan.kelompok');
    Route::get('/atasan/karyawan', [ManajemenController::class, 'karyawan'])->name('atasan.karyawan');

    // Excel Management Routes
    Route::get('/atasan/excel', [App\Http\Controllers\ExcelController::class, 'index'])->name('atasan.excel.index');
    Route::get('/atasan/excel/upload', [App\Http\Controllers\ExcelController::class, 'upload'])->name('atasan.excel.upload');
    Route::get('/atasan/excel/create', [App\Http\Controllers\ExcelController::class, 'create'])->name('atasan.excel.create');
    
    // Admin Settings Routes
    Route::get('/atasan/pengaturan', [App\Http\Controllers\SettingsController::class, 'adminIndex'])->name('atasan.settings');
    
    // Export Data Routes
    Route::get('/atasan/export-data', [App\Http\Controllers\ExportDataController::class, 'index'])->name('atasan.export-data');
    
    // Statistik & Prediksi Route
    Route::get('/atasan/statistik-prediksi', [App\Http\Controllers\Admin\StatistikController::class, 'index'])->name('atasan.statistik-prediksi');
    
    // Statistik & Prediksi Routes (Admin only)
    Route::prefix('admin')->middleware(['auth'])->group(function() {
        Route::get('statistik', [App\Http\Controllers\Admin\StatistikController::class, 'index'])->name('admin.statistik.index');
        Route::get('statistik/data', [App\Http\Controllers\Admin\StatistikController::class, 'data'])->name('admin.statistik.data');
        Route::get('prediksi', [App\Http\Controllers\Admin\PrediksiController::class, 'index'])->name('admin.prediksi.index');
        Route::get('prediksi/generate-kegiatan', [App\Http\Controllers\Admin\PrediksiController::class, 'generateKegiatan'])->name('admin.prediksi.generate-kegiatan');
        Route::post('prediksi/generate-kegiatan', [App\Http\Controllers\Admin\PrediksiController::class, 'generatePrediksiKegiatan'])->name('admin.prediksi.generate-kegiatan.post');
        Route::get('prediksi/kegiatan/get-by-kelompok', [App\Http\Controllers\Admin\PrediksiController::class, 'getPrediksiKegiatanByKelompok'])->name('admin.prediksi.getPrediksiKegiatanByKelompok');
        Route::get('prediksi/latest', [App\Http\Controllers\Admin\PrediksiController::class, 'getLatest'])->name('admin.prediksi.latest');
        Route::post('prediksi/generate', [App\Http\Controllers\Admin\PrediksiController::class, 'generate'])->name('admin.prediksi.generate');
        Route::post('prediksi/reset', [App\Http\Controllers\Admin\PrediksiController::class, 'reset'])->name('admin.prediksi.reset');
        Route::get('prediksi/export/{format}', [App\Http\Controllers\Admin\PrediksiController::class, 'export'])->name('admin.prediksi.export');
        Route::get('prediksi/{id}', [App\Http\Controllers\Admin\PrediksiController::class, 'show'])->name('admin.prediksi.show');
        Route::delete('prediksi/{id}', [App\Http\Controllers\Admin\PrediksiController::class, 'destroy'])->name('admin.prediksi.destroy');
    });
    
    // Kelompok Settings Routes
    Route::get('/kelompok/pengaturan', [App\Http\Controllers\SettingsController::class, 'kelompokIndex'])->name('kelompok.settings');
    
    // Prediksi Routes (Karyawan)
    Route::prefix('kelompok')->middleware(['auth'])->group(function() {
        Route::get('prediksi/generate-kegiatan', [App\Http\Controllers\Admin\PrediksiController::class, 'generateKegiatanKaryawan'])->name('kelompok.prediksi.generate-kegiatan');
        Route::post('prediksi/generate-kegiatan', [App\Http\Controllers\Admin\PrediksiController::class, 'generatePrediksiKegiatanKaryawan'])->name('kelompok.prediksi.generate-kegiatan.post');
        Route::get('prediksi/kegiatan/get-by-kelompok', [App\Http\Controllers\Admin\PrediksiController::class, 'getPrediksiKegiatanByKelompokKaryawan'])->name('kelompok.prediksi.getPrediksiKegiatanByKelompok');
    });
    
    // Dashboard Routes (duplicate removed - already defined above)

// Test route
Route::get('/test-modal', function () {
    return view('test-modal');
});

// Simple test route
Route::get('/test-simple', function () {
    return view('test-simple');
});


    // API Routes for AJAX
    Route::prefix('api')->middleware(['auth'])->group(function () {
        // Kelompok Routes
        Route::get('/kelompok', [KelompokController::class, 'index']);
        Route::post('/kelompok', [KelompokController::class, 'store']);
        Route::put('/kelompok/{id}', [KelompokController::class, 'update']);
        Route::delete('/kelompok/{id}', [KelompokController::class, 'destroy']);

        // Karyawan Routes
        Route::get('/karyawan', [KaryawanController::class, 'index']);
        Route::post('/karyawan', [KaryawanController::class, 'store']);
        Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);

        // Laporan Karyawan Routes
        Route::get('/laporan-karyawan', [LaporanKaryawanController::class, 'getLaporans']);
        Route::post('/laporan-karyawan', [LaporanKaryawanController::class, 'store']);
        Route::post('/laporan-karyawan/import', [LaporanKaryawanController::class, 'import']);
        Route::get('/laporan-karyawan/template', [LaporanKaryawanController::class, 'downloadTemplate']);
        Route::get('/laporan-karyawan/{id}', [LaporanKaryawanController::class, 'show']);
        Route::match(['put', 'post'], '/laporan-karyawan/{id}', [LaporanKaryawanController::class, 'update']);
        Route::delete('/laporan-karyawan/{id}', [LaporanKaryawanController::class, 'destroy']);
        Route::get('/laporan-karyawan/{id}/download', [LaporanKaryawanController::class, 'downloadFile']);
        
        // Pemantauan Laporan Routes (Admin)
        Route::get('/pemantauan-laporan/{id}', [App\Http\Controllers\PemantauanLaporanController::class, 'show']);
        Route::match(['put', 'post'], '/pemantauan-laporan/{id}', [App\Http\Controllers\PemantauanLaporanController::class, 'update']);
        Route::delete('/pemantauan-laporan/{id}', [App\Http\Controllers\PemantauanLaporanController::class, 'destroy']);
        Route::get('/pemantauan-laporan/{id}/download', [App\Http\Controllers\PemantauanLaporanController::class, 'downloadFile']);

        // Excel Routes
        Route::post('/excel/upload', [App\Http\Controllers\ExcelController::class, 'store']);
        Route::post('/excel/generate', [App\Http\Controllers\ExcelController::class, 'generate']);
        Route::get('/excel/download/{fileName}', [App\Http\Controllers\ExcelController::class, 'download']);
        Route::delete('/excel/delete/{fileName}', [App\Http\Controllers\ExcelController::class, 'destroy']);
        Route::get('/excel/files', [App\Http\Controllers\ExcelController::class, 'getFiles']);
        
        // Test route
        Route::get('/excel/test', function() {
            return response()->json(['message' => 'API Excel test route working']);
        });

        // Admin Settings API Routes
        Route::post('/admin/settings', [App\Http\Controllers\SettingsController::class, 'updateAdminSettings']);
        Route::post('/admin/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile']);
        Route::get('/admin/profile', [App\Http\Controllers\SettingsController::class, 'getProfile']);
        Route::delete('/admin/profile/avatar', [App\Http\Controllers\SettingsController::class, 'deleteAvatar']);
        Route::get('/admin/system-stats', [App\Http\Controllers\SettingsController::class, 'getSystemStats']);
        Route::post('/admin/backup', [App\Http\Controllers\SettingsController::class, 'backupSystem']);
        Route::post('/admin/restore', [App\Http\Controllers\SettingsController::class, 'restoreSystem']);

        // Kelompok Settings API Routes
        Route::post('/kelompok/settings', [App\Http\Controllers\SettingsController::class, 'updateKelompokSettings']);
        Route::get('/kelompok/profile', [App\Http\Controllers\SettingsController::class, 'getKelompokProfile']);
        Route::delete('/kelompok/profile/avatar', [App\Http\Controllers\SettingsController::class, 'deleteKelompokAvatar']);
        Route::post('/kelompok/account', [App\Http\Controllers\SettingsController::class, 'updateAccount']);
        Route::post('/kelompok/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotifications']);
        Route::post('/kelompok/work-schedule', [App\Http\Controllers\SettingsController::class, 'updateWorkSchedule']);
        Route::get('/kelompok/monthly-reports', [App\Http\Controllers\SettingsController::class, 'getMonthlyReports']);

        // Dashboard API Routes
        Route::get('/admin/dashboard-stats', [App\Http\Controllers\DashboardController::class, 'getAdminStats']);
        Route::get('/admin/dashboard-charts', [App\Http\Controllers\DashboardController::class, 'getAdminChartData']);
        Route::get('/kelompok/dashboard-stats', [App\Http\Controllers\DashboardController::class, 'getKelompokStats']);
        Route::get('/kelompok/dashboard-charts', [App\Http\Controllers\DashboardController::class, 'getKelompokChartData']);

        // Export Routes
        Route::get('/export/all', [ExportController::class, 'exportAllData']);
        Route::get('/export/kelompok', [ExportController::class, 'exportByKelompok']);
        Route::get('/export/my-kelompok', [ExportController::class, 'exportKelompokData']);
        
        // Export Data Routes
        Route::get('/export-data/all', [App\Http\Controllers\ExportDataController::class, 'exportAllData']);
        Route::get('/export-data/kelompok', [App\Http\Controllers\ExportDataController::class, 'exportByKelompok']);
        Route::get('/kelompok', [App\Http\Controllers\ExportDataController::class, 'getKelompokList']);
    });
});
