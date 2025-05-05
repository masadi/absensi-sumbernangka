<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\Soal_ujian;
use App\Models\Ujian_siswa;
use App\Models\Peserta_didik;

class PantauUjianController extends Controller
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
        })->withCount([
            'siswa as aktif' => function($query){
                $query->whereStatus(0);
            },
            'siswa as selesai' => function($query){
                $query->whereStatus(1);
            }
        ])->whereStatus(1)->orderBy(request()->sortby, request()->sortbydesc)
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
        /*})
        ->when(request()->tingkat, function($query) {
            $query->whereHas('jadwal_ujian', function($query){
                $query->whereHas('rombongan_belajar', function($query){
                    $query->where('tingkat_pendidikan_id', request()->tingkat);
                });
            });
        })
        ->when(request()->rombongan_belajar_id, function($query) {
            $query->whereHas('jadwal_ujian', function($query){
                $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
            });*/
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => $data_sekolah]);
    }
    public function list(){
        $data = [
            'ujian' => Soal_ujian::with(['jadwal_ujian' => function($query){
                $query->with(['jadwal', 'mata_pelajaran']);
            }])->find(request()->soal_ujian_id),
            'pd' => Peserta_didik::withWhereHas('user', function($query){
                $query->withWhereHas('ujian_siswa', function($query){
                    $query->where('soal_ujian_id', request()->soal_ujian_id);
                });
            })->orderBy('nama')->get(),
        ];
        return response()->json($data);
    }
    public function status(){
        $find = Ujian_siswa::where(function($query){
            $query->where('soal_ujian_id', request()->soal_ujian_id);
            $query->where('user_id', request()->user_id);
        })->first();
        if($find){
            $find->status = (request()->aksi == 'reset') ? 0 : 1;
            $text = (request()->aksi == 'reset') ? 'Reset!' : 'Force Selesai!';
            if($find->save()){
                $data = [
                    'icon' => 'success',
                    'text' => 'Status Ujian Peserta Didik berhasil disimpan',
                    'title' => 'Berhasil',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Status Ujian Peserta Didik gagal disimpan. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Ujian Peserta Didik tidak ditemukan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
