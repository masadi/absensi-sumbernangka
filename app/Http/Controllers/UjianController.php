<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal_ujian;
use App\Models\Soal_ujian;
use App\Models\Jawaban_soal;
use App\Models\Jawaban_pd;
use App\Models\Ujian_siswa;
use App\Models\Rombongan_belajar;
use Carbon\Carbon;

class UjianController extends Controller
{
    public function index(){
        $data = [
            'rombongan_belajar' => Rombongan_belajar::where(function($query){
                $query->where('semester_id', semester_id());
                $query->withWhereHas('anggota', function($query){
                    $query->where('peserta_didik_id', request()->user()->peserta_didik_id);
                });
            })->first(),
            'items' => Jadwal_ujian::withWhereHas('rombongan_belajar', function($query){
                $query->where('semester_id', semester_id());
                $query->withWhereHas('anggota', function($query){
                    $query->where('peserta_didik_id', request()->user()->peserta_didik_id);
                });
            })->with(['mata_pelajaran', 'jadwal', 'soal_ujian.ujian_siswa' => function($query){
                $query->where('user_id', request()->user()->id);
            }])->orderBy('tanggal')->orderBy('jam_ke')->get(),
        ];
        return response()->json($data);
    }
    public function waktu(){
        /*$first = Ujian_siswa::firstOrCreate(
            [
                'soal_ujian_id' => request()->soal_ujian_id,
                'user_id' => request()->user()->id,
            ],
            [
                'waktu' => 120,
            ]
        );*/
        $first = Ujian_siswa::where(function($query){
            $query->where('soal_ujian_id', request()->soal_ujian_id);
            $query->where('user_id', request()->user()->id);
        })->first();
        $data = [
            'waktu' => ($first) ? $first->waktu : NULL,
        ];
        return response()->json($data);
    }
    public function soal(){
        $soal = Jawaban_soal::with([
            'jawaban_pd' => function($query){
                $query->whereHas('pd', function($query){
                    $query->where('peserta_didik.peserta_didik_id', request()->user()->peserta_didik_id);
                });
            },
            'soal_ujian.jadwal_ujian' => function($query){
                $query->with('jadwal', 'mata_pelajaran');
            }
        ])->where(function($query){
            $query->where('soal_ujian_id', request()->soal_ujian_id);
            $query->where('nomor_jawaban', request()->nomor_jawaban);
        })->first();
        $take = ($soal) ? $soal->soal_ujian->jumlah_soal : 10;
        $data = [
            'soal' => $soal,
            'all_soal' => Jawaban_soal::with([
                'jawaban_pd' => function($query){
                    $query->whereHas('pd', function($query){
                        $query->where('peserta_didik.peserta_didik_id', request()->user()->peserta_didik_id);
                    });
                }
            ])->where(function($query){
                $query->where('soal_ujian_id', request()->soal_ujian_id);
            })->orderBy('nomor_jawaban')->take($take)->get(),
        ];
        return response()->json($data);
    }
    public function simpan(){
        Jawaban_pd::updateOrCreate(
            [
                'jawaban_soal_id' => request()->jawaban_soal_id,
                'anggota_rombel_id' => request()->anggota_rombel_id,
                'nomor_jawaban' => request()->nomor_jawaban,
            ],
            [
                'jawaban' => request()->jawaban,
                'is_ragu' => (request()->ragu) ? 1 : 0,
            ]
        );
        $actual_start_at = Carbon::now();
        $actual_end_at   = Carbon::parse(date('Y-m-d H:i:s', strtotime(request()->waktu)));
        $waktu = $actual_end_at->diffInSeconds($actual_start_at, true);

        $data = [
            'ujian_siswa' => Ujian_siswa::updateOrCreate(
                [
                    'user_id' => request()->user()->id,
                    'soal_ujian_id' => request()->soal_ujian_id,
                ],
                [
                    'waktu' => $waktu,
                ],
            ),
            'waktu' => $waktu,
        ];
        //$data = request()->all();
        return response()->json($data);
    }
    public function selesai(){
        $this->simpan();
        $insert = Ujian_siswa::where(function($query){
            $query->where('soal_ujian_id', request()->soal_ujian_id);
            $query->where('user_id', request()->user()->id);
        })->update(['status' => 1]);
        if($insert){
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Ujian berhasil disimpan',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Ujian gagal disimpan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
}
