<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\Jadwal;
use App\Models\Soal_ujian;
use App\Models\Tingkat_pendidikan;
use App\Models\Rombongan_belajar;
use App\Models\Jadwal_ujian;
use App\Imports\ImportJawaban;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class SoalUjianController extends Controller
{
    public function index(){
        $data_sekolah = [];
        if(request()->user()->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = Sekolah::whereNotNull('bentuk_pendidikan_id')->select('sekolah_id', 'nama')->get();
        }
        $data = Soal_ujian::withWhereHas('jadwal_ujian', function($query){
            $query->withWhereHas('rombongan_belajar', function($query){
                $query->where('semester_id', request()->semester_id);
            });
            $query->with(['mata_pelajaran', 'jadwal']);
        })->withCount('jawaban_soal as jumlah_kunci')->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->whereHas('jadwal_ujian', function($query){
                $query->whereHas('mata_pelajaran', function($query){
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                });
            });
        })
        ->when(request()->sekolah_id, function($query) {
            $query->whereHas('jadwal_ujian', function($query){
                $query->whereHas('rombongan_belajar', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            });
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => $data_sekolah]);
    }
    public function referensi(){
        $data_sekolah = Sekolah::whereNotNull('bentuk_pendidikan_id')->select('sekolah_id', 'nama')->orderBy('bentuk_pendidikan_id')->get();
        $data_tingkat = [];
        $data_rombel = [];
        $data_ujian = [];
        $data_jadwal = [];
        if(request()->sekolah_id){
            $sekolah = Sekolah::find(request()->sekolah_id);
            $data_tingkat = Tingkat_pendidikan::where('jenjang_pendidikan_id', $sekolah->bentuk_pendidikan_id)->orderBy('tingkat_pendidikan_id')->get();
        }
        if(request()->tingkat){
            $data_rombel = Rombongan_belajar::where(function($query){
                $query->where('sekolah_id', request()->sekolah_id);
                $query->where('semester_id', request()->semester_id);
                $query->where('tingkat_pendidikan_id', request()->tingkat);
            })->orderBy('nama')->get();
        }
        if(request()->rombongan_belajar_id){
            $data_jadwal = Jadwal::where('rombongan_belajar_id', request()->rombongan_belajar_id)->orderBy('tanggal')->get();
        }
        if(request()->jadwal_id){
            $data_ujian = Jadwal_ujian::with('mata_pelajaran')->where('jadwal_id', request()->jadwal_id)->orderBy('tanggal')->orderBy('jam_ke')->get();
        }
        $data = [
            'data_tingkat' => $data_tingkat,
            'data_sekolah' => $data_sekolah,
            'data_rombel' => $data_rombel,
            'data_jadwal' => $data_jadwal,
            'data_ujian' => $data_ujian,
        ];
        return response()->json($data);
    }
    private function validasi(){
        /*
        semester_id: '',
        sekolah_id: '',
        tingkat: '',
        rombongan_belajar_id: '',
        jadwal_id: '',
        jadwal_ujian_id: '',
        jumlah_soal: '',
        waktu_ujian: 120,
        skor_benar: '',
        skor_salah: 0,
        skala_nilai: 100,
        jumlah_pg: '',
        */
        request()->validate(
            [
                'sekolah_id' => ['required'],
                'tingkat' => ['required'],
                'rombongan_belajar_id' => ['required'],
                'jadwal_id' => ['required'],
                'jadwal_ujian_id' => ['required'],
                'jumlah_soal' => ['required', 'numeric'],
                'waktu_ujian' => ['required', 'numeric'],
                'skor_benar' => ['required', 'numeric'],
                'skor_salah' => ['required', 'numeric'],
                'skala_nilai' => ['required', 'numeric'],
                'jumlah_pg' => ['required'],
                //'status' => ['required'],
            ],
            [
                'sekolah_id.required' => 'Unit tidak boleh kosong!!',
                'tingkat.required' => 'Tingkat tidak boleh kosong!!',
                'rombongan_belajar_id.required' => 'Rombongan Belajar tidak boleh kosong!!',
                'jadwal_id.required' => 'Jadwal Ujian tidak boleh kosong!!',
                'jadwal_ujian_id.required' => 'Mata Ujian tidak boleh kosong!!',
                'jumlah_soal.required' => 'Jumlah Soal tidak boleh kosong!!',
                'waktu_ujian.required' => 'Lama Ujian tidak boleh kosong!!',
                'skor_benar.required' => 'Skor Benar tidak boleh kosong!!',
                'skor_salah.required' => 'Skor Salah tidak boleh kosong!!',
                'skala_nilai.required' => 'Skala Nilai tidak boleh kosong!!',
                'jumlah_soal.numeric' => 'Jumlah Soal harus berupa angka!!',
                'waktu_ujian.numeric' => 'Lama Ujian harus berupa angka!!',
                'skor_benar.numeric' => 'Skor Benar harus berupa angka!!',
                'skor_salah.numeric' => 'Skor Salah harus berupa angka!!',
                'skala_nilai.numeric' => 'Skala Nilai harus berupa angka!!',
                'jumlah_pg.required' => 'Jumlah Opsi Pilihan Ganda tidak boleh kosong!!',
                //'status.required' => 'Status tidak boleh kosong!!',
            ]
        );
    }
    public function store(){
        $this->validasi();
        if(request()->soal_ujian_id){
            $soal_ujian = Soal_ujian::find(request()->soal_ujian_id);
            $soal_ujian->jadwal_ujian_id = request()->jadwal_ujian_id;
            $soal_ujian->jumlah_soal = request()->jumlah_soal;
            $soal_ujian->waktu_ujian = request()->waktu_ujian * 60;
            $soal_ujian->skor_benar = request()->skor_benar;
            $soal_ujian->skor_salah = request()->skor_salah;
            $soal_ujian->skala_nilai = request()->skala_nilai;
            $soal_ujian->jumlah_pg = request()->jumlah_pg;
            $insert = $soal_ujian->save();
        } else {
            $insert = Soal_ujian::updateOrCreate(
                [
                    'jadwal_ujian_id' => request()->jadwal_ujian_id,
                ],
                [
                    'jumlah_soal' => request()->jumlah_soal,
                    'waktu_ujian' => request()->waktu_ujian * 60,
                    'skor_benar' => request()->skor_benar,
                    'skor_salah' => request()->skor_salah,
                    'skala_nilai' => request()->skala_nilai,
                    'jumlah_pg' => request()->jumlah_pg,
                ]
            );
        }
        //$soal_ujian->status = request()->status;
        if($insert){
            $data = [
                'icon' => 'success',
                'text' => 'Soal Ujian berhasil disimpan',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Soal Ujian gagal disimpan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function status(){
        $soal_ujian = Soal_ujian::find(request()->soal_ujian_id);
        $soal_ujian->status = (request()->status) ? 0 : 1;
        $status_text = (request()->status) ? 'nonaktifkan' : 'aktifkan';
        if($soal_ujian->save()){
            $data = [
                'icon' => 'success',
                'text' => 'Soal Ujian berhasil di '.$status_text,
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Soal Ujian gagal di '.$status_text.'. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function destroy($id){
        $find = Soal_ujian::find($id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'text' => 'Soal Ujian berhasil dihapus',
                    'title' => 'Berhasil',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Soal Ujian gagal dihapus. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Soal Ujian tidak ditemukan!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function upload_jawaban(){
        request()->validate(
            [
                'file_excel' => 'mimes:xlsx', // 1MB Max
            ],
            [
                'file_excel.mimes' => 'File harus berupa file dengan ekstensi: xlsx.',
            ]
        );
        $file_path = request()->file_excel->store('files', 'public');
        Excel::import(new ImportJawaban(request()->soal_ujian_id), storage_path('/app/public/'.$file_path));
        Storage::disk('public')->delete($file_path);
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Kunci Jawaban berhasil disimpan',
        ];
        return response()->json($data);
    }
}
