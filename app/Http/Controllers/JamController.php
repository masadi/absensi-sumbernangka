<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jam;
use App\Models\Sekolah;
use App\Models\Ptk;
use App\Models\Peserta_didik;
use App\Models\Jam_hari;
use App\Models\Jam_ptk;
use App\Models\Jam_pd;
use App\Models\Nama_hari;
use App\Models\Semester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\Rule;

class JamController extends Controller
{
    public function index(){
        $data = Jam::withCount(['jam_ptk', 'jam_pd', 'jam_hari'])->with(['jam_hari', 'sekolah' => function($query){
            $query->select('sekolah_id', 'nama');
        }])->where(function($query){
            $query->where('semester_id', request()->semester_id);
        })->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            $query->orWhereHas('data_ptk', function($query){
                $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            });
            $query->orWhereHas('data_pd', function($query){
                $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            });
        })->paginate(request()->per_page);
        return response()->json($data);
    }
    public function referensi(){
        $data = [
            'sekolah' => Sekolah::select('sekolah_id', 'nama')->get(),
            'data_hari' => Nama_hari::select('nama')->get(),
        ];
        return response()->json($data);
    }
    public function simpan(){
        request()->validate(
            [
                'sekolah_id' => 'required',
                'nama' => 'required',
                'hari' => 'required|array',
                'scan_masuk_start' => 'required|date_format:H:i:s',
                'scan_masuk_end' => 'required|date_format:H:i:s',
                'waktu_akhir_masuk' => 'required|date_format:H:i:s',
                'scan_istirahat_start' => 'required|date_format:H:i:s',
                'scan_istirahat_end' => 'required|date_format:H:i:s',
                'waktu_akhir_istirahat' => 'required|date_format:H:i:s',
                'scan_pulang_start' => 'required|date_format:H:i:s',
                'scan_pulang_end' => 'required|date_format:H:i:s',
            ],
            [
                'sekolah_id.required' => 'Unit tidak boleh kosong!!',
                'nama.required' => 'Nama tidak boleh kosong!!',
                'hari.required' => 'Hari tidak boleh kosong!!',
                'scan_masuk_start' => [
                    'required' => 'Jam Mulai Scan Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Masuk format salah!',
                ],
                'scan_masuk_end' => [
                    'required' => 'Jam Akhir Scan Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Akhir Scan Masuk format salah!',
                ],
                'waktu_akhir_masuk' => [
                    'required' => 'Jam Waktu Akhir Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Waktu Akhir Masuk format salah!',
                ],
                'scan_istirahat_start' => [
                    'required' => 'Jam Mulai Scan Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Masuk format salah!',
                ],
                'scan_istirahat_end' => [
                    'required' => 'Jam Akhir Scan Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Akhir Scan Masuk format salah!',
                ],
                'waktu_akhir_istirahat' => [
                    'required' => 'Jam Waktu Akhir Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Waktu Akhir Masuk format salah!',
                ],
                'scan_pulang_start' => [
                    'required' => 'Jam Mulai Scan Pulang tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Pulang format salah!',
                ],
                'scan_pulang_start' => [
                    'required' => 'Jam Mulai Scan Pulang tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Pulang format salah!',
                ],
            ]
        );
        $insert = NULL;
        $jam = Jam::create([
            'sekolah_id' => (request()->sekolah_id) ? request()->sekolah_id : NULL,
            'semester_id' => request()->semester_id,
            'untuk' => 'all',//request()->untuk,
            'nama' => request()->nama,
            'is_libur' => (request()->is_libur) ?? 0,
            'tanggal_mulai' => (request()->tanggal_mulai) ? date('Y-m-d', strtotime(request()->tanggal_mulai)) : NULL,
            'tanggal_akhir' => (request()->tanggal_akhir) ? date('Y-m-d', strtotime(request()->tanggal_akhir)) : NULL,
            'scan_masuk_start' => request()->scan_masuk_start,
            'scan_masuk_end' => request()->scan_masuk_end,
            'waktu_akhir_masuk' => request()->waktu_akhir_masuk,
            'scan_istirahat_start' => request()->scan_istirahat_start,
            'scan_istirahat_end' => request()->scan_istirahat_end,
            'waktu_akhir_istirahat' => request()->waktu_akhir_istirahat,
            'scan_pulang_start' => request()->scan_pulang_start,
            'scan_pulang_end' => request()->scan_pulang_end,
        ]);
        if(request()->hari){
            foreach(request()->hari as $hari){
                $insert = Jam_hari::create([
                    'nama' => $hari,
                    'jam_id' => $jam->id,
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        }
        if($insert){
            $data = [
                'icon' => 'success',
                'text' => 'Jam berhasil disimpan',
                'title' => 'Berhasil',
            ];            
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Jam disimpan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function update(){
        request()->validate(
            [
                'sekolah_id' => 'required',
                'nama' => 'required',
                'hari' => 'required|array',
                'scan_masuk_start' => 'required|date_format:H:i:s',
                'scan_masuk_end' => 'required|date_format:H:i:s',
                'waktu_akhir_masuk' => 'required|date_format:H:i:s',
                'scan_istirahat_start' => 'required|date_format:H:i:s',
                'scan_istirahat_end' => 'required|date_format:H:i:s',
                'waktu_akhir_istirahat' => 'required|date_format:H:i:s',
                'scan_pulang_start' => 'required|date_format:H:i:s',
                'scan_pulang_end' => 'required|date_format:H:i:s',
            ],
            [
                'sekolah_id.required' => 'Unit tidak boleh kosong!!',
                'nama.required' => 'Nama tidak boleh kosong!!',
                'hari.required' => 'Hari tidak boleh kosong!!',
                'scan_masuk_start' => [
                    'required' => 'Jam Mulai Scan Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Masuk format salah!',
                ],
                'scan_masuk_end' => [
                    'required' => 'Jam Akhir Scan Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Akhir Scan Masuk format salah!',
                ],
                'waktu_akhir_masuk' => [
                    'required' => 'Jam Waktu Akhir Masuk (Jam 1) tidak boleh kosong!!',
                    'date_format' => 'Jam Waktu Akhir Masuk format salah!',
                ],
                'scan_istirahat_start' => [
                    'required' => 'Jam Mulai Scan Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Masuk format salah!',
                ],
                'scan_istirahat_end' => [
                    'required' => 'Jam Akhir Scan Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Akhir Scan Masuk format salah!',
                ],
                'waktu_akhir_istirahat' => [
                    'required' => 'Jam Waktu Akhir Masuk (Jam 2) tidak boleh kosong!!',
                    'date_format' => 'Jam Waktu Akhir Masuk format salah!',
                ],
                'scan_pulang_start' => [
                    'required' => 'Jam Mulai Scan Pulang tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Pulang format salah!',
                ],
                'scan_pulang_start' => [
                    'required' => 'Jam Mulai Scan Pulang tidak boleh kosong!!',
                    'date_format' => 'Jam Mulai Scan Pulang format salah!',
                ],
            ]
        );
        $insert = NULL;
        $jam = Jam::find(request()->id);
        $jam->nama = request()->nama;
        $jam->scan_masuk_start = request()->scan_masuk_start;
        $jam->scan_masuk_end = request()->scan_masuk_end;
        $jam->waktu_akhir_masuk = request()->waktu_akhir_masuk;
        $jam->scan_istirahat_start = request()->scan_istirahat_start;
        $jam->scan_istirahat_end = request()->scan_istirahat_end;
        $jam->waktu_akhir_istirahat = request()->waktu_akhir_istirahat;
        $jam->scan_pulang_start = request()->scan_pulang_start;
        $jam->scan_pulang_end = request()->scan_pulang_end;
        if($jam->save()){
            if(request()->hari){
                foreach(request()->hari as $hari){
                    $insert = Jam_hari::updateOrCreate(
                        [
                            'jam_id' => $jam->id,
                            'nama' => $hari,
                        ],
                        [
                            'tanggal' => Carbon::now()->format('Y-m-d'),
                        ]
                    );
                }
            }
            Jam_hari::where('jam_id', request()->id)->whereNotIn('nama', request()->hari)->delete();
            $data = [
                'icon' => 'success',
                'text' => 'Jam berhasil diperbaharui',
                'title' => 'Berhasil',
            ];            
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Jam gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function terdaftar(){
        if(request()->data == 'ptk'){
            $data = Ptk::where(function($query){
                $query->whereHas('jam', function($query){
                    $query->where('jam_id', request()->jam_id);
                });
            })->when(request()->filter_nama, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->filter_nama . '%');
            })->paginate(25);
        } else {
            $data = Peserta_didik::where(function($query){
                $query->whereHas('jam', function($query){
                    $query->where('jam_id', request()->jam_id);
                });
            })->when(request()->filter_nama, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->filter_nama . '%');
            })->paginate(25);
        }
        return response()->json($data);
    }
    public function kosong(){
        $jam = Jam::find(request()->jam_id);
        if(request()->data == 'ptk'){
            $data = Ptk::where(function($query) use ($jam){
                $query->whereDoesntHave('jam', function($query){
                    $query->where('jam_id', request()->jam_id);
                });
                if($jam->sekolah_id){
                    $query->where('sekolah_id', $jam->sekolah_id);
                }
            })->when(request()->filter_nama, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->filter_nama . '%');
            })->paginate(25);
        } else {
            $data = Peserta_didik::where(function($query) use ($jam){
                $query->whereDoesntHave('jam', function($query){
                    $query->where('jam_id', request()->jam_id);
                });
                $query->whereHas('anggota_rombel', function($query){
                    $query->whereHas('semester', function($query){
                        $query->where('periode_aktif', 1);
                    });
                });
                if($jam->sekolah_id){
                    $query->where('sekolah_id', $jam->sekolah_id);
                }
            })->when(request()->filter_nama, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->filter_nama . '%');
            })->paginate(25);
        }
        return response()->json($data);
    }
    public function set_anggota(){
        if(request()->aksi == 'in'){
            if(request()->data == 'ptk'){
                Jam_ptk::updateOrCreate([
                    'ptk_id' => request()->id,
                    'jam_id' => request()->jam_id,
                ]);
            } else {
                Jam_pd::updateOrCreate([
                    'peserta_didik_id' => request()->id,
                    'jam_id' => request()->jam_id,
                ]);
            }
        } elseif(request()->aksi == 'all-in'){
            $all_pd = Peserta_didik::where(function($query){
                $query->whereDoesntHave('jam', function($query){
                    $query->where('jam_id', request()->jam_id);
                });
                $query->whereHas('anggota_rombel', function($query){
                    $query->whereHas('semester', function($query){
                        $query->where('periode_aktif', 1);
                    });
                });
            })->select('peserta_didik_id')->get();
            foreach($all_pd as $pd){
                Jam_pd::updateOrCreate([
                    'peserta_didik_id' => $pd->peserta_didik_id,
                    'jam_id' => request()->jam_id,
                ]);
            }
        } elseif(request()->aksi == 'all-out'){
            Jam_pd::where('jam_id', request()->jam_id)->delete();
        } else {
            if(request()->data == 'ptk'){
                Jam_ptk::where('jam_id', request()->jam_id)->where('ptk_id', request()->id)->delete();
            } else {
                Jam_pd::where('jam_id', request()->jam_id)->where('peserta_didik_id', request()->id)->delete();
            }
        }
    }
    public function detil(){
        $data = Jam::with(['hari', 'data_ptk', 'data_pd'])->find(request()->id);
        return response()->json($data);
    }
    public function hapus(){
        $find = Jam::find(request()->id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'text' => 'Jam berhasil dihapus',
                    'title' => 'Berhasil',
                ]; 
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Jam gagal dihapus. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Jam tidak ditemukan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
