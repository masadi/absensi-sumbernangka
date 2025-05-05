<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absen;
use App\Models\Izin;
use App\Models\Peserta_didik;
use App\Models\Libur;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StatistikController extends Controller
{
    public function index(){
        if(request()->bulan){
            $now = date('Y').'-'.request()->bulan.'-01';
            $firstOfMonth = Carbon::create($now)->firstOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::create($now)->endOfMonth()->format('Y-m-d');
            $bulan = Carbon::create($now)->endOfMonth()->format('m');
        } else {
            $firstOfMonth = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
            $bulan = Carbon::now()->endOfMonth()->format('m');
        }
        $firstOfMonth = $firstOfMonth.' 00:00:00';
        $endOfMonth = $endOfMonth. ' 23:59:59';
        $period = CarbonPeriod::between($firstOfMonth, $endOfMonth)->addFilter(function ($date) {
            return !$date->isFriday();
            //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
        });
        $ahadir = Absen::where(function($query) use ($firstOfMonth, $endOfMonth, $bulan){
            if(request()->aksi == 'ptk'){
                $query->whereNotNull('ptk_id');
            } else {
                $query->withWhereHas('pd', function($query){
                    $query->select('peserta_didik_id');
                    $query->withWhereHas('kelas', function($query){
                        $query->select('rombongan_belajar.rombongan_belajar_id', 'nama');
                        $query->where('rombongan_belajar.semester_id', semester_id());
                    });
                });
            }
            $query->whereBetween('created_at', [$firstOfMonth, $endOfMonth]);
            $query->whereNotIn('created_at', $this->hari_libur($bulan));
            $query->has('absen_masuk');
        })->orderBy('created_at')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d');
        });
        $aizin = Absen::where(function($query) use ($firstOfMonth, $endOfMonth, $bulan){
            if(request()->aksi == 'ptk'){
                $query->whereNotNull('ptk_id');
            } else {
                $query->withWhereHas('pd', function($query){
                    $query->select('peserta_didik_id');
                    $query->withWhereHas('kelas', function($query){
                        $query->select('rombongan_belajar.rombongan_belajar_id', 'nama');
                        $query->where('rombongan_belajar.semester_id', semester_id());
                    });
                });
            }
            $query->whereBetween('created_at', [$firstOfMonth, $endOfMonth]);
            $query->whereNotIn('created_at', $this->hari_libur($bulan));
            $query->has('izin');
        })->orderBy('created_at')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d');
        });
        $aalpa = Absen::where(function($query) use ($firstOfMonth, $endOfMonth, $bulan){
            if(request()->aksi == 'ptk'){
                $query->whereNotNull('ptk_id');
            } else {
                $query->withWhereHas('pd', function($query){
                    $query->select('peserta_didik_id');
                    $query->withWhereHas('kelas', function($query){
                        $query->select('rombongan_belajar.rombongan_belajar_id', 'nama');
                        $query->where('rombongan_belajar.semester_id', semester_id());
                    });
                });
            }
            $query->whereBetween('created_at', [$firstOfMonth, $endOfMonth]);
            $query->whereNotIn('created_at', $this->hari_libur($bulan));
            $query->doesnthave('absen_masuk');
        })->orderBy('created_at')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d');
        });
        $hadir = [];
        $izin = [];
        $alpa = [];
        foreach ($period as $date) {
            $hadir[] = isset($ahadir[$date->format('d')]) ? $ahadir[$date->format('d')]->count() : 0;
            $izin[] = isset($aizin[$date->format('d')]) ? $aizin[$date->format('d')]->count() : 0;
            $alpa[] = isset($aalpa[$date->format('d')]) ? $aalpa[$date->format('d')]->count() : 0;
            $categories[] = $date->format('d');
        }
        $data = [
            'semester_id' => semester_id(),
            'series' => [
                [
                    'name' => 'Hadir',
                    'data' => $hadir,
                ],
                [
                    'name' => 'Izin/Sakit',
                    'data' => $izin,
                ],
                [
                    'name' => 'Tidak Hadir',
                    'data' => $alpa,
                ],
            ],
            'chartOptions' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 350
                ],
                'plotOptions' => [
                    'bar' => [
                        'horizontal' => false,
                        'columnWidth' => '55%',
                        'endingShape' => 'rounded',
                    ],
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
                'stroke' => [
                    'show' => true,
                    'width' => 2,
                    'colors' => ['transparent']
                ],
                'xaxis' => [
                    'categories' => $categories,
                ],
                'fill' => [
                    'opacity' => 1,
                    'colors' => ['#198754', '#ffc107', '#dc3545']
                ],
                'legend' => [
                    'markers' => [
                        'fillColors' => ['#198754', '#ffc107', '#dc3545']
                    ],
                ]
            ],
            'bulan' => (request()->bulan) ? request()->bulan : date('m'),
            'firstOfMonth' => $firstOfMonth, 
            'endOfMonth' => $endOfMonth,
            'ahadir' => $ahadir,
            'aizin' => $aizin,
            'aalpa' => $aalpa,
        ];
        return response()->json($data);
    }
    public function cek_libur($bulan){
        return Libur::whereMonth('mulai_tanggal', $bulan)->orWhereMonth('sampai_tanggal', $bulan)->get();
    }
    public function hari_libur($bulan){
        $hari_libur = [];
        foreach($this->cek_libur($bulan) as $libur){
            $period = CarbonPeriod::between($libur->mulai_tanggal, $libur->sampai_tanggal)->addFilter(function ($date) {
                return !$date->isFriday();
                //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
            });
            $hari_libur[] = collect($period->map(function (Carbon $date){
                return $date->format('Y-m-d');
            }));
        }
        $tanggal_libur = [];
        foreach($hari_libur as $period){
            foreach($period as $date){
                $tanggal_libur[$date] = $date;
            }
        }
        return $tanggal_libur;
    }
}
