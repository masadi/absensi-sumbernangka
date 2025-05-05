<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\Hari_ujian;
use App\Models\Kelompok;
use App\Models\Mata_pelajaran;
use App\Models\Jadwal;
use App\Models\Jadwal_ujian;
use App\Models\Catatan_ujian;
use App\Models\Rombongan_belajar;
use App\Models\Ptk;

use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    public function index(){
        $data = Jadwal::withWhereHas('rombongan_belajar', function($query){
            $query->where('semester_id', request()->semester_id);
            $query->select('rombongan_belajar_id', 'nama', 'ptk_id', 'sekolah_id');
            $query->when(request()->sekolah_id, function($query) {
                $query->where('sekolah_id', request()->sekolah_id);
            });
        })->with(['ptk' => function($query){
            $query->select('ptk_id', 'nama');
        }])->withCount('jadwal_ujian')->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => Sekolah::get()]);
    }
    public function mata_ujian(){
        $data = [
            'jadwal_ujian' => Jadwal_ujian::with(['hari', 'mata_pelajaran'])->where('jadwal_id', request()->jadwal_id)->orderBy('tanggal')->orderBy('jam_ke')->get(),
            'data_catatan' => Catatan_ujian::where('jadwal_id', request()->jadwal_id)->orderBy('id')->get(),
            'data_kelompok' => Kelompok::get(),
            //'rombel' => Rombongan_belajar::find(request()->rombongan_belajar_id),
        ];
        return response()->json($data);
    }
    public function edit(){
        $data = [
            'jadwal' => Jadwal::find(request()->jadwal_id),
            'data_ptk' => Ptk::where('sekolah_id', request()->sekolah_id)->orderBy('nama')->get(),
            'data_rombel' => Rombongan_belajar::where(function($query){
                $query->where('sekolah_id', request()->sekolah_id);
                $query->where('semester_id', request()->semester_id);
            })->orderBy('tingkat_pendidikan_id')->orderBy('nama')->get(),
        ];
        return response()->json($data);
    }
    public function simpan_jadwal(){
        request()->validate(
            [
                //'hari_id' => ['required','array'],
                'nama' => ['required'],
                'ptk_id' => ['required'],
                'rombongan_belajar_id' => ['required'],
                'tanggal' => ['required'],
            ],
            [
                //'hari_id.required' => 'Hari tidak boleh kosong!!',
                'nama.required' => 'Nama Ujian tidak boleh kosong!!',
                'ptk_id.required' => 'Ketua Pelaksana tidak boleh kosong!!',
                'rombongan_belajar_id.required' => 'Rombongan Belajar tidak boleh kosong!!',
                'tanggal.required' => 'Tanggal tidak boleh kosong!!',
            ]
        );
        if(request()->id){
            $find = Jadwal::find(request()->id);
            $find->nama = request()->nama;
            $find->ptk_id = request()->ptk_id;
            $find->rombongan_belajar_id = request()->rombongan_belajar_id;
            $find->tanggal = request()->tanggal;
            if($find->save()){
                $data = [
                    'icon' => 'success',
                    'text' => 'Jadwal Ujian berhasil diperbaharui',
                    'title' => 'Berhasil',
                ];            
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Jadwal Ujian gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } elseif(request()->aksi == 'salin'){
            $insert = 0;
            $jadwal = Jadwal::with(['jadwal_ujian', 'catatan_ujian'])->find(request()->jadwal_id);
            $new = Jadwal::create([
                'nama' => request()->nama,
                'ptk_id' => request()->ptk_id,
                'rombongan_belajar_id' => request()->rombongan_belajar_id,
                'tanggal' => request()->tanggal,
            ]);
            foreach($jadwal->jadwal_ujian as $jadwal_ujian){
                Jadwal_ujian::create([
                    'jadwal_id' => $new->id,
                    'jenis' => '-',
                    'rombongan_belajar_id' => request()->rombongan_belajar_id,
                    'mata_pelajaran_id' => $jadwal_ujian->mata_pelajaran_id,
                    'hari_id' => $jadwal_ujian->hari_id,
                    'tanggal' => $jadwal_ujian->tanggal,
                    'jam_ke' => $jadwal_ujian->jam_ke,
                ]);
                $insert++;
            }
            foreach($jadwal->catatan_ujian as $catatan_ujian){
                Catatan_ujian::create([
                    'jenis' => '-',
                    'rombongan_belajar_id' => request()->rombongan_belajar_id,
                    'catatan' => $catatan_ujian->catatan,
                    'jadwal_id' => $new->id,
                ]);
            }
            if($insert){
                $data = [
                    'icon' => 'success',
                    'text' => 'Jadwal Ujian berhasil disalin',
                    'title' => 'Berhasil',
                ];            
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Jadwal Ujian gagal disalin. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $insert = Jadwal::create([
                'nama' => request()->nama,
                'ptk_id' => request()->ptk_id,
                'rombongan_belajar_id' => request()->rombongan_belajar_id,
                'tanggal' => request()->tanggal,
            ]);
            if($insert){
                $data = [
                    'icon' => 'success',
                    'text' => 'Jadwal Ujian berhasil disimpan',
                    'title' => 'Berhasil',
                ];            
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Jadwal Ujian gagal disimpan. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        }
        return response()->json($data);
    }
    public function simpan(){
        $insert = NULL;
        if(request()->aksi == 'catatan'){
            request()->validate([
                'catatan' => 'required',
            ],
            [
                'catatan.required' => 'Isi Catatan tidak boleh kosong!!',
            ]);
            if(request()->id){
                $find = Catatan_ujian::find(request()->id);
                $find->catatan = request()->catatan;
                $insert = $find->save();
            } else {
                $jadwal = Jadwal::find(request()->jadwal_id);
                $insert = Catatan_ujian::create([
                    'jenis' => '-',
                    'rombongan_belajar_id' => $jadwal->rombongan_belajar_id,
                    'catatan' => request()->catatan,
                    'jadwal_id' => request()->jadwal_id,
                ]);    
            }
            if($insert){
                $data = [
                    'icon' => 'success',
                    'text' => 'Catatan '.strtoupper(request()->aksi).' berhasil disimpan',
                    'title' => 'Berhasil',
                ];            
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Catatan '.strtoupper(request()->aksi).' gagal disimpan. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            request()->validate(
                [
                    'tanggal' => ['required','array'],
                    'jam_ke' => ['required','array'],
                    'kelompok_id' => ['required','array'],
                    'mata_pelajaran_id' => ['required','array'],
                ],
                [
                    'tanggal.required' => 'Hari tidak boleh kosong!!',
                    'jam_ke.required' => 'Jam Ke- tidak boleh kosong!!',
                    'kelompok_id.required' => 'Kelompok tidak boleh kosong!!',
                    'mata_pelajaran_id.required' => 'Mata Pelajaran tidak boleh kosong!!',
                ]
            );
            $jadwal = Jadwal::find(request()->id);
            foreach(request()->tanggal as $urut => $tanggal){
                $insert = Jadwal_ujian::create([
                    'jadwal_id' => request()->id,
                    'jenis' => '-',
                    'rombongan_belajar_id' => $jadwal->rombongan_belajar_id,
                    'mata_pelajaran_id' => request()->mata_pelajaran_id[$urut],
                    'hari_id' => 1,//request()->hari_id[$urut],
                    'tanggal' => request()->tanggal[$urut],
                    'jam_ke' => request()->jam_ke[$urut],
                ]);
            }
            if($insert){
                $data = [
                    'icon' => 'success',
                    'text' => 'Mata Ujian berhasil disimpan',
                    'title' => 'Berhasil',
                ];            
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Mata Ujian gagal disimpan. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        }
        return response()->json($data);
    }
    public function get_data(){
        $data = [
            'data_rombel' => Rombongan_belajar::where(function($query){
                $query->where('semester_id', request()->semester_id);
                $query->where('sekolah_id', request()->sekolah_id);
            })->orderBy('tingkat_pendidikan_id')->orderBy('nama')->get(),
            'data_ptk' => Ptk::where('sekolah_id', request()->sekolah_id)->orderBy('nama')->get(),
        ];
        return response()->json($data);
    }
    public function get_mapel(){
        $data = Mata_pelajaran::where('kelompok_id', request()->kelompok_id)->get();
        return response()->json($data);
    }
    public function get_sekolah(){
        $data = Sekolah::get();
        return response()->json($data);
    }
    public function hapus(){
        $delete = FALSE;
        if(request()->aksi == 'ujian'){
            $find = Jadwal_ujian::find(request()->id);
            if($find && $find->delete()){
                $delete = TRUE;
            }
            $text = 'Mata Ujian';
        } elseif(request()->aksi == 'jadwal'){
            $find = Jadwal::find(request()->id);
            if($find && $find->delete()){
                $delete = TRUE;
            }
            $text = 'Jadwal Ujian';
        } else {
            $find = Catatan_ujian::find(request()->id);
            if($find && $find->delete()){
                $delete = TRUE;
            }
            $text = 'Catatan Ujian';
        }
        if($delete){
            $data = [
                'icon' => 'success',
                'text' => $text.' berhasil dihapus!',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => $text.' gagal dihapus. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function salin(){
        /*
        $data = [
            'jadwal_ujian' => Jadwal_ujian::with(['hari', 'mata_pelajaran'])->where('rombongan_belajar_id', request()->rombongan_belajar_id)->where('jenis', request()->jenis_jadwal)->orderBy('hari_id')->orderBy('jam_ke')->get(),
            'data_catatan' => Catatan_ujian::where('rombongan_belajar_id', request()->rombongan_belajar_id)->where('jenis', request()->jenis_jadwal)->orderBy('id')->get(),
        ];
        */
        $rombel_sekarang = Rombongan_belajar::find(request()->rombongan_belajar_id);
        $rombel_jadwal = Rombongan_belajar::withWhereHas('jadwal_ujian', function($query){
                $query->where('jenis', request()->jenis_jadwal);
        })->with(['catatan_ujian' => function($query){
            $query->where('jenis', request()->jenis_jadwal);
        }])
        ->where(function($query) use ($rombel_sekarang){
            $query->where('rombongan_belajar_id', '<>', request()->rombongan_belajar_id);
            $query->where('semester_id', $rombel_sekarang->semester_id);
            $query->where('tingkat_pendidikan_id', $rombel_sekarang->tingkat_pendidikan_id);
            //$query->where('nama_jurusan', $rombel_sekarang->nama_jurusan);
        })->first();
        $insert = 0;
        if($rombel_jadwal){
            foreach($rombel_jadwal->jadwal_ujian as $jadwal_ujian){
                $insert++;
                Jadwal_ujian::updateOrCreate([
                    'jenis' => $jadwal_ujian->jenis,
                    'rombongan_belajar_id' => request()->rombongan_belajar_id,
                    'mata_pelajaran_id' => $jadwal_ujian->mata_pelajaran_id,
                    'hari_id' => $jadwal_ujian->hari_id,
                    'jam_ke' => $jadwal_ujian->jam_ke,
                ]);

            }
            foreach($rombel_jadwal->catatan_ujian as $catatan_ujian){
                Catatan_ujian::updateOrCreate([
                    'jenis' => $catatan_ujian->jenis,
                    'rombongan_belajar_id' => request()->rombongan_belajar_id,
                    'catatan' => $catatan_ujian->catatan,
                ]);
            }
        }
        if($rombel_jadwal){
            if($insert){
                $data = [
                    'icon' => 'success',
                    'text' => 'Jadwal Ujian berhasil disalin!',
                    'title' => 'Berhasil',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'text' => 'Jadwal Ujian gagal disalin. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Kelas tingkat '.$rombel_sekarang->tingkat_pendidikan_id.' jurusan '.$rombel_sekarang->nama_jurusan.' belum ada yang memiliki jadwal ujian!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function update(){
        $find = Jadwal_ujian::find(request()->id);
        $find->tanggal = request()->tanggal;
        if($find->save()){
            $data = [
                'variant' => 'success',
                'text' => 'Jadwal Ujian berhasil diperbaharui!',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'variant' => 'success',
                'text' => 'Jadwal Ujian gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
