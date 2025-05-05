<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PtkController;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\LiburController;
use App\Http\Controllers\JamController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\SoalUjianController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\PantauUjianController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('scan', [ScanController::class, 'index']);
Route::get('display', [ScanController::class, 'display']);
Route::group(['prefix' => 'statistik'], function () {
  Route::post('/', [StatistikController::class, 'index']);
  Route::post('/pd', [StatistikController::class, 'pd']);
  Route::post('/ptk', [StatistikController::class, 'ptk']);
});
Route::group(['prefix' => 'auth'], function () {
  Route::get('/semester', [AuthController::class, 'semester']);
  Route::post('login', [AuthController::class, 'login']);
  Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('user', [AuthController::class, 'update_user']);
    Route::post('users/generate', [AuthController::class, 'generate']);
    Route::get('users/list', [AuthController::class, 'list']);
    Route::post('user/delete', [AuthController::class, 'hapus']);
    Route::post('user/detil', [AuthController::class, 'detil']);
    Route::post('user/reset-password', [AuthController::class, 'reset_password']);
    Route::post('user/foto', [AuthController::class, 'foto']);
    Route::post('user/ganti-password', [AuthController::class, 'ganti_password']);
    Route::post('user/update-role', [AuthController::class, 'update_role']);
  });
});
Route::group(['middleware' => 'auth:sanctum'], function () {
  Route::group(['prefix' => 'ujian'], function () {
    Route::get('/', [UjianController::class, 'index']);
    Route::post('/soal', [UjianController::class, 'soal']);
    Route::post('waktu', [UjianController::class, 'waktu']);
    Route::post('/simpan', [UjianController::class, 'simpan']);
    Route::post('selesai', [UjianController::class, 'selesai']);
    Route::post('hasil', [UjianController::class, 'hasil']);
  });
  Route::group(['prefix' => 'dashboard'], function () {
    Route::post('/', [DashboardController::class, 'index']);
    Route::post('/generate-scan', [DashboardController::class, 'generate_scan']);
  });
  Route::group(['prefix' => 'pengaturan'], function () {
    Route::get('/umum', [DashboardController::class, 'pengaturan']);
    Route::post('/umum', [DashboardController::class, 'save']);
  });
  Route::group(['prefix' => 'libur'], function () {
    Route::get('/', [LiburController::class, 'index']);
    Route::post('/simpan', [LiburController::class, 'simpan']);
    Route::post('/hapus', [LiburController::class, 'hapus']);
  });
  Route::group(['prefix' => 'referensi'], function () {
    Route::get('/unit', [UnitController::class, 'index']);
    Route::post('/sync-data', [UnitController::class, 'sync_data']);
    Route::post('/update-unit', [UnitController::class, 'update_unit']);
    Route::post('/update-kasek ', [UnitController::class, 'update_kasek']);
    Route::get('/ptk', [PtkController::class, 'index']);
    Route::post('/get-ptk', [PtkController::class, 'get_ptk']);
    Route::post('/update-bp', [PtkController::class, 'update_bp']);
    Route::post('/upload-ptk', [PtkController::class, 'upload_ptk']);
    Route::post('/add-ptk', [PtkController::class, 'add_ptk']);
    Route::post('/update-ptk', [PtkController::class, 'update_ptk']);
    Route::get('/rombel', [RombelController::class, 'index']);
    Route::get('/pd', [SiswaController::class, 'index']);
    Route::post('/get-siswa', [SiswaController::class, 'get_siswa']);
    Route::post('/get-tingkat', [RombelController::class, 'get_tingkat']);
    Route::post('/get-rombel', [RombelController::class, 'get_rombel']);
    Route::post('/pindah-rombel', [SiswaController::class, 'pindah_rombel']);
    Route::post('/delete-siswa', [SiswaController::class, 'delete']);
    Route::post('/update-siswa', [SiswaController::class, 'update_siswa']);
    Route::get('/sekolah', [UnitController::class, 'sekolah']);
    Route::post('/import-pd', [SiswaController::class, 'import_pd']);
    Route::get('/get-sekolah', [RombelController::class, 'get_sekolah']);
    Route::post('/simpan-rombel', [RombelController::class, 'simpan_rombel']);
    Route::post('/non-anggota', [RombelController::class, 'non_anggota']);
    Route::post('/anggota-rombel', [RombelController::class, 'anggota_rombel']);
    Route::post('/set-anggota', [RombelController::class, 'set_anggota']);
    Route::post('/edit-rombel', [RombelController::class, 'edit_rombel']);
    Route::post('/update-rombel', [RombelController::class, 'update_rombel']);
    Route::get('/admin', [UnitController::class, 'admin']);
    Route::post('/post-admin', [UnitController::class, 'post_admin']);
    Route::get('/bp', [UnitController::class, 'bp']);
    Route::post('/post-bp', [UnitController::class, 'post_bp']);
  });
  Route::group(['prefix' => 'jadwal'], function () {
    Route::get('/', [JadwalController::class, 'index']);
    Route::post('/edit', [JadwalController::class, 'edit']);
    Route::post('/hapus', [JadwalController::class, 'hapus']);
    Route::post('/simpan-jadwal', [JadwalController::class, 'simpan_jadwal']);
    Route::post('/simpan', [JadwalController::class, 'simpan']);
    Route::post('/update', [JadwalController::class, 'update']);
    Route::post('/mata-ujian', [JadwalController::class, 'mata_ujian']);
    Route::post('/get-data', [JadwalController::class, 'get_data']);
    Route::post('/get-sekolah', [JadwalController::class, 'get_sekolah']);
    Route::post('/get-mapel', [JadwalController::class, 'get_mapel']);
    Route::post('/salin', [JadwalController::class, 'salin']);
  });
  Route::group(['prefix' => 'jam'], function () {
    Route::get('/', [JamController::class, 'index']);
    Route::get('/referensi', [JamController::class, 'referensi']);
    Route::post('/simpan', [JamController::class, 'simpan']);
    Route::post('/update', [JamController::class, 'update']);
    Route::get('/terdaftar', [JamController::class, 'terdaftar']);
    Route::get('/kosong', [JamController::class, 'kosong']);
    Route::post('/set-anggota', [JamController::class, 'set_anggota']);
    Route::post('/detil', [JamController::class, 'detil']);
    Route::post('/hapus', [JamController::class, 'hapus']);
  });
  Route::group(['prefix' => 'laporan'], function () {
    Route::get('/kehadiran', [LaporanController::class, 'kehadiran']);
    Route::get('/ketidakhadiran', [LaporanController::class, 'ketidakhadiran']);
    Route::post('/update-jam', [LaporanController::class, 'update_jam']);
    Route::post('/update-keterangan', [LaporanController::class, 'update_keterangan']);
    Route::post('/tidak-hadir', [LaporanController::class, 'tidak_hadir']);
    Route::post('/save-izin', [LaporanController::class, 'save_izin']);
    Route::post('/hapus-izin', [LaporanController::class, 'hapus_izin']);
    Route::post('/cleasing', [LaporanController::class, 'cleasing']);
    Route::post('/scan-manual', [LaporanController::class, 'scan_manual']);
  });
  Route::group(['prefix' => 'rekapitulasi'], function () {
    Route::get('/', [RekapitulasiController::class, 'index']);
  });
  Route::group(['prefix' => 'user'], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('update-profile', [AuthController::class, 'update_profile']);
  });
  Route::group(['prefix' => 'pelanggaran'], function () {
    Route::get('/', [PelanggaranController::class, 'index']);
    Route::post('/store', [PelanggaranController::class, 'store']);
    Route::post('/rekap', [PelanggaranController::class, 'rekap']);
  });
  Route::group(['prefix' => 'soal-ujian'], function () {
    Route::get('/', [SoalUjianController::class, 'index']);
    Route::post('/referensi', [SoalUjianController::class, 'referensi']);
    Route::post('/store', [SoalUjianController::class, 'store']);
    Route::post('/status', [SoalUjianController::class, 'status']);
    Route::delete('/destroy/{id}', [SoalUjianController::class, 'destroy']);
    Route::post('/upload-jawaban', [SoalUjianController::class, 'upload_jawaban']);
  });
  Route::group(['prefix' => 'pantau-ujian'], function () {
    Route::get('/', [PantauUjianController::class, 'index']);
    Route::post('/list', [PantauUjianController::class, 'list']);
    Route::post('/status', [PantauUjianController::class, 'status']);
  });
});
