<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ptk;
use App\Models\Peserta_didik;
use App\Models\Semester;
use App\Models\Sekolah;
use Carbon\Carbon;

class RekapitulasiController extends Controller
{
    public function index(){
        $semester = Semester::find(request()->semester_id);
        $user = auth()->user();
        if($user->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = Sekolah::select('sekolah_id', 'nama')->get();
        } else {
            $data_sekolah = NULL;
        }
        if(request()->aksi == 'ptk'){
            $data = Ptk::with(['sekolah' => function($query){
                $query->select('sekolah_id', 'nama');
            }])
            ->withCount(['absen' => function($query){
                $query->has('absen_masuk');
            }, 'izin', 'sakit', 'cuti', 'alpa'])
            ->orderBy('sekolah_id', 'ASC')
            ->orderBy(request()->sortby, request()->sortbydesc)
            ->where(function($query) use ($user){
                if($user->sekolah_id){
                    $query->where('sekolah_id', $user->sekolah_id);
                }
            })
            ->when(request()->q, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                $query->orWhere('nuptk', 'ILIKE', '%' . request()->q . '%');
            })->when(request()->sekolah_id, function($query) {
                $query->where('sekolah_id', request()->sekolah_id);
            })->paginate(request()->per_page);
        } else {
            $data = Peserta_didik::with([
                'kelas' => function($query){
                    $query->where('rombongan_belajar.semester_id', request()->semester_id);
                },
                'sekolah' => function($query){
                    $query->select('sekolah_id', 'nama');
                }
            ])
            ->withCount(['absen' => function($query){
                $query->has('absen_masuk');
            }, 'izin', 'sakit', 'cuti', 'alpa'])
            ->orderBy('sekolah_id', 'ASC')
            ->orderBy(request()->sortby, request()->sortbydesc)
            ->where(function($query) use ($user){
                if($user->sekolah_id){
                    $query->where('sekolah_id', $user->sekolah_id);
                }
            })
            ->when(request()->q, function($query) {
                $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                $query->orWhere('nisn', 'ILIKE', '%' . request()->q . '%');
            })->when(request()->sekolah_id, function($query) {
                $query->where('sekolah_id', request()->sekolah_id);
            })->paginate(request()->per_page);
        }
        return response()->json([
            'status' => 'success', 
            'data' => $data, 
            'data_sekolah' => $data_sekolah,
            'sekolah_id' => request()->sekolah_id,
            'tanggal_mulai' => $semester->tanggal_mulai,
            'tanggal_selesai' => Carbon::now()->format('Y-m-d'),
        ]);
    }
}
