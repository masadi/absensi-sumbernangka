<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sekolah;
use App\Models\Rombongan_belajar;
use App\Models\Peserta_didik;
use App\Models\Anggota_rombel;
use App\Models\Ptk;

class RombelController extends Controller
{
    public function index(){
        $data_sekolah = [];
        $user = auth()->user();
        if($user->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = Sekolah::select('sekolah_id', 'nama')->get();
        }
        $data = Rombongan_belajar::with([
            'walas' => function($query){
                $query->select('ptk_id', 'nama');
            },
            'sekolah' => function($query){
                $query->select('sekolah_id', 'nama');
            }
        ])->orderBy(request()->sortby, request()->sortbydesc)
        ->where(function($query) use ($user){
            if($user->sekolah_id){
                $query->where('sekolah_id', $user->sekolah_id);
            }
            $query->where('semester_id', request()->semester_id);
        })
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            $query->orWhereHas('walas', function($query){
                $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            });
        })
        ->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => $data_sekolah]);
    }
    public function get_rombel(){
        $data = [
            'rombel' => Rombongan_belajar::where(function($query){
                $query->where('semester_id', request()->semester_id);
                $query->where('sekolah_id', request()->sekolah_id);
                if(request()->tingkat){
                    $query->where('tingkat_pendidikan_id', request()->tingkat);
                }
            })->orderBy('nama')->get(),
            'waktu' => now()->format('H:i:s'),
        ];
        return response()->json($data);
    }
    public function get_tingkat(){
        $tingkat = [];
        if(request()->sekolah_id){
            $sekolah = Sekolah::find(request()->sekolah_id);
            if($sekolah->bentuk_pendidikan_id == 1){
                $tingkat = [
                    [
                        'id' => 'KelA',
                        'nama' => "Kelompok A"
                    ],
                    [
                        'id' => 'KelB',
                        'nama' => "Kelompok B"
                    ],
                    [
                        'id' => 'KB',
                        'nama' => "KB"
                    ],
                    [
                        'id' => 'TPA',
                        'nama' => "TPA"
                    ],
                    [
                        'id' => 'SPS',
                        'nama' => "SPS"
                    ],
                ];
            }
            if($sekolah->bentuk_pendidikan_id == 5){
                $tingkat = [
                    [
                        'id' => 1,
                        'nama' => "Kelas 1",
                    ],
                    [
                        'id' => 2,
                        'nama' => "Kelas 2",
                    ],
                    [
                        'id' => 3,
                        'nama' => "Kelas 3",
                    ],
                    [
                        'id' => 4,
                        'nama' => "Kelas 4",
                    ],
                    [
                        'id' => 5,
                        'nama' => "Kelas 5",
                    ],
                    [
                        'id' => 6,
                        'nama' => "Kelas 6",
                    ],
                ];
            }
            if($sekolah->bentuk_pendidikan_id == 6){
                $tingkat = [
                    [
                        'id' => 7,
                        'nama' => "Kelas 7",
                    ],
                    [
                        'id' => 8,
                        'nama' => "Kelas 8",
                    ],
                    [
                        'id' => 9,
                        'nama' => "Kelas 9",
                    ],
                ];
            }
            if($sekolah->bentuk_pendidikan_id == 15){
                $tingkat = [
                    [
                        'id' => 10,
                        'nama' => 'Tingkat 10',
                    ],
                    [
                        'id' => 11,
                        'nama' => 'Tingkat 11',
                    ],
                    [
                        'id' => 12,
                        'nama' => 'Tingkat 12',
                    ],
                    [
                        'id' => 13,
                        'nama' => 'Tingkat 13',
                    ],
                ];
            }
            $data = [
                'tingkat' => $tingkat,
                'ptk' => Ptk::where('sekolah_id', request()->sekolah_id)->orderBy('nama')->get(),
            ];
        }
        if(request()->bentuk_pendidikan_id == 1){
            $data = [
                [
                    'id' => 'KelA',
                    'nama' => "Kelompok A"
                ],
                [
                    'id' => 'KelB',
                    'nama' => "Kelompok B"
                ],
                [
                    'id' => 'KB',
                    'nama' => "KB"
                ],
                [
                    'id' => 'TPA',
                    'nama' => "TPA"
                ],
                [
                    'id' => 'SPS',
                    'nama' => "SPS"
                ],
            ];
        }
        if(request()->bentuk_pendidikan_id == 5){
            $data = [
                [
                    'id' => 1,
                    'nama' => "Kelas 1",
                ],
                [
                    'id' => 2,
                    'nama' => "Kelas 2",
                ],
                [
                    'id' => 3,
                    'nama' => "Kelas 3",
                ],
                [
                    'id' => 4,
                    'nama' => "Kelas 4",
                ],
                [
                    'id' => 5,
                    'nama' => "Kelas 5",
                ],
                [
                    'id' => 6,
                    'nama' => "Kelas 6",
                ],
            ];
        }
        if(request()->bentuk_pendidikan_id == 6){
            $data = [
                [
                    'id' => 7,
                    'nama' => "Kelas 7",
                ],
                [
                    'id' => 8,
                    'nama' => "Kelas 8",
                ],
                [
                    'id' => 9,
                    'nama' => "Kelas 9",
                ],
            ];
        }
        if(request()->bentuk_pendidikan_id == 15){
            $data = [
                [
                    'id' => 10,
                    'nama' => 'Tingkat 10',
                ],
                [
                    'id' => 11,
                    'nama' => 'Tingkat 11',
                ],
                [
                    'id' => 12,
                    'nama' => 'Tingkat 12',
                ],
                [
                    'id' => 13,
                    'nama' => 'Tingkat 13',
                ],
            ];
        }
        return response()->json($data);
    }
    public function simpan(){
        $insert = 0;
        request()->validate(
            [
                'hari_id.*' => 'required',
                'jam_ke.*' => 'required',
                'kelompok_id.*' => 'required',
                'mata_pelajaran_id.*' => 'required',
            ],
            [
                'hari_id.*.required' => 'Hari tidak boleh kosong!',
                'jam_ke.*.required' => 'Jam tidak boleh kosong!',
                'kelompok_id.*.required' => 'Kode Mapel tidak boleh kosong!',
                'mata_pelajaran_id.*.required' => 'Mata Pelajaran tidak boleh kosong!',
            ]
        );
        foreach(request()->hari_id as $urut => $hari_id){
            $insert = Jadwal_ujian::create([
                'jenis' => request()->aksi,
                'rombongan_belajar_id' => request()->rombongan_belajar_id,
                'mata_pelajaran_id' => request()->mata_pelajaran_id,
                'hari_id' => request()->hari_id,
                'jam_ke' => request()->jam_ke,
            ]);
        }
        if($insert){
            $data = [
                'icon' => 'success',
                'text' => 'Jadwal '.strtoupper(request()->aksi).' berhasil disimpan',
                'title' => 'Berhasil',
            ];            
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Jadwal '.strtoupper(request()->aksi).' gagal disimpan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function get_sekolah(){
        $data = Sekolah::orderBy('bentuk_pendidikan_id')->get();
        return response()->json($data);
    }
    public function simpan_rombel(){
        request()->validate(
            [
                'sekolah_id' => 'required',
                'tingkat' => 'required',
                'nama' => 'required',
                'nama_jurusan' => 'required',
                'ptk_id' => 'required',
            ],
            [
                'sekolah_id.required' => 'Unit tidak boleh kosong!',
                'tingkat.required' => 'Tignkat tidak boleh kosong!',
                'nama.required' => 'Nama Rombel tidak boleh kosong!',
                'nama_jurusan.required' => 'Nama Jurusan tidak boleh kosong!',
                'ptk_id.required' => 'Wali Kelas tidak boleh kosong!',
            ]
        );
        $insert = Rombongan_belajar::create([
            'rombongan_belajar_id' => Str::uuid(),
            'sekolah_id' => request()->sekolah_id,
            'semester_id' => request()->semester_id,
            'nama' => request()->nama,
            'tingkat_pendidikan_id' => request()->tingkat,
            'ptk_id' => request()->ptk_id,
            'nama_jurusan' => request()->nama_jurusan,
        ]);
        if($insert){
            $data = [
                'icon' => 'success',
                'text' => 'Rombongan Belajar berhasil disimpan',
                'title' => 'Berhasil',
            ];            
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Rombongan Belajar gagal disimpan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function anggota_rombel(){
        $data = Peserta_didik::whereHas('anggota_rombel', function($query){
            $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
        })->orderBy('nama')->get();
        return response()->json($data);
    }
    public function non_anggota(){
        $rombel = Rombongan_belajar::find(request()->rombongan_belajar_id);
        $data = Peserta_didik::where(function($query) use ($rombel){
            $query->where('sekolah_id', $rombel->sekolah_id);
            $query->whereDoesntHave('anggota_rombel', function($query){
                $query->whereHas('rombongan_belajar', function($query){
                    $query->where('semester_id', request()->semester_id);
                });
            });
            $query->whereDoesntHave('pd_keluar', function($query){
                $query->where('semester_id', request()->semester_id);
            });
        })->when(request()->filter_nama, function($query) {
            $query->where('nama', 'ilike', '%'.request()->filter_nama.'%');
            $query->orWhere('nisn', 'ilike', '%'.request()->filter_nama.'%');
        })->orderBy('nama')->get();
        return response()->json($data);
    }
    public function set_anggota(){
        if(request()->data == 'masukkan'){
            $insert = Anggota_rombel::create([
                'anggota_rombel_id' => Str::uuid(),
                'peserta_didik_id' => request()->peserta_didik_id,
                'rombongan_belajar_id' => request()->rombongan_belajar_id,
                'semester_id' => request()->semester_id,
            ]);
        } else {
            $insert = Anggota_rombel::where(function($query){
                $query->where('peserta_didik_id', request()->peserta_didik_id);
                $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
            })->delete();
        }
        if($insert){
            $data = [
                'icon' => 'success',
                'text' => 'Peserta Didik berhasil di'. request()->aksi,
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Peserta Didik gagal di'. request()->aksi .'. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function edit_rombel(){
        $rombel = Rombongan_belajar::with('sekolah')->find(request()->rombongan_belajar_id);
        if($rombel->sekolah->bentuk_pendidikan_id == 1){
            $tingkat = [
                [
                    'id' => 'KelA',
                    'nama' => "Kelompok A"
                ],
                [
                    'id' => 'KelB',
                    'nama' => "Kelompok B"
                ],
                [
                    'id' => 'KB',
                    'nama' => "KB"
                ],
                [
                    'id' => 'TPA',
                    'nama' => "TPA"
                ],
                [
                    'id' => 'SPS',
                    'nama' => "SPS"
                ],
            ];
        }
        if($rombel->sekolah->bentuk_pendidikan_id == 5){
            $tingkat = [
                [
                    'id' => 1,
                    'nama' => "Kelas 1",
                ],
                [
                    'id' => 2,
                    'nama' => "Kelas 2",
                ],
                [
                    'id' => 3,
                    'nama' => "Kelas 3",
                ],
                [
                    'id' => 4,
                    'nama' => "Kelas 4",
                ],
                [
                    'id' => 5,
                    'nama' => "Kelas 5",
                ],
                [
                    'id' => 6,
                    'nama' => "Kelas 6",
                ],
            ];
        }
        if($rombel->sekolah->bentuk_pendidikan_id == 6){
            $tingkat = [
                [
                    'id' => 7,
                    'nama' => "Kelas 7",
                ],
                [
                    'id' => 8,
                    'nama' => "Kelas 8",
                ],
                [
                    'id' => 9,
                    'nama' => "Kelas 9",
                ],
            ];
        }
        if($rombel->sekolah->bentuk_pendidikan_id == 15){
            $tingkat = [
                [
                    'id' => 10,
                    'nama' => 'Tingkat 10',
                ],
                [
                    'id' => 11,
                    'nama' => 'Tingkat 11',
                ],
                [
                    'id' => 12,
                    'nama' => 'Tingkat 12',
                ],
                [
                    'id' => 13,
                    'nama' => 'Tingkat 13',
                ],
            ];
        }
        $data = [
            'rombel' => $rombel,
            'tingkat' => $tingkat,
            'ptk' => Ptk::where('sekolah_id', request()->sekolah_id)->orderBy('nama')->get(),
        ];
        return response()->json($data);
    }
    public function update_rombel(){
        $find = Rombongan_belajar::with('sekolah')->find(request()->rombongan_belajar_id);
        $find->nama = request()->nama;
        $find->tingkat_pendidikan_id = request()->tingkat;
        $find->ptk_id = request()->ptk_id;
        $find->nama_jurusan = request()->nama_jurusan;
        if($find->save()){
            $data = [
                'icon' => 'success',
                'text' => 'Rombongan Belajar berhasil diperbaharui',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Rombongan Belajar gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
