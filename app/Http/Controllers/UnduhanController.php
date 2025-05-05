<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Exports\PelanggaranExport;
use App\Exports\RekapTingkat;
use App\Exports\RekapRombel;
use App\Exports\RekapPd;
use App\Exports\ExportJawaban;
use App\Exports\AnalisisHasil;
use App\Exports\TemplatePd;
use App\Models\Peserta_didik;
use App\Models\Rombongan_belajar;
use App\Models\Soal_ujian;
use App\Models\Jadwal_ujian;
use App\Models\Jawaban_soal;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class UnduhanController extends Controller
{
    public function __construct()
    {
        $this->sekolah_id = Route::current()->parameters['sekolah_id'] ?? NULL;
        $this->semester_id = Route::current()->parameters['semester_id'] ?? NULL;
        $this->start = Route::current()->parameters['start'] ?? NULL;
        $this->end = Route::current()->parameters['end'] ?? NULL;
        $this->tingkat = Route::current()->parameters['tingkat'] ?? NULL;
        $this->rombongan_belajar_id = Route::current()->parameters['rombongan_belajar_id'] ?? NULL;
        $this->peserta_didik_id = Route::current()->parameters['peserta_didik_id'] ?? NULL;
    }
    public function pelanggaran(){
        return (new PelanggaranExport)
        ->query(['start' => $this->start, 'end' => $this->end, 'sekolah_id' => $this->sekolah_id, 'semester_id' => $this->semester_id])
        ->download('laporan-pelanggaran-'.Carbon::now()
        ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function rekap_tingkat(){
        return (new RekapTingkat)
            ->query(['tingkat' => $this->tingkat, 'start' => $this->start, 'end' => $this->end, 'sekolah_id' => $this->sekolah_id, 'semester_id' => $this->semester_id])
            ->download('rekap-pelanggaran-tingkat-'.$this->tingkat.'-'.Carbon::now()
            ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function rekap_rombel(){
        $rombel = Rombongan_belajar::find($this->rombongan_belajar_id);
            return (new RekapRombel)
            ->query([
                'rombongan_belajar_id' => $this->rombongan_belajar_id, 
                'rombel' => $rombel, 
                'start' => $this->start, 
                'end' => $this->end, 
                'sekolah_id' => $this->sekolah_id,
                'semester_id' => $this->semester_id,
            ])
            ->download('rekap-pelanggaran-kelas'.$rombel->nama.'-'.Carbon::now()
            ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function rekap_pd(){
        $pd = Peserta_didik::with([
            'pelanggaran' => function($query){
                if($this->end){
                    $query->whereDate('tanggal', '>=', $this->start);
                    $query->whereDate('tanggal', '<=', $this->end);
                }
                $query->orderBy('waktu');
            },
            'kelas' => function($query){
                $query->where('sekolah_id', $this->sekolah_id);
                $query->where('rombongan_belajar.semester_id', $this->semester_id);
            }
        ])->find($this->peserta_didik_id);
            return (new RekapPd)
            ->query(['pd' => $pd, 'start' => $this->start, 'end' => $this->end, 'sekolah_id' => $this->sekolah_id, 'semester_id' => $this->semester_id])
            ->download('rekap-pelanggaran-'.$pd->nama.'-'.Carbon::now()
            ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function template_jawaban(){
        $soal_ujian = Soal_ujian::with([
            'jawaban_soal' => function($query){
                $query->orderBy('nomor_jawaban');
            },
            'jadwal_ujian' => function($query){
                $query->with(['rombongan_belajar', 'mata_pelajaran', 'jadwal']);
            },
        ])->find(request()->route('soal_ujian_id'));
        return (new ExportJawaban)
        ->query(['soal_ujian' => $soal_ujian])
        ->download('template-kunci-jawaban-mata-ujian-'.clean($soal_ujian->jadwal_ujian->jadwal->nama).'-mata-pelajaran-'.$soal_ujian->jadwal_ujian->mata_pelajaran->nama.'-kelas-'.$soal_ujian->jadwal_ujian->rombongan_belajar->nama.'.xlsx');
    }
    public function analisis(){
        $jadwal_ujian = Jadwal_ujian::with([
            'jadwal',
            'soal_ujian' => function($query){
                $query->select('id', 'jadwal_ujian_id', 'jumlah_soal', 'jumlah_pg', 'skor_benar', 'skor_salah', 'skala_nilai');
                $query->withCount('ujian_siswa as peserta');
            },
            'mata_pelajaran',
            'rombongan_belajar' => function($query){
                $query->with([
                    'sekolah' => function($query){
                        $query->select('sekolah_id', 'nama', 'ptk_id');
                    }
                ]);
            },
        ])->find(request()->route('jadwal_ujian_id'));
        $data = [
            'jadwal_ujian' => $jadwal_ujian,
            'jawaban_soal' => Jawaban_soal::with([
                'jawaban_benar' => function($query){
                    $query->select('jawaban_pd.anggota_rombel_id');
                    $query->join('jawaban_soal', function ($join) {
                        $join->on('jawaban_pd.jawaban_soal_id', '=', 'jawaban_soal.id');
                        $join->on('jawaban_pd.jawaban', '=', 'jawaban_soal.jawaban');
                        $join->on('jawaban_pd.nomor_jawaban', '=', 'jawaban_soal.nomor_jawaban');
                    });
                },
                'jawaban_salah' => function($query){
                    $query->select('jawaban_pd.anggota_rombel_id');
                    $query->join('jawaban_soal', function ($join) {
                        $join->on('jawaban_pd.jawaban_soal_id', '=', 'jawaban_soal.id');
                        $join->on('jawaban_pd.jawaban', '<>', 'jawaban_soal.jawaban');
                        $join->on('jawaban_pd.nomor_jawaban', '=', 'jawaban_soal.nomor_jawaban');
                    });
                }
            ])->where('soal_ujian_id', $jadwal_ujian->soal_ujian->id)->orderBy('nomor_jawaban')->take($jadwal_ujian->soal_ujian->jumlah_soal)->get(),
            'items' => Peserta_didik::withWhereHas('anggota_rombel', function($query){
                $query->with([
                    'jawaban_pd' => function($query){
                        $query->orderBy('nomor_jawaban');
                        $query->whereHas('jawaban_soal', function($query){
                            $query->whereHas('soal_ujian', function($query){
                                $query->where('jadwal_ujian_id', request()->route('jadwal_ujian_id'));
                            });
                        });
                    },
                ]);
                /*$query->withCount(['jawaban_pd as jawaban_benar' => function($query){
                    $query->join('jawaban_soal', function ($join) {
                        $join->on('jawaban_pd.jawaban_soal_id', '=', 'jawaban_soal.id');
                        $join->on('jawaban_pd.jawaban', '=', 'jawaban_soal.jawaban');
                        $join->on('jawaban_pd.nomor_jawaban', '=', 'jawaban_soal.nomor_jawaban');
                    });
                }]);*/
                $query->select('anggota_rombel_id', 'peserta_didik_id');
                $query->whereHas('rombongan_belajar', function($query){
                    $query->whereHas('jadwal_ujian', function($query){
                        $query->where('id', request()->route('jadwal_ujian_id'));
                    });
                });
            })->select('peserta_didik_id', 'nama', 'nisn')->orderBy('nama')->get(),
        ];
        return (new AnalisisHasil((object) $data))
        ->download('analisis-soal.xlsx');
    }
    public function template_pd(){
        return Excel::download(new TemplatePd([
            'jumlah' => request()->route('jumlah')
        ]), 'Template Siswa.xlsx');
    }
}
