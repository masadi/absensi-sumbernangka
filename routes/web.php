<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\UnduhanController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('cetak')->name('cetak.')->group( function(){
    Route::get('/semua-pdf/{jenis}/{start}/{end}', [CetakController::class, 'semua_pdf'])->name('semua_pdf');
    Route::get('/semua-excel/{jenis}/{start}/{end}', [CetakController::class, 'semua_excel'])->name('semua_excel');
    Route::get('/pdf/{data}/{id}/{jenis}/{start}/{end}', [CetakController::class, 'index'])->name('pdf');
    Route::get('/excel/{data}/{id}/{jenis}/{start}/{end}', [CetakController::class, 'excel'])->name('excel');
    Route::get('/pdf-rekap/{format}/{data}/{start}/{end}/{id}', [CetakController::class, 'pdf_rekap'])->name('cetak-rekap.pdf');
    Route::get('/excel-rekap/{format}/{data}/{start}/{end}/{id}', [CetakController::class, 'excel_rekap'])->name('cetak-rekap.excel');
    Route::get('/pdf-libur/{start}/{end}', [CetakController::class, 'pdf_libur'])->name('cetak-libur.pdf');
    Route::get('/id-anggota/{rombongan_belajar_id}', [CetakController::class, 'id_anggota'])->name('id-anggota');
    Route::get('/kartu-pelajar/{rombongan_belajar_id}', [CetakController::class, 'kartu_pelajar'])->name('kartu-pelajar');
    Route::get('/kartu-pkl/{rombongan_belajar_id}', [CetakController::class, 'kartu_pkl'])->name('kartu-pkl');
    Route::get('/id-card/{asal}/{id}', [CetakController::class, 'id_card'])->name('id-card');
    Route::get('/id-pelajar/{id}', [CetakController::class, 'id_pelajar'])->name('id-pelajar');
    Route::get('/id-pkl/{id}', [CetakController::class, 'id_pkl'])->name('id-pkl');
    Route::get('/jadwal/{jadwal_id}', [CetakController::class, 'jadwal'])->name('jadwal');
    Route::get('/keterlambatan/{jenis}/{start}/{end}', [CetakController::class, 'keterlambatan'])->name('keterlambatan');
    Route::get('/ketidakhadiran/{jenis}/{start}/{end}', [CetakController::class, 'ketidakhadiran'])->name('ketidakhadiran');
    Route::get('/pulang-cepat/{jenis}/{start}/{end}', [CetakController::class, 'pulang_cepat'])->name('pulang-cepat');
    Route::get('/qrcode/{sekolah_id}/{asal}/{tingkat?}', [CetakController::class, 'semua_qrcode'])->name('semua-qrcode');
});
Route::prefix('unduhan')->name('unduhan.')->group( function(){
    Route::get('/pelanggaran/{sekolah_id}/{semester_id}/{start?}/{end?}', [UnduhanController::class, 'pelanggaran'])->name('pelanggaran');
    Route::get('/rekap-tingkat/{sekolah_id}/{semester_id}/{tingkat?}/{start?}/{end?}', [UnduhanController::class, 'rekap_tingkat'])->name('rekap-tingkat');
    Route::get('/rekap-rombel/{sekolah_id}/{semester_id}/{rombongan_belajar_id?}/{start?}/{end?}', [UnduhanController::class, 'rekap_rombel'])->name('rekap-rombel');
    Route::get('/rekap-pd/{sekolah_id}/{semester_id}/{peserta_didik_id?}/{start?}/{end?}', [UnduhanController::class, 'rekap_pd'])->name('rekap-pd');
    Route::get('/template-jawaban/{soal_ujian_id}', [UnduhanController::class, 'template_jawaban'])->name('template-jawaban');
    Route::get('/analisis-hasil-ujian/{jadwal_ujian_id}', [UnduhanController::class, 'analisis'])->name('analisis-hasil-ujian');
    Route::get('/template-pd/{jumlah}', [UnduhanController::class, 'template_pd'])->name('template-pd');
});
Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '.*');