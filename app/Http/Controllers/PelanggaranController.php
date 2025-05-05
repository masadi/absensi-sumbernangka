<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggaran;

class PelanggaranController extends Controller
{
    public function index(){
        $data = Pelanggaran::withWhereHas('pd', function($query){
            $query->where('sekolah_id', request()->sekolah_id);
            $query->where('semester_id', request()->semester_id);
        })->with('kelas')
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query){
            $query->whereHas('pd', function($query){
                $query->where('sekolah_id', request()->sekolah_id);
                    $query->where('nama', 'ILIKE', '%' . request()->q . '%');
                    $query->orWhere('nisn', 'ILIKE', '%' . request()->q . '%');
                $query->where('semester_id', request()->semester_id);
            });
        })->when(request()->end, function($query){
            $query->whereDate('tanggal', '>=', request()->start);
            $query->whereDate('tanggal', '<=', request()->end);
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function store(){
        request()->validate(
            [
                'rombongan_belajar_id' => 'required',
                'anggota_rombel_id' => 'required',
                'tanggal' => 'required|date_format:Y-m-d',
                'waktu' => 'required|date_format:H:i:s',
                'user_id' => 'required',
                'masalah' => 'required',
                'tindak_lanjut' => 'required',
            ],
            [
                'rombongan_belajar_id.required' => 'Peserta Didik tidak boleh kosong!',
                'anggota_rombel_id.required' => 'Peserta Didik tidak boleh kosong!!',
                'tanggal.required' => 'Tanggal tidak boleh kosong!',
                'waktu.required' => 'Waktu tidak boleh kosong!',
                'tanggal.date_format' => 'Format Tanggal salah!',
                'waktu.date_format' => 'Format Waktu salah!',
                'user_id.required' => 'Petugas tidak boleh kosong!',
                'masalah.required' => 'Masalah tidak boleh kosong!',
                'tindak_lanjut.required' => 'Tindak Lanjut tidak boleh kosong!',
            ]
        );
        Pelanggaran::create([
            'anggota_rombel_id' => request()->anggota_rombel_id,
            'tanggal' => request()->tanggal,
            'waktu' => request()->waktu,
            'user_id' => request()->user_id,
            'masalah' => request()->masalah,
            'tindak_lanjut' => request()->tindak_lanjut,
            'catatan' => request()->catatan,
        ]);
        $data = [
            'icon' => 'success',
            'text' => 'Data Pelanggaran berhasil disimpan',
            'title' => 'Berhasil',
        ];
        return response()->json($data);
    }
}
