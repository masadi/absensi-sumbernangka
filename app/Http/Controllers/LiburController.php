<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori_libur;
use App\Models\Sekolah;
use App\Models\Libur;
use App\Models\Libur_ptk;
use Carbon\Carbon;

class LiburController extends Controller
{
    public function index(){
        $start = Carbon::createFromTimeStamp(strtotime(request()->start))->format('Y-m-d');
        $end = Carbon::createFromTimeStamp(strtotime(request()->end))->format('Y-m-d');
        $bulan = Carbon::createFromTimeStamp(strtotime(request()->start))->format('m');
        /*
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
        */
        $data = [
            'kategori_libur' => Kategori_libur::orderBy('id')->get(),
            'data_sekolah' => Sekolah::select('sekolah_id', 'nama')->get(),
            'semester_id' => semester_id(),
            //'hari_libur' => Libur::where('mulai_tanggal', '>=', $start)->where('sampai_tanggal', '<=', $end)->get(),
            'hari_libur' => Libur::whereMonth('mulai_tanggal', $bulan)->orWhereMonth('sampai_tanggal', $bulan)->get(),
            'start' => $start,
            'end' => $end,
            'bulan' => $bulan,
        ];
        return response()->json($data);
    }
    public function simpan(){
        if(request()->aksi == 'libur'){
            request()->validate(
                [
                    'nama' => 'required',
                    'kategori_id' => 'required',
                    'mulai_tanggal' => 'required',
                    'sampai_tanggal' => 'required',
                ],
                [
                    'nama.required' => 'Nama tidak boleh kosong!',
                    'kategori_id.required' => 'Kategori tidak boleh kosong!',
                    'mulai_tanggal.required' => 'Tanggal Mulai tidak boleh kosong!',
                    'sampai_tanggal.required' => 'Tanggal Selesai tidak boleh kosong!',
                ]
            );
            if(request()->id){
                $find = Libur::find(request()->id);
                $find->kategori_id = request()->kategori_id;
                $find->title =  request()->nama;
                $find->mulai_tanggal =  request()->mulai_tanggal;
                $find->sampai_tanggal =  request()->sampai_tanggal;
                $find->save();
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Libur berhasil diperbaharui',
                ];
            } else {
                Libur::create([
                    'kategori_id' => request()->kategori_id,
                    'title' => request()->nama,
                    'mulai_tanggal' => request()->mulai_tanggal,
                    'sampai_tanggal' => request()->sampai_tanggal,
                ]);
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Libur berhasil disimpan',
                ];
            }
        } else {
            request()->validate(
                [
                    'nama' => 'required',
                ],
                [
                    'nama.required' => 'Nama tidak boleh kosong!',
                ]
            );
            if(request()->id){
                $find = Kategori_libur::find(request()->id);
                $find->sekolah_id = (request()->sekolah_id) ? request()->sekolah_id : NULL;
                $find->nama =  request()->nama;
                $find->save();
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Kategori Libur berhasil diperbaharui',
                ];
            } else {
                Kategori_libur::create([
                    'tahun_ajaran_id' => substr(semester_id(),0,4),
                    'sekolah_id' => (request()->sekolah_id) ? request()->sekolah_id : NULL,
                    'nama' => request()->nama,
                ]);
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Kategori Libur berhasil disimpan',
                ];
            }
        }
        return response()->json($data);
    }
    public function hapus(){
        if(request()->aksi == 'libur'){
            $find = Libur::find(request()->id);
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Libur berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'danger',
                    'title' => 'Gagal',
                    'text' => 'Data Libur gagal dihapus',
                ];
            }
        } else {
            $find = Kategori_libur::find(request()->id);
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Kategori Libur berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'danger',
                    'title' => 'Gagal',
                    'text' => 'Kategori Libur gagal dihapus',
                ];
            }
        }
        return response()->json($data);
    }
}
