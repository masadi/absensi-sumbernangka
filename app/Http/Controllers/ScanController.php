<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Peserta_didik;
use App\Models\Ptk;
use App\Models\Absen;
use App\Models\Absen_masuk;
use App\Models\Absen_pulang;
use App\Models\Semester;
use App\Models\Jam_pd;
use App\Models\Jam_ptk;
use App\Models\Izin;
use App\Models\Libur;
use Carbon\Carbon;
//use Pusher\Pusher;
use App\Events\ScanEvent;

class ScanController extends Controller
{
    public function index(){
        $id = Str::isUuid(request()->id);
        $libur = NULL;
        if($id){
            $peserta_didik = Peserta_didik::find(request()->id);
            if($peserta_didik){
                $libur = $this->check_libur($peserta_didik->sekolah_id);
                if($libur){
                    $data = $this->toastr('danger', 'Absen Gagal', 'Hari ini '. Carbon::now()->translatedFormat('d F Y').', Libur!!!', 'Dong.mp3');
                } else {
                    $data = $this->proses_absen_pd();
                }
            } else {
                $ptk = Ptk::find(request()->id);
                if($ptk){
                    $libur = $this->check_libur($ptk->sekolah_id);
                    if($libur){
                        $data = $this->toastr('danger', 'Absen Gagal', 'Hari ini '. Carbon::now()->translatedFormat('d F Y').', Libur!!!', 'Dong.mp3');
                    } else {
                        $data = $this->proses_absen_ptk();
                    }
                } else {
                    $data = $this->toastr('danger', 'Absen Gagal', 'Data Peserta Didik/PTK tidak ditemukan!', 'Dong.mp3');
                }
            }
        } else {
            $data = $this->toastr('danger', 'Absen Gagal', 'Data Peserta Didik/PTK tidak ditemukan!', 'Dong.mp3');
        }
        $data['libur'] = $libur;
        return response()->json($data);
    }
    public function toastr($type, $title, $message, $mp3, $absen = NULL){
        $data = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'mp3' => $mp3,
            'semester_id' => $this->getSemester(),
            'request' => request()->all(),
            'tanggal_mulai' => ('2024-01-02' <= Carbon::now()->format('Y-m-d')),
            'tanggal_selesai' => ('2024-06-30' >= Carbon::now()->format('Y-m-d')),
            'absen' => $absen,

        ];
        event(new ScanEvent($data));
        return $data;
    }
    public function check_libur($sekolah_id){
        $libur = Libur::where(function($query) use ($sekolah_id){
            $query->whereHas('kategori_libur', function($query) use ($sekolah_id){
                $query->where('sekolah_id', $sekolah_id);
            });
            $query->where('mulai_tanggal', '<=', Carbon::now()->format('Y-m-d'));
            $query->where('sampai_tanggal', '>=', Carbon::now()->format('Y-m-d'));
            $query->orWhereHas('kategori_libur', function($query){
                $query->whereNull('sekolah_id');
            });
            $query->where('mulai_tanggal', '<=', Carbon::now()->format('Y-m-d'));
            $query->where('sampai_tanggal', '>=', Carbon::now()->format('Y-m-d'));
        })->first();
        return $libur;
    }
    private function proses_absen_ptk(){
        $jam_ptk = Jam_ptk::with(['jam', 'ptk.izin_harian' => function($query){
            $query->where('izin.created_at', Carbon::now()->format('Y-m-d'));
        }])->where(function($query){
            $query->where('ptk_id', request()->id);
            $query->whereHas('jam', function($query){
                $query->whereHas('semester', function($query){
                    $query->where('semester_id', $this->getSemester()->semester_id);
                    $query->where('tanggal_mulai', '<=', Carbon::now()->format('Y-m-d'));
                    $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
            $query->whereHas('hari', function($query){
                $query->where('jam_hari.nama', Carbon::now()->translatedFormat('l'));
            });
        })->first();
        //dump($this->getSemester()->semester_id);
        //dd($jam_ptk);
        if($jam_ptk){
            if($jam_ptk->ptk->izin_harian){
                $keterangan = $jam_ptk->ptk->izin_harian->keterangan;
                $alasan = ($jam_ptk->ptk->izin_harian->alasan) ? ' dengan alasan '.$jam_ptk->ptk->izin_harian->alasan : '';
                if($keterangan == 'izin'){
                    $notif = 'izin tidak masuk'.$alasan;
                } else {
                    $notif = 'izin '.ucwords($keterangan).''.$alasan;
                }
                $data = $this->toastr('danger', 'Absen Gagal', 'Hari ini Anda terdeteksi memiliki '.$notif, 'Dong.mp3');
            } else {
                if(check_scan_masuk_start($jam_ptk->jam->scan_masuk_start)){
                    if(check_scan_masuk_end($jam_ptk->jam->scan_masuk_end)){
                        $absen = insert_absen_ptk(request()->id, $this->getSemester()->semester_id);
                        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
                        if($absen_masuk){
                            $data = $this->toastr('danger', 'Absen Gagal', 'Absen masuk '.$jam_ptk->ptk->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
                        } else {
                            $from = Carbon::createFromFormat('H:i:s', $jam_ptk->jam->waktu_akhir_masuk);
                            $to = Carbon::createFromFormat('H:i:s', $this->jam_sekarang()->format('H:i:s'));
                            Absen_masuk::updateOrCreate(
                                [
                                    'absen_id' => $absen->id,
                                ],
                                [
                                    'terlambat' => (Str::contains($from->diffInMinutes($to, false), '-')) ? 0 : $from->diffInMinutes($to, false),
                                ]
                            );
                            $absen->ptk = $jam_ptk->ptk;
                            $data = $this->toastr('success', 'Absen Masuk hari ini berhasil disimpan', 'Selamat Datang '.$jam_ptk->ptk->nama, 'Ding.mp3', $absen);
                        }
                    } elseif(check_scan_pulang_start($jam_ptk->jam->scan_pulang_start)){
                        if(check_scan_pulang_end($jam_ptk->jam->scan_pulang_end)){
                            $data = $this->proses_absen_pulang_ptk($jam_ptk);
                        } else {
                            $data = $this->toastr('danger', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    } else {
                        if(check_scan_pulang_end($jam_ptk->jam->scan_pulang_end)){
                            $data = $this->proses_absen_pulang_ptk($jam_ptk);
                        } else {
                            $data = $this->toastr('danger', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    }
                } else {
                    $data = $this->toastr('danger', 'Absen Gagal', 'Belum waktunya scan masuk!', 'Dong.mp3');
                }
            }
        } else {
            $data = $this->toastr('danger', 'Absen Gagal', 'Setting JAM PTK tidak ditemukan!', 'Dong.mp3');
        }
        return $data;
    }
    private function getSemester(){
        return Semester::where('periode_aktif', 1)->first();
    }
    public function jam_sekarang(){
        return Carbon::now();
    }
    private function proses_absen_pd(){
        $jam_pd = Jam_pd::with(['jam', 'pd' => function($query){
            $query->with([
                'izin_harian' => function($query){
                    $query->where('izin.created_at', Carbon::now()->format('Y-m-d'));
                },
                'kelas' => function($query){
                    $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
                }
            ]);
        }])->where(function($query){
            $query->where('peserta_didik_id', request()->id);
            $query->whereHas('jam', function($query){
                $query->whereHas('semester', function($query){
                    $query->where('semester_id', $this->getSemester()->semester_id);
                    $query->where('tanggal_mulai', '<=', Carbon::now()->format('Y-m-d'));
                    $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
            $query->whereHas('hari', function($query){
                $query->where('jam_hari.nama', Carbon::now()->translatedFormat('l'));
            });
        })->first();
        if($jam_pd){
            if(!$jam_pd->pd->izin_harian){
                if(check_scan_masuk_start($jam_pd->jam->scan_masuk_start)){
                    if(check_scan_masuk_end($jam_pd->jam->scan_masuk_end)){
                        $absen = insert_absen(request()->id, $this->getSemester()->semester_id);
                        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
                        if($absen_masuk){
                            $data = $this->toastr('danger', 'Absen Gagal', 'Absen masuk '.$jam_pd->pd->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
                        } else {
                            $from = Carbon::createFromFormat('H:i:s', $jam_pd->jam->waktu_akhir_masuk);
                            $to = Carbon::createFromFormat('H:i:s', $this->jam_sekarang()->format('H:i:s'));
                            $masuk = Absen_masuk::updateOrCreate(
                                [
                                    'absen_id' => $absen->id,
                                ],
                                [
                                    'terlambat' => (Str::contains($from->diffInMinutes($to, false), '-')) ? 0 : $from->diffInMinutes($to, false),
                                ]
                            );
                            $absen->peserta_didik = $jam_pd->pd;
                            $absen->absen_masuk = $masuk;
                            $data = $this->toastr('success', 'Absen Masuk hari ini berhasil disimpan', 'Selamat Datang '.$jam_pd->pd->nama, 'Ding.mp3', $absen);
                        }
                    } elseif(check_scan_pulang_start($jam_pd->jam->scan_pulang_start)){
                        if(check_scan_pulang_end($jam_pd->jam->scan_pulang_end)){
                            $data = $this->proses_absen_pulang_pd($jam_pd);
                        } else {
                            $data = $this->toastr('danger', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    } else {
                        if(check_scan_pulang_end($jam_pd->jam->scan_pulang_end)){
                            $data = $this->proses_absen_pulang_pd($jam_pd);
                        } else {
                            $data = $this->toastr('danger', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    }
                } else {
                    $data = $this->toastr('danger', 'Absen Gagal', 'Belum waktunya scan masuk!', 'Dong.mp3');
                }
            } else {
                $data = $this->toastr('danger', 'Absen Gagal', 'Terdeteksi Anda memiliki izin untuk hari ini!', 'Dong.mp3');
            }
        } else {
            $data = $this->toastr('danger', 'Absen Gagal', 'Setting JAM PD tidak ditemukan!', 'Dong.mp3');
        }
        return $data;
    }
    private function proses_absen_pulang_pd($jam_pd){
        $absen = insert_absen(request()->id, $this->getSemester()->semester_id);
        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
        if($absen_masuk){
            $absen_pulang = Absen_pulang::where('absen_id', $absen->id)->first();
            if($absen_pulang){
                $data = $this->toastr('danger', 'Absen Gagal', 'Absen pulang '.$jam_pd->pd->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
            } else {
                $from = $this->jam_sekarang()->format('H:i:s');
                $to = Carbon::createFromFormat('H:i:s', $jam_pd->jam->scan_pulang_start);
                $pulang = Absen_pulang::updateOrCreate(
                    [
                        'absen_id' => $absen->id,
                    ],
                    [
                        //'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? 0 : $to->diffInMinutes($from, false),
                        'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? $to->diffInMinutes($from) : 0,
                        //$to->diffInMinutes($from),
                    ]
                );
                $absen->peserta_didik = $jam_pd->pd;
                $absen->absen_pulang = $pulang;
                $data = $this->toastr('success', 'Absen Pulang berhasil disimpan', $jam_pd->pd->nama. ' telah scan pulang', 'Ding.mp3', $absen);
                //$this->notif_pulang_siswa($absen);
            }
        } else {
            $data = $this->toastr('danger', 'Absen Gagal', $jam_pd->pd->nama.' hari ini belum absen masuk', 'Dong.mp3');
        }
        return $data;
    }
    private function proses_absen_pulang_ptk($jam_ptk){
        $absen = insert_absen_ptk(request()->id, $this->getSemester()->semester_id);
        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
        if($absen_masuk){
            $absen_pulang = Absen_pulang::where('absen_id', $absen->id)->first();
            if($absen_pulang){
                $data = $this->toastr('danger', 'Absen Gagal', 'Absen pulang '.$jam_ptk->ptk->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
            } else {
                $from = $this->jam_sekarang()->format('H:i:s');
                $to = Carbon::createFromFormat('H:i:s', $jam_ptk->jam->scan_pulang_start);
                $absen_pulang = Absen_pulang::updateOrCreate(
                    [
                        'absen_id' => $absen->id,
                    ],
                    [
                        //'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? 0 : $to->diffInMinutes($from, false),
                        'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? $to->diffInMinutes($from) : 0,
                        //'pulang_cepat' => $to->diffInMinutes($from),
                    ]
                );
                $absen->absen_pulang = $absen_pulang;
                $absen->ptk = $jam_ptk->ptk;
                $data = $this->toastr('success', 'Absen Pulang '.$jam_ptk->ptk->nama.' berhasil disimpan', 'Terima kasih', 'Ding.mp3'. $absen);
                //$this->notif_pulang_guru($absen);
            }           
        } else {
            $data = $this->toastr('danger', 'Absen Gagal', $jam_ptk->ptk->nama.' hari ini belum absen masuk', 'Dong.mp3');
        }
    }
    public function display(){
        $data = [
            'siswa_masuk' => Absen::with(['peserta_didik' => function($query){
                $query->with(['kelas' => function($query){
                    $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
                }]);
            }])->where(function($query){
                $query->whereDate('created_at', Carbon::today());
                $query->has('peserta_didik');
            })->whereHas('absen_masuk', function($query){
                $query->where('terlambat', 0);
            })->orderBy('created_at', 'DESC')->limit(10)->get(),
            'siswa_lambat' => Absen::with(['peserta_didik' => function($query){
                $query->with(['kelas' => function($query){
                    $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
                }]);
            }])->where(function($query){
                $query->whereDate('created_at', Carbon::today());
                $query->has('peserta_didik');
            })->withWhereHas('absen_masuk', function($query){
                $query->where('terlambat', '>', 0);
            })->orderBy('created_at', 'DESC')->limit(10)->get(),
            'pulang_cepat' => Absen::with(['peserta_didik' => function($query){
                $query->with(['kelas' => function($query){
                    $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
                }]);
            }])->where(function($query){
                $query->whereDate('created_at', Carbon::today());
                $query->has('peserta_didik');
            })->withWhereHas('absen_pulang', function($query){
                $query->where('pulang_cepat', '>', 0);
            })->orderBy('created_at', 'DESC')->limit(10)->get(),
        ];
        return response()->json($data);
    }
}
