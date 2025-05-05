<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Jam;
use App\Models\Absen;
use App\Models\Absen_masuk;
use App\Models\Absen_pulang;
use App\Models\Jam_ptk;
use App\Models\Izin;
use App\Models\Libur;
use App\Models\Semester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LaporanController extends Controller
{
    public function kehadiran(){
        $data_sekolah = [];
        $user = auth()->user();
        if($user->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = data_sekolah();
        }
        if(request()->aksi == 'guru'){
            $withWhereHas = 'ptk';
        } else {
            $withWhereHas = 'pd';
        }
        $data = Absen::where(function($query){
            if(!request()->sampai_tanggal){
                $query->whereMonth('created_at', date('m'));
            }
        })
        ->withWhereHas('absen_masuk')
        ->with(['absen_masuk_pertama', 'absen_masuk_kedua', 'absen_pulang'])
        ->withWhereHas($withWhereHas, function($query) use ($user){
            if($user->sekolah_id){
                $query->where('sekolah_id', $user->sekolah_id);
            }
            if(request()->aksi == 'guru'){
                $query->select('ptk_id', 'nama');
            } else {
                $query->select('peserta_didik_id', 'nama');
            }
        })
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            if(request()->aksi == 'guru'){
                $query->whereHas('ptk', function($query){
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                    $query->orWhere('nuptk', 'ILIKE', '%' . request()->q . '%');
                });
            } else {
                $query->whereHas('pd', function($query){
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                    $query->orWhere('nisn', 'ILIKE', '%' . request()->q . '%');
                });
            }
        })
        ->when(request()->sampai_tanggal, function($query){
            $query->whereDate('created_at', '>=', request()->mulai_tanggal);
            $query->whereDate('created_at', '<=', request()->sampai_tanggal);
        })
        ->when(request()->sekolah_id, function($query){
            if(request()->aksi == 'guru'){
                $query->whereHas('ptk', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            } else {
                $query->whereHas('pd', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            }
        })
        ->paginate(request()->per_page);
        return response()->json([
            'status' => 'success', 
            'data' => $data, 
            'mulai_tanggal' => (request()->mulai_tanggal) ? request()->mulai_tanggal : Carbon::now()->firstOfMonth()->format('Y-m-d'),
            'sampai_tanggal' => (request()->sampai_tanggal) ? request()->sampai_tanggal : now()->format('Y-m-d'),
            'mulai_tanggal_str' => (request()->mulai_tanggal) ? Carbon::parse(request()->mulai_tanggal)->translatedFormat('d F Y') : '',
            'sampai_tanggal_str' => (request()->sampai_tanggal) ? Carbon::parse(request()->sampai_tanggal)->translatedFormat('d F Y') : '',
            'data_sekolah' => $data_sekolah
        ]);
    }
    public function ketidakhadiran(){
        $data_sekolah = [];
        $user = auth()->user();
        if($user->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = data_sekolah();
        }
        if(request()->aksi == 'guru'){
            $withWhereHas = 'ptk';
        } else {
            $withWhereHas = 'pd';
        }
        $data = Absen::where(function($query){
            $query->doesntHave('absen_masuk');
            if(!request()->sampai_tanggal){
                $query->whereMonth('created_at', date('m'));
            }
        })
        ->withWhereHas($withWhereHas, function($query) use ($user){
            if($user->sekolah_id){
                $query->where('sekolah_id', $user->sekolah_id);
            }
            if(request()->aksi == 'guru'){
                $query->select('ptk_id', 'nama');
            } else {
                $query->select('peserta_didik_id', 'nama');
                $query->withWhereHas('kelas', function($query) use ($user){
                    $query->where('rombongan_belajar.semester_id', request()->semester_id);
                });
            }
        })
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            if(request()->aksi == 'guru'){
                $query->whereHas('ptk', function($query){
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                    $query->orWhere('nuptk', 'ILIKE', '%' . request()->q . '%');
                });
            } else {
                $query->whereHas('pd', function($query){
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                    $query->orWhere('nisn', 'ILIKE', '%' . request()->q . '%');
                });
            }
        })
        ->when(request()->sampai_tanggal, function($query){
            $query->whereDate('created_at', '>=', request()->mulai_tanggal);
            $query->whereDate('created_at', '<=', request()->sampai_tanggal);
        })
        ->when(request()->sekolah_id, function($query){
            if(request()->aksi == 'guru'){
                $query->whereHas('ptk', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            } else {
                $query->whereHas('pd', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            }
        })
        ->paginate(request()->per_page);
        //2024-01-18
        return response()->json([
            'status' => 'success', 
            'data' => $data, 
            'mulai_tanggal' => (request()->mulai_tanggal) ? request()->mulai_tanggal : Carbon::now()->firstOfMonth()->format('Y-m-d'),
            'sampai_tanggal' => (request()->sampai_tanggal) ? request()->sampai_tanggal : now()->format('Y-m-d'),
            'mulai_tanggal_str' => (request()->mulai_tanggal) ? Carbon::parse(request()->mulai_tanggal)->translatedFormat('d F Y') : '',
            'sampai_tanggal_str' => (request()->sampai_tanggal) ? Carbon::parse(request()->sampai_tanggal)->translatedFormat('d F Y') : '',
            'data_sekolah' => $data_sekolah,
            'ketidakhadiran' => 1,
        ]);
    }
    public function update_jam(){
        request()->validate(
            [
                'jam_masuk' => [
                    'required',
                ],
                'jam_pulang' => [
                    'required',
                ]
            ],
            [
                'jam_masuk.required' => 'Jam Scan Masuk tidak boleh kosong!',
                'jam_pulang.required' => 'Jam Scan Pulang tidak boleh kosong!',
            ],
        );
        $jam_masuk_str = Str::of(request()->jam_masuk)->explode(':');
        $jam_masuk = request()->jam_masuk;
        if(Str::length($jam_masuk_str->last()) == 1){
            $jam_masuk = $jam_masuk_str[0].':'.$jam_masuk_str[1].':0'.$jam_masuk_str[2];
        }
        $jam_pulang_str = Str::of(request()->jam_pulang)->explode(':');
        $jam_pulang = request()->jam_pulang;
        if(Str::length($jam_pulang_str->last()) == 1){
            $jam_pulang = $jam_pulang_str[0].':'.$jam_pulang_str[1].':0'.$jam_pulang_str[2];
        }
        $absen = Absen::with(['absen_masuk', 'absen_pulang'])->find(request()->id);
        $jam_ptk = Jam::with(['ptk' => function($query) use ($absen){
            $query->whereHas('hari', function($query) use ($absen){
                $query->where('jam_hari.nama', Carbon::create($absen->created_at)->translatedFormat('l'));
            });
        }, 'hari' => function($query) use ($absen){
            $query->where('nama', Carbon::create($absen->created_at)->translatedFormat('l'));
        }])->where(function($query) use ($absen){
            $query->whereHas('ptk', function($query) use ($absen){
                $query->where('ptk_id', $absen->ptk_id);
                $query->whereHas('hari', function($query) use ($absen){
                    //dd($absen->created_at)
                    $query->where('jam_hari.nama', Carbon::create($absen->created_at)->translatedFormat('l'));
                });
                $query->where('semester_id', $absen->semester_id);
            });
        })->first();
        if($jam_ptk){
            $from_masuk = Carbon::createFromFormat('H:i:s', $jam_ptk->waktu_akhir_masuk);
            $to_masuk = Carbon::createFromFormat('H:i:s', $jam_masuk);
            $masuk = Absen_masuk::updateOrCreate(
                [
                    'absen_id' => $absen->id,
                ],
                [
                    'terlambat' => (Str::contains($from_masuk->diffInMinutes($to_masuk, false), '-')) ? 0 : $from_masuk->diffInMinutes($to_masuk, false),
                    'created_at' => Carbon::create($absen->created_at)->format('Y-m-d').' '.$jam_masuk
                ]
            );
            $from_pulang = Carbon::createFromFormat('H:i:s', $jam_pulang);
            $to_pulang = Carbon::createFromFormat('H:i:s', $jam_ptk->scan_pulang_start);
            $pulang = Absen_pulang::updateOrCreate(
                [
                    'absen_id' => $absen->id,
                ],
                [
                    'pulang_cepat' => (Str::contains($from_pulang->diffInMinutes($to_pulang, false), '-')) ? 0 : $from_pulang->diffInMinutes($to_pulang, false),
                    'created_at' => Carbon::create($absen->created_at)->format('Y-m-d').' '.$jam_pulang
                ]
            );
        }
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jam Scan berhasil diperbaharui',
        ];
        return response()->json($data);
    }
    public function update_keterangan(){
        request()->validate(
            [
                'berkas_masuk' => [
                    'nullable',
                    'mimes:jpeg,jpg,png,pdf'
                ],
                'berkas_pulang' => [
                    'nullable',
                    'mimes:jpeg,jpg,png,pdf'
                ]
            ],
            [
                'berkas_masuk.mimes' => 'Berkas Keterangan Masuk harus berekstensi JPEG/JPG/PDF',
                'berkas_pulang.mimes' => 'Berkas Keterangan Pulang harus berekstensi JPEG/JPG/PDF',
            ]
        );
        $absen = Absen::with(['absen_masuk', 'absen_pulang'])->find(request()->id);
        $berkas_masuk = NULL;
        if(request()->berkas_masuk){
            $berkas_masuk = request()->berkas_masuk->store('public/berkas');
            $berkas_masuk = Str::of($berkas_masuk)->basename();
        }
        if($absen->absen_masuk){
            $absen->absen_masuk->keterangan = request()->keterangan_masuk;
            $absen->absen_masuk->dokumen = $berkas_masuk;
            $absen->absen_masuk->save();
        }
        $berkas_pulang = NULL;
        if(request()->berkas_pulang){
            $berkas_pulang = request()->berkas_pulang->store('public/berkas');
            $berkas_pulang = Str::of($berkas_pulang)->basename();
        }
        if($absen->absen_pulang){
            $absen->absen_pulang->keterangan = request()->keterangan_pulang;
            $absen->absen_pulang->dokumen = $berkas_pulang;
            $absen->absen_pulang->save();
        }
        $update = 1;
        if($update){
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Keterangan berhasil disimpan',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Keterangan gagal disimpan. Silahkan coba beberapa saat lagi',
            ];
        }
        return response()->json($data);
    }
    public function tidak_hadir(){
        $data = [
            'absen' => Absen::with([
                'ptk' => function($query){
                    $query->select('ptk_id', 'nama');
                    $query->with(['izin' => function($query){
                        $query->where('absen_id', request()->id);
                        $query->where('izin.keterangan', request()->keterangan);
                    }]);
                },
                'pd' => function($query){
                    $query->select('peserta_didik_id', 'nama');
                    $query->with(['izin' => function($query){
                        $query->where('absen_id', request()->id);
                        $query->where('izin.keterangan', request()->keterangan);
                    }]);
                },
            ])->find(request()->id),
            'keterangan' => ucfirst(request()->keterangan),
        ];
        return response()->json($data);
    }
    public function save_izin(){
        request()->validate(
            [
                'berkas' => [
                    'nullable',
                    'mimes:jpeg,jpg,png,pdf'
                ],
                'berkas' => [
                    'nullable',
                    'mimes:jpeg,jpg,png,pdf'
                ]
            ],
            [
                'berkas.mimes' => 'Berkas Keterangan Masuk harus berekstensi JPEG/JPG/PDF',
                'berkas.mimes' => 'Berkas Keterangan Pulang harus berekstensi JPEG/JPG/PDF',
            ]
        );
        $berkas = NULL;
        if(request()->berkas){
            $berkas = request()->berkas->store('public/berkas');
            $berkas = Str::of($berkas)->basename();
        }
        Izin::updateOrCreate(
            [
                'absen_id' => request()->id,
                'keterangan' => strtolower(request()->keterangan),
            ],
            [
                'alasan' => request()->alasan,
                'berkas' => $berkas,
                'jenis' => request()->jenis,
            ]
        );
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data '.ucfirst(request()->keterangan).' berhasil disimpan',
        ];
        return response()->json($data);
    }
    public function hapus_izin(){
        $find = Izin::find(request()->id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data '.ucfirst(request()->keterangan).' berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Data '.ucfirst(request()->keterangan).' gagal dihapus. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Data '.ucfirst(request()->keterangan).' tidak ditemukan!',
            ];
        }
        return response()->json($data);
    }
    public function cleasing(){
        $semester = Semester::find(semester_id());
        $hari_libur = Libur::where('mulai_tanggal', '>=', $semester->tanggal_mulai)->where('sampai_tanggal', '<=', $semester->tanggal_selesai)->get();
        $periode = [];
        foreach($hari_libur as $libur){
            $period = CarbonPeriod::between($libur->mulai_tanggal, $libur->sampai_tanggal)->addFilter(function ($date) {
                return !$date->isFriday();
                //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
            });
            $periode[] = collect($period->map(function (Carbon $date){
                return $date->format('Y-m-d');
            }));
        }
        $dates = [];
        foreach($periode as $period){
            foreach($period as $date){
                $dates[$date] = $date;
            }
        }
        $deleted = 0;
        foreach($dates as $d){
            $absen = Absen::withWhereHas('absen_masuk')->where(function($query) use ($d){
                $query->has(request()->aksi);
                $query->whereDate('created_at', $d);
            })->with('absen_pulang')->get();
            foreach($absen as $scan){
                if($scan->absen_pulang){
                    $scan->absen_pulang->delete();
                }
                if($scan->absen_masuk->delete()){
                    $deleted++;
                }
            }
        }
        $aksi  = (request()->aksi == 'ptk') ? 'Guru' : 'Peserta Didik';
        $data = [
            'icon' => ($deleted) ? 'success' : 'error',
            'title' => ($deleted) ? 'Berhasil' : 'Gagal',
            'text' => ($deleted) ? 'Cleansing Absen '.$aksi.' berhasil. Sebanyak '.$deleted.' terhapus' : 'Scan pada hari libur tidak ditemukan!',
            'deleted' => $deleted,
            //'period' => $period,
        ];
        return response()->json($data);
    }
    public function scan_manual(){
        $jam_ptk = Jam_ptk::with(['jam'])->where(function($query){
            $query->where('ptk_id', request()->ptk_id);
            $query->whereHas('jam', function($query){
                $query->whereHas('semester', function($query){
                    $query->where('semester_id', request()->semester_id);
                    $query->where('tanggal_mulai', '<=', Carbon::now()->format('Y-m-d'));
                    $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
            $query->whereHas('hari', function($query){
                $query->where('jam_hari.nama', Carbon::create(request()->created_at)->translatedFormat('l'));
            });
        })->first();
        if($jam_ptk){
            $from = Carbon::createFromFormat('H:i:s', $jam_ptk->jam->waktu_akhir_masuk);
            $to = Carbon::createFromFormat('H:i:s', request()->jam_hadir);
            Absen_masuk::updateOrCreate(
                [
                    'absen_id' => request()->id,
                ],
                [
                    'terlambat' => (Str::contains($from->diffInMinutes($to, false), '-')) ? 0 : $from->diffInMinutes($to, false),
                    'keterangan' => request()->keterangan,
                    'created_at' => Carbon::now()->format('Y-m-d').' '.request()->jam_hadir,
                ]
            );
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Scan Masuk berhasil disimpan',
                'hari' => Carbon::create(request()->created_at)->translatedFormat('l'),
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Pengaturan jam tidak ditemukan',
                'hari' => Carbon::create(request()->created_at)->translatedFormat('l'),
            ];
        }
        return response()->json($data);
    }
}
