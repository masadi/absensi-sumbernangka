<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\RekapSemua;
use App\Exports\RekapSatuan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ptk;
use App\Models\Peserta_didik;
use App\Models\Rombongan_belajar;
use App\Models\Jadwal;
use App\Models\Jadwal_ujian;
use App\Models\Absen;
use App\Models\Libur;
use App\Models\Sekolah;
use App\Models\Catatan_ujian;
use App\Models\Nama_hari;
use App\Models\Hari_ujian;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PDF;

class CetakController extends Controller
{
    //start
    //end
    //absen_id
    public function index(Request $request){
        if($request->route('jenis') == 'harian'){
            if($request->route('data') == 'ptk'){
                $content = Absen::with(['ptk' => function($query){
                    $query->with(['absen' => function($query){
                        $query->where('semester_id', semester_id());
                        $query->withCount(['absen_masuk as terlambat' => function($query){
                            $query->where('terlambat', '>', 0);
                        }]);
                        $query->withCount(['absen_pulang as pulang_cepat' => function($query){
                            $query->where('pulang_cepat', '>', 0);
                        }]);
                        $query->with(['absen_masuk', 'absen_pulang']);
                        /*$query->with(['absen_masuk' => function($query){
                            $query->withSum('terlambat');
                        }, 'absen_pulang'  => function($query){
                            $query->withSum('pulang_cepat as total_comments');
                        }]);*/
                        $query->whereDate('created_at', '>=', request()->route('start'));
                        $query->whereDate('created_at', '<=', request()->route('end'));
                        $query->orderBy('created_at');
                    }]);
                }])->find($request->route('id'));
            } else {
                $content = Absen::find($request->route('id'));
            }
        } else {
            if($request->route('data') == 'ptk'){
                $content = Ptk::with(['absen' => function($query){
                    $query->where('semester_id', semester_id());
                    /*$query->with(['absen_masuk' => function($query){
                        $query->withSum('terlambat');
                    }, 'absen_pulang'  => function($query){
                        $query->withSum('pulang_cepat as total_comments');
                    }]);*/
                    $query->with(['absen_masuk', 'absen_pulang']);
                    $query->whereDate('created_at', '>=', request()->route('start'));
                    $query->whereDate('created_at', '<=', request()->route('end'));
                    $query->orderBy('created_at');
                }])->find($request->route('id'));
            } else {
                $content = Peserta_didik::with(['absen' => function($query){
                    $query->where('semester_id', semester_id());
                    $query->with(['absen_masuk', 'absen_pulang']);
                    $query->whereDate('created_at', '>=', request()->route('start'));
                    $query->whereDate('created_at', '<=', request()->route('end'));
                    $query->orderBy('created_at');
                }])->find($request->route('id'));
            }
        }
        $data = [
            'start' => Carbon::parse($request->route('start'))->translatedFormat('d F Y'),
            'startStr' => $request->route('start'),
            'endStr' => $request->route('end'),
            'end' => Carbon::parse($request->route('end'))->translatedFormat('d F Y'),
            'from' => $request->route('start'),
            'to' => $request->route('end'),
            'data' => $content,
            'ptk' => ($request->route('data') == 'ptk') ? TRUE : FALSE,
            'jenis' => $request->route('jenis'),
        ];
        $pdf = PDF::loadView('cetak.satuan-'.$request->route('jenis').'-pdf', $data, [], [
            'margin_footer' => 5,
            'orientation' => ($request->route('jenis') == 'rekap') ? 'L' : 'P',
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
		$pdf->getMpdf()->defaultfooterline=1;
		$pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.Carbon::now()->translatedFormat('d F Y'));
        return $pdf->stream('document.pdf');
    }
    public function excel(Request $request){
        if($request->route('jenis') == 'harian'){
            $content = Absen::find($request->route('id'));
            if($content->ptk_id){
                $nama = $content->ptk->nama;
            } else {
                $nama = $content->pd->nama;
            }
        } else {
            if($request->route('data') == 'ptk'){
                $content = Ptk::with(['absen' => function($query){
                    $query->where('semester_id', semester_id());
                    $query->with(['absen_masuk', 'absen_pulang']);
                }])->find($request->route('id'));
            } else {
                $content = Peserta_didik::find($request->route('id'));
            }
            $nama = $content->nama;
        }
        $data = [
            'start' => Carbon::parse($request->route('start'))->translatedFormat('d F Y'),
            'startStr' => $request->route('start'),
            'endStr' => $request->route('end'),
            'end' => Carbon::parse($request->route('end'))->translatedFormat('d F Y'),
            'from' => $request->route('start'),
            'to' => $request->route('end'),
            'data' => $content,
            'ptk' => ($request->route('data') == 'ptk') ? TRUE : FALSE,
            'jenis' => $request->route('jenis'),
        ];
        return (new RekapSatuan)
        ->query($data)
        ->download('laporan-presensi-'.$nama.'-'.Carbon::now()
        ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function semua_pdf(Request $request){
        $data = [
            'start' => Carbon::parse($request->route('start'))->translatedFormat('d F Y'),
            'end' => Carbon::parse($request->route('end'))->translatedFormat('d F Y'),
            'from' => $request->route('start'),
            'to' => $request->route('end'),
            'collection_pd' => ($request->route('jenis') == 'pd' || $request->route('jenis') == 'all') ? Peserta_didik::with([
                'absen' => function($query){
                    $query->whereDate('created_at', '>=', request()->route('start'));
                    $query->whereDate('created_at', '<=', request()->route('end'));
                },
                'absen_masuk' => function($query){
                    $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                    $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                },
                'absen_pulang' => function($query){
                    $query->whereDate('absen_pulang.created_at', '>=', request()->route('start'));
                    $query->whereDate('absen_pulang.created_at', '<=', request()->route('end'));
                }
            ])->get() : NULL,
            'collection_ptk' => ($request->route('jenis') == 'ptk' || $request->route('jenis') == 'all') ? Ptk::with([
                'absen' => function($query){
                    $query->whereDate('created_at', '>=', request()->route('start'));
                    $query->whereDate('created_at', '<=', request()->route('end'));
                },
                'absen_masuk' => function($query){
                    $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                    $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                },
                'absen_pulang' => function($query){
                    $query->whereDate('absen_pulang.created_at', '>=', request()->route('start'));
                    $query->whereDate('absen_pulang.created_at', '<=', request()->route('end'));
                }
            ])->get() : NULL,
        ];
        $title = 'rekapitulasi-presensi-'.Carbon::now()
        ->translatedFormat('d-F-Y');
        $pdf = PDF::loadView('cetak.semua-pdf', $data, [], [
            'title'      => $title,
            'orientation' => 'L',
            'margin_footer' => 5,
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
		$pdf->getMpdf()->defaultfooterline=1;
		$pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.Carbon::now()->translatedFormat('d F Y'));
        return $pdf->stream($title.'.pdf');
    }
    public function semua_excel(Request $request){
        return (new RekapSemua)
        ->query($request->route('jenis'), $request->route('start'), $request->route('end'))
        ->download('rekapitulasi-presensi-'.Carbon::now()
        ->translatedFormat('d-F-Y').'.xlsx');
    }
    public function semua_qrcode(){
        $asal = request()->route('asal');
        $data = [];
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [54, 85.1],
        ]);
        //'format'        => [54, 85.6],
        //'format'        => [54, 76],
        $sekolah = Sekolah::find(request()->route('sekolah_id'));
        //dd($sekolah->bentuk_pendidikan_id);
        $jenjang = 'smp';
        if($sekolah->bentuk_pendidikan_id == 15){
            $jenjang = 'smk';
        }
        if($asal == 'ptk'){
            $collection = Ptk::select('ptk_id', 'nuptk', 'nama', 'photo')->where('sekolah_id', request()->route('sekolah_id'))->orderBy('nama')->paginate(10);
        } else {
            $collection = Peserta_didik::select('peserta_didik_id', 'nisn', 'nama', 'photo', 'tempat_lahir', 'tanggal_lahir')->whereNotNull('photo')->where('sekolah_id', request()->route('sekolah_id'))->orderBy('nama')->paginate(10);
        }
        foreach($collection as $key => $item){
            if($key > 0){
                $pdf->getMpdf()->WriteHTML('<pagebreak />');
            }
            if($asal == 'ptk'){
                $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($item->ptk_id));
            } else {
                $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($item->peserta_didik_id));
            }
            $params = [
                'item' => $item,
                'qrcode' => $qrcode,
                'bentuk_pendidikan_id' => $sekolah->bentuk_pendidikan_id,
            ];
            $html = view('cetak.kartu-'.$asal.'-'.$jenjang, $params);
			$pdf->getMpdf()->WriteHTML($html);
        }
        return $pdf->stream('qrcode-pd.pdf');
    }
    
    public function id_card(){
        $asal = request()->route('asal');
        $id = request()->route('id');
        $data = [
            'item' => ($asal == 'ptk') ? Ptk::select('ptk_id', 'nuptk', 'nama', 'photo', 'sekolah_id')->with(['sekolah' => function($query){
                $query->select('sekolah_id', 'bentuk_pendidikan_id');
            }])->find($id) : Peserta_didik::select('peserta_didik_id', 'nisn', 'nama', 'photo', 'tempat_lahir', 'tanggal_lahir', 'sekolah_id')->with(['sekolah' => function($query){
                $query->select('sekolah_id', 'bentuk_pendidikan_id');
            }, 'kelas' => function($query){
                $query->where('rombongan_belajar.semester_id', semester_id());
            }])->find($id),
            'qrcode' => base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($id??'string')),
            'bentuk_pendidikan_id' => NULL,
        ];
        if($asal == 'ptk'){
            $nama = $data['item']->nama;
        } else {
            $nama = $data['item']->nama.'-'.$data['item']->kelas?->nama;
        }
        $jenjang = 'smp';
        if($data['item']->sekolah->bentuk_pendidikan_id == 15){
            $jenjang = 'smk';
        }
        $pdf = PDF::loadView('cetak.kartu-'.$asal.'-'.$jenjang, $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [54, 85.1],
        ]);
        $pdf->showImageErrors = true;
        return $pdf->stream(clean($nama).'.pdf');
    }
    public function id_pelajar(){
        $id = request()->route('id');
        $data['item'] = Peserta_didik::select('peserta_didik_id', 'sekolah_id', 'nisn', 'nama', 'photo', 'tempat_lahir', 'tanggal_lahir')->with([
            'kelas' => function($query){
                $query->where('rombongan_belajar.semester_id', semester_id());
            },
            'sekolah' => function($query){
                $query->select('sekolah_id', 'bentuk_pendidikan_id');
            },
        ])->find($id);
        //$view = 'kartu-pelajar';
        $view = 'kartu-pelajar';
        $pdf = PDF::loadView('cetak.'.$view, $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [85.6, 54],
        ]);
        return $pdf->stream(clean($data['item']->nama).'.pdf');
    }
    public function id_pkl(){
        $id = request()->route('id');
        $data = [
            'item' => Peserta_didik::select('peserta_didik_id', 'nisn', 'nama', 'photo')->with(['kelas' => function($query){
                $query->where('rombongan_belajar.semester_id', semester_id());
            }])->find($id),
        ];
        $nama = $data['item']->nama;
        $pdf = PDF::loadView('cetak.kartu-pkl', $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [54, 85.6],
        ]);
        return $pdf->stream(clean($nama).'.pdf');
    }
    public function id_anggota(){
        $collection = Peserta_didik::select('peserta_didik_id', 'nisn', 'nama', 'photo')->whereNotNull('photo')->whereHas('anggota_rombel', function($query){
            $query->where('rombongan_belajar_id', request()->route('rombongan_belajar_id'));
        })->orderBy('nama')->get();
        $data = [];
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [54, 85.6],
        ]);
        foreach($collection as $key => $item){
            if($key > 0){
                $pdf->getMpdf()->WriteHTML('<pagebreak />');
            }
            $params = [
                'item' => $item,
                'qrcode' => base64_encode(QrCode::format('svg')->size(150)->errorCorrection('H')->generate($item->peserta_didik_id??'string')),
            ];
            $html = view('cetak.kartu-pd', $params);
			$pdf->getMpdf()->WriteHTML($html);
        }
        return $pdf->stream('qrcode-anggota-rombel.pdf');
    }
    public function kartu_pelajar(){
        $collection = Peserta_didik::select('peserta_didik_id', 'sekolah_id', 'nisn', 'nama', 'photo', 'tempat_lahir', 'tanggal_lahir')->with([
            'sekolah' => function($query){
                $query->select('sekolah_id', 'bentuk_pendidikan_id');
            },
        ])->withWhereHas('kelas', function($query){
            $query->where('rombongan_belajar.rombongan_belajar_id', request()->route('rombongan_belajar_id'));
        })->orderBy('nama')->get();
        $data = [];
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [85.6, 54],
        ]);
        foreach($collection as $key => $item){
            if($key > 0){
                $pdf->getMpdf()->WriteHTML('<pagebreak />');
            }
            $params = [
                'item' => $item,
            ];
            $html = view('cetak.kartu-pelajar', $params);
			$pdf->getMpdf()->WriteHTML($html);
        }
        return $pdf->stream('kartu-pelajar-anggota-rombel.pdf');
    }
    public function kartu_pkl(){
        $collection = Peserta_didik::select('peserta_didik_id', 'sekolah_id', 'nisn', 'nama', 'photo', 'tempat_lahir', 'tanggal_lahir')->with([
            'sekolah' => function($query){
                $query->select('sekolah_id', 'bentuk_pendidikan_id');
            },
        ])->withWhereHas('kelas', function($query){
            $query->where('rombongan_belajar.rombongan_belajar_id', request()->route('rombongan_belajar_id'));
        })->orderBy('nama')->get();
        $data = [];
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'default_font_size' => '8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'format'        => [54, 85.6],
        ]);
        foreach($collection as $key => $item){
            if($key > 0){
                $pdf->getMpdf()->WriteHTML('<pagebreak />');
            }
            $params = [
                'item' => $item,
            ];
            $html = view('cetak.kartu-pkl', $params);
			$pdf->getMpdf()->WriteHTML($html);
        }
        return $pdf->stream('kartu-pkl-anggota-rombel.pdf');
    }
    public function tanggal_cetak(){
        return Carbon::now()->translatedFormat('d F Y');
    }
    private function jml_hari_libur($sekolah_id, $from, $to, $period, $nama_hari){
        $libur = Libur::where(function($query) use ($from, $to, $period, $sekolah_id){
            $query->whereHas('kategori_libur', function($query) use ($sekolah_id){
                $query->where('sekolah_id', $sekolah_id);
                $query->orWhereNull('sekolah_id');
            });
            $query->whereDate('mulai_tanggal', '>=', $from);
            $query->whereDate('sampai_tanggal', '<=', $to);
            $query->where(function($query) use($period) {
                collect($period->map(function (Carbon $date) use ($query){
                    $query->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                    $query->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
                }));
            });
        })->get();
        $all_libur = [];
        foreach($libur as $li){
            $all_libur = CarbonPeriod::between($li->mulai_tanggal, $li->sampai_tanggal)
            ->addFilter(function ($date) use ($nama_hari){
                return in_array($date->translatedFormat('l'), $nama_hari);
            });
        }
        return $all_libur;
    }
    public function pdf_rekap(){
        $ids = base64_decode(request()->route('id'));
        $ids = explode(',', $ids);
        $data = [
            'start' => Carbon::parse(request()->route('start'))->translatedFormat('d F Y'),
            'end' => Carbon::parse(request()->route('end'))->translatedFormat('d F Y'),
            'from' => request()->route('start'),
            'to' => request()->route('end'),
            //'collection_pd' => (request()->route('data') == 'pd') ? Peserta_didik::whereIn('peserta_didik_id', $id)->get() : NULL,
            //'collection_ptk' => (request()->route('data') == 'ptk') ? Ptk::whereIn('ptk_id', $id)->get() : NULL,
        ];
        $title = 'rekapitulasi-presensi-'.Carbon::now()
        ->translatedFormat('d-F-Y');
        $period = CarbonPeriod::between($data['from'], $data['to'])->addFilter(function ($date) {
            return !$date->isFriday();
            //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
        });
        $libur = Libur::select('mulai_tanggal')->where(function($query) use ($period){
            $query->whereDate('mulai_tanggal', '>=', request()->route('start'));
            $query->whereDate('sampai_tanggal', '<=', request()->route('end'));
            $query->where(function($query) use($period) {
                collect($period->map(function (Carbon $date) use ($query){
                    $query->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                    $query->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
                }));
            });
        })->get();
        $hari_libur = NULL;
        foreach ($libur as $value) {
            $hari_libur[] = date('Y-m-d', strtotime($value->mulai_tanggal));
        }
        $callback = function($query) use ($period, $hari_libur){
            //$query->where('semester_id', semester_id());
            $query->with(['absen_masuk', 'absen_pulang']);
            //$query->whereBetween('created_at', [request()->route('start'), request()->route('end')]);
            $query->whereDate('created_at', '>=', request()->route('start'));
            $query->whereDate('created_at', '<=', request()->route('end'));
            if($hari_libur){
                $query->whereNotIn('created_at', $hari_libur);
            }
            $query->orderBy('created_at');
        };
        if(request()->route('format') == 'harian'){
            $pdf = PDF::loadView('cetak.blank', $data, [], [
                'title'      => $title,
                'margin_footer' => 5,
                //'orientation' => 'L',
                //'default_font_size' => '10',
            ]);
            $pdf->getMpdf()->defaultfooterfontsize=7;
            $pdf->getMpdf()->defaultfooterline=1;
            $pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.$this->tanggal_cetak());
            foreach($ids as $key => $id){
                if($key > 0){
                    $pdf->getMpdf()->WriteHTML('<pagebreak />');
                }
                $params = [
                    'period' => $period,
                    'start' => $data['start'],
                    'startStr' => $data['from'],
                    'endStr' => $data['to'],
                    'end' => $data['end'],
                    'from' => $data['from'],
                    'to' => $data['to'],
                    'data' => (request()->route('data') == 'pd') ? Peserta_didik::with(['absen' => $callback])->find($id) : Ptk::with(['absen' => $callback])->find($id),
                    'ptk' => (request()->route('data') == 'ptk') ? TRUE : FALSE,
                    'tanggal_cetak' => $this->tanggal_cetak(),
                    //'jenis' => $request->route('jenis'),
                    /*
                    content = Ptk::with(['absen' => function($query){
                        $query->where('semester_id', semester_id());
                        $query->with(['absen_masuk', 'absen_pulang']);
                        $query->whereBetween('created_at', [request()->route('start'), request()->route('end')]);
                        $query->orderBy('created_at');
                    }])->find($request->route('id'));
                    */
                ];
                $rapor_catatan = view('cetak.rekap-pdf', $params);
                $pdf->getMpdf()->WriteHTML($rapor_catatan);
            }
        } else {
            $data = [
                'sekolah' => ($this->loggedUser()->hasRole(['unit'], semester_id())) ? Sekolah::find($this->loggedUser()->sekolah_id) : NULL,
                'period' => $period,
                'start' => $data['start'],
                'startStr' => $data['from'],
                'endStr' => $data['to'],
                'end' => $data['end'],
                'from' => $data['from'],
                'to' => $data['to'],
                'data' => (request()->route('data') == 'pd') ? Peserta_didik::with(['absen' => $callback])->find($ids) : Ptk::with(['absen' => $callback])->find($ids),
                'ptk' => (request()->route('data') == 'ptk') ? TRUE : FALSE,
                'tanggal_cetak' => $this->tanggal_cetak(),
            ];
            $pdf = PDF::loadView('cetak.laporan-bulanan-tu-pdf', $data, [], [
                'title'      => $title,
                'orientation' => 'L',
                'margin_footer' => 5,
            ]);
            $pdf->getMpdf()->defaultfooterfontsize=7;
            $pdf->getMpdf()->defaultfooterline=1;
            $pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.Carbon::now()->translatedFormat('d F Y'));
        }
        return $pdf->stream($title.'.pdf');
    }
    private function loggedUser(){
        return auth()->user();
    }
    public function excel_rekap(){
        //
    }
    public function pdf_libur(){
        $data = [
            'start' => Carbon::parse(request()->route('start'))->translatedFormat('d F Y'),
            'end' => Carbon::parse(request()->route('end'))->translatedFormat('d F Y'),
            'from' => request()->route('start'),
            'to' => request()->route('end'),
            'hari_libur' => Libur::where(function($query){
                $query->whereDate('mulai_tanggal', '>=', request()->route('start'));
                $query->whereDate('sampai_tanggal', '<=', request()->route('end'));
            })->get(),
        ];
        $title = 'rekapitulasi-libur';
        $pdf = PDF::loadView('cetak.libur-pdf', $data, [], [
            'title'      => $title,
            'orientation' => 'L',
            'margin_footer' => 5,
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
		$pdf->getMpdf()->defaultfooterline=1;
		$pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.Carbon::now()->translatedFormat('d F Y'));
        return $pdf->stream($title.'.pdf');        
    }
    public function jadwal(){
        $data = [];
        $title = 'cetak-jadwal-'.request()->route('jenis');
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'title'      => $title,
            'margin_footer' => 5,
            //'orientation' => 'L',
            //'default_font_size' => '10',
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
        $pdf->getMpdf()->defaultfooterline=1;
        $pdf->getMpdf()->SetFooter('Dicetak dari App '.config('app.name').' v.'.config('app.version').'|{PAGENO}| Tanggal Cetak '.$this->tanggal_cetak());
        $jadwal = Jadwal::with([
            'ptk' => function($query){
                $query->select('ptk_id', 'nama');
            },
            'rombongan_belajar' => function($query){
                $query->select('rombongan_belajar_id', 'nama', 'sekolah_id', 'semester_id');
                $query->with([
                    'sekolah' => function($query){
                        $query->select('sekolah_id', 'nama', 'ptk_id');
                        $query->with(['kasek' => function($query){
                            $query->select('ptk_id', 'nama');
                        }]);
                    },
                    'semester' => function($query){
                        $query->select('semester_id', 'nama');
                    }
                ]);
            },
            'jadwal_ujian' => function($query){
                $query->orderBy('tanggal');
                $query->orderBy('jam_ke');
                $query->with('mata_pelajaran');
            },
            'catatan_ujian',
        ])->find(request()->route('jadwal_id'));
        $data_pd = Peserta_didik::whereHas('anggota_rombel', function($query) use ($jadwal){
            $query->where('rombongan_belajar_id', $jadwal->rombongan_belajar_id);
        })->with(['kelas' => function($query) use ($jadwal){
            $query->where('rombongan_belajar.rombongan_belajar_id', $jadwal->rombongan_belajar_id);
        }])->orderBy('nama')->get();
        //$data_catatan = Catatan_ujian::where('jadwal_id', request()->route('jadwal_id'))->get();
        //$nama_smt = str_replace(date('Y').'/'.(date('Y')+1), '', semester_id());
        //$nama_tapel = str_replace('Ganjil', '', str_replace('Genap', '', semester_id()));
        /*$nama_hari = Hari_ujian::whereHas('jadwal_ujian', function($query){
            $query->where('rombongan_belajar_id', request()->route('rombongan_belajar_id'));
            $query->where('jenis', request()->route('jenis'));
        })->with(['jadwal_ujian' => function($query){
            $query->where('rombongan_belajar_id', request()->route('rombongan_belajar_id'));
            $query->where('jenis', request()->route('jenis'));
            $query->orderBy('hari_id', 'ASC');
            $query->orderBy('jam_ke', 'ASC');
        }])->orderBy('id')->get();
        @if($jenis == 'PTS')
                    PENILAIAN TENGAH SEMESTER {{$nama_smt}}
                    @else
                    PENILAIAN AKHIR SEMESTER {{$nama_smt}}
                    @endif
        if(request()->route('jenis') == 'pts'){
            $nama_ujian = 'PENILAIAN TENGAH SEMESTER '.$nama_smt;
        } elseif(request()->route('jenis') == 'uas'){
            $nama_ujian = 'PENILAIAN AKHIR SEMESTER '.$nama_smt;
        } else {
            $nama_ujian = 'Ujian Satuan Pendidikan (USP) ';
        }
        */
        foreach($data_pd as $key => $pd){
            /*if(request()->route('jenis') == 'pts'){
                $no_peserta = strtoupper(request()->route('jenis')).$nama_smt.'_'.$pd->nisn;
            } elseif(request()->route('jenis') == 'uas'){
                $no_peserta = strtoupper(request()->route('jenis')).$nama_smt.'_'.$pd->nisn;
            } else {
                $no_peserta = strtoupper(request()->route('jenis')).'_'.$pd->nisn;
            }*/
            if($key > 0){
                $pdf->getMpdf()->WriteHTML('<pagebreak />');
            }
            $params = [
                'jadwal' => $jadwal,
                //'nama_ujian' => $nama_ujian,
                //'rombongan_belajar' => $rombongan_belajar,
                //'tanggal' => Carbon::createFromTimeStamp(strtotime(request()->route('tanggal')))->translatedFormat('j F Y'),
                //'ptk' => $ptk,
                //'jadwal_ujian' => $jadwal_ujian,
                //'data_catatan' => $data_catatan,
                'pd' => $pd,
                //'sekolah' => $sekolah,
                //'nama_smt' => trim(strtoupper($nama_smt)),
                //'nama_tapel' => trim($nama_tapel),
                //'nama_hari' => $nama_hari,
                //'no_peserta' => $pd->nisn,
            ];
            $rapor_catatan = view('cetak.jadwal-ujian-pdf', $params);
            $pdf->getMpdf()->WriteHTML($rapor_catatan);
        }
        return $pdf->stream($title.'.pdf');
    }
    public function keterlambatan(){
        $data = [
            'jenis' => request()->route('jenis'),
            'start' => request()->route('start'),
            'end' => request()->route('end'),
            'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
            'endtStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
        ];
        $title = 'laporan-keterlambatan-'.request()->route('jenis');
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'title'      => $title,
            'margin_footer' => 5,
            //'orientation' => 'L',
            //'default_font_size' => '10',
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
        $pdf->getMpdf()->defaultfooterline=1;
        $pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.$this->tanggal_cetak());
        if(request()->route('jenis') == 'pd'){
            $data_pd = Absen::with(
                [
                'pd' => function($query){
                    $query->select('peserta_didik_id', 'nama', 'nisn', 'sekolah_id');
                    $query->with([
                        'sekolah' => function($query){
                            $query->select('sekolah_id', 'nama');
                        }, 
                        'kelas' => function($query){
                            $query->where('anggota_rombel.semester_id', semester_id());
                            $query->select('nama');
                        },
                        'absen_masuk' => function($query){
                            $query->where('terlambat', '>', 0);
                            $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                            $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                        }
                    ]
                    );
                    /*$query->withCount(['absen_masuk' => function($query){
                        $query->where('terlambat', '>', 0);
                        $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                        $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                    }]);*/
                }
                ]
            )->where(function($query){
                $query->whereHas('absen_masuk', function($query){
                    $query->where('terlambat', '>', 0);
                });
                $query->has('pd');
                $query->whereDate('created_at', '>=', request()->route('start'));
                $query->whereDate('created_at', '<=', request()->route('end'));
            })->orderBy('created_at')->get();
            $params = [
                'data' => $data_pd,
                'start' => request()->route('start'),
                'end' => request()->route('end'),
                'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
                'endStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
            ];
            $html = view('cetak.keterlambatan-pd', $params);
            $pdf->getMpdf()->WriteHTML($html);
            return $pdf->stream($title.'.pdf');
        } elseif(request()->route('jenis') == 'ptk'){
            $data_pd = Absen::with(
                [
                'ptk' => function($query){
                    $query->select('ptk_id', 'nama', 'sekolah_id');
                    $query->with([
                        'sekolah' => function($query){
                            $query->select('sekolah_id', 'nama');
                        }, 
                        'absen_masuk' => function($query){
                            $query->where('terlambat', '>', 0);
                            $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                            $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                        }
                    ]
                    );
                    /*$query->withCount(['absen_masuk' => function($query){
                        $query->where('terlambat', '>', 0);
                        $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                        $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                    }]);*/
                }
                ]
            )->where(function($query){
                $query->whereHas('absen_masuk', function($query){
                    $query->where('terlambat', '>', 0);
                });
                $query->has('ptk');
                $query->whereDate('created_at', '>=', request()->route('start'));
                $query->whereDate('created_at', '<=', request()->route('end'));
            })->orderBy('created_at')->get();
            $params = [
                'data' => $data_pd,
                'start' => request()->route('start'),
                'end' => request()->route('end'),
                'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
                'endStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
            ];
            $html = view('cetak.keterlambatan-ptk', $params);
            $pdf->getMpdf()->WriteHTML($html);
            return $pdf->stream($title.'.pdf');
        } else {
            echo 'permintaan tidak sah!';
        }
    }
    public function ketidakhadiran(){
        $data = [
            'jenis' => request()->route('jenis'),
            'start' => request()->route('start'),
            'end' => request()->route('end'),
            'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
            'endtStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
        ];
        $title = 'laporan-ketidakhadiran-'.request()->route('jenis');
        $pdf = PDF::loadView('cetak.blank', $data, [], [
            'title'      => $title,
            'margin_footer' => 5,
            //'orientation' => 'L',
            //'default_font_size' => '10',
        ]);
        $pdf->getMpdf()->defaultfooterfontsize=7;
        $pdf->getMpdf()->defaultfooterline=1;
        $pdf->getMpdf()->SetFooter('Dicetak dari App '.config('settings.app_name').' v.'.config('settings.app_version').'|{PAGENO}| Tanggal Cetak '.$this->tanggal_cetak());
        if(request()->route('jenis') == 'pd'){
            $data_pd = Absen::with(
                [
                'pd' => function($query){
                    $query->select('peserta_didik_id', 'nama', 'nisn', 'sekolah_id');
                    $query->with([
                        'sekolah' => function($query){
                            $query->select('sekolah_id', 'nama');
                        }, 
                        'kelas' => function($query){
                            $query->where('anggota_rombel.semester_id', semester_id());
                            $query->select('nama');
                        },
                    ]
                    );
                    /*$query->withCount(['absen_masuk' => function($query){
                        $query->where('terlambat', '>', 0);
                        $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                        $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                    }]);*/
                }
                ]
            )->where(function($query){
                $query->doesntHave('absen_masuk');
                $query->has('pd');
                $query->whereDate('created_at', '>=', request()->route('start'));
                $query->whereDate('created_at', '<=', request()->route('end'));
            })->orderBy('created_at')->get();
            $params = [
                'data' => $data_pd,
                'start' => request()->route('start'),
                'end' => request()->route('end'),
                'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
                'endStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
            ];
            $html = view('cetak.ketidakhadiran-pd', $params);
            $pdf->getMpdf()->WriteHTML($html);
            return $pdf->stream($title.'.pdf');
        } elseif(request()->route('jenis') == 'ptk'){
            $data_pd = Absen::with(
                [
                'ptk' => function($query){
                    $query->select('ptk_id', 'nama', 'sekolah_id');
                    $query->with([
                        'sekolah' => function($query){
                            $query->select('sekolah_id', 'nama');
                        }, 
                    ]
                    );
                    /*$query->withCount(['absen_masuk' => function($query){
                        $query->where('terlambat', '>', 0);
                        $query->whereDate('absen_masuk.created_at', '>=', request()->route('start'));
                        $query->whereDate('absen_masuk.created_at', '<=', request()->route('end'));
                    }]);*/
                }
                ]
            )->where(function($query){
                $query->doesntHave('absen_masuk');
                $query->has('ptk');
                $query->whereDate('created_at', '>=', request()->route('start'));
                $query->whereDate('created_at', '<=', request()->route('end'));
            })->orderBy('created_at')->get();
            $params = [
                'data' => $data_pd,
                'start' => request()->route('start'),
                'end' => request()->route('end'),
                'startStr' => Carbon::createFromTimeStamp(strtotime(request()->route('start')))->translatedFormat('j F Y'),
                'endStr' => Carbon::createFromTimeStamp(strtotime(request()->route('end')))->translatedFormat('j F Y'),
            ];
            $html = view('cetak.ketidakhadiran-ptk', $params);
            $pdf->getMpdf()->WriteHTML($html);
            return $pdf->stream($title.'.pdf');
        } else {
            echo 'permintaan tidak sah!';
        }
    }
    public function pulang_cepat(){
        echo 'on progress!';
    }
}
