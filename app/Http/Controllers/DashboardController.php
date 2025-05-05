<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Semester;
use App\Models\Team;
use App\Models\User;
use App\Models\Absen;
use App\Models\Izin;
use App\Models\Ptk;
use App\Models\Peserta_didik;
use Carbon\Carbon;
/*
use App\Models\Guru;
use App\Models\Peserta_didik;
use App\Models\Rombongan_belajar;
use App\Models\Jurusan_sp;
use App\Models\Pembelajaran;
use App\Models\Jam;
use App\Models\Whatsapp;
use App\Models\Anggota_rombel;
use File;*/

class DashboardController extends Controller
{
    private function loggedUser(){
        return auth()->user();
    }
    private function nama_hari(){
        return Carbon::createFromTimeStamp(strtotime(request()->tanggal))->translatedFormat('l');
        return Carbon::now()->translatedFormat('l');
    }
    private function tanggal(){
        return Carbon::createFromTimeStamp(strtotime(request()->tanggal))->format('Y-m-d');
        return Carbon::now()->format('Y-m-d');
    }
    public function generate_scan(){
        request()->validate(
            [
                'tanggal' => 'required|date_format:Y-m-d',
            ],
            [
                'tanggal.required' => 'Tanggal tidak boleh kosong!',
                'tanggal.date_format' => 'Format Tanggal salah!',
            ]
        );
        $data = Ptk::whereHas('jam', function($query){
            $query->where('semester_id', request()->semester_id);
            if($this->loggedUser()->sekolah_id){
                $query->where('sekolah_id', $this->loggedUser()->sekolah_id);
            }
            $query->whereHas('hari', function($query){
                $query->where('nama', $this->nama_hari());
            });
        })->get();
        $ada = 0;
        $baru = 0;
        $nama_ada = [];
        $nama_baru = [];
        foreach($data as $d){
            $absen = Absen::where(function($query) use ($d){
                $query->whereDate('created_at', $this->tanggal());
                $query->where('ptk_id', $d->ptk_id);
                $query->where('semester_id', request()->semester_id);
            })->first();
            if($absen){
                $nama_ada[] = $d;
                $ada++;
            } else {
                $nama_baru[] = $d;
                $baru++;
                Absen::create([
                    'ptk_id' => $d->ptk_id,
                    'semester_id' => request()->semester_id,
                    'created_at' => date('Y-m-d H:i:s', strtotime($this->tanggal())),
                    'updated_at' => date('Y-m-d H:i:s', strtotime($this->tanggal())),
                ]);
            }
        }
        $data = Peserta_didik::where(function($query){
            $query->whereHas('jam', function($query){
                $query->where('semester_id', request()->semester_id);
                if($this->loggedUser()->sekolah_id){
                    $query->where('sekolah_id', $this->loggedUser()->sekolah_id);
                }
                $query->whereHas('hari', function($query){
                    $query->where('nama', $this->nama_hari());
                });
            });
            $query->whereHas('kelas', function($query){
                $query->where('rombongan_belajar.semester_id', request()->semester_id);
            });
        })->orderBy('nama')->get();
        $ada_pd = 0;
        $baru_pd = 0;
        $nama_ada_pd = [];
        $nama_baru_pd = [];
        foreach($data as $d){
            $absen = Absen::where(function($query) use ($d){
                $query->whereDate('created_at', $this->tanggal());
                $query->where('peserta_didik_id', $d->peserta_didik_id);
                $query->where('semester_id', request()->semester_id);
            })->first();
            if($absen){
                $nama_ada_pd[] = $d;
                $ada_pd++;
            } else {
                $nama_baru_pd[] = $d;
                $baru_pd++;
                Absen::create([
                    'peserta_didik_id' => $d->peserta_didik_id,
                    'semester_id' => request()->semester_id,
                    'created_at' => date('Y-m-d H:i:s', strtotime($this->tanggal())),
                    'updated_at' => date('Y-m-d H:i:s', strtotime($this->tanggal())),
                ]);
            }
        }
        $data = [
            'icon' => 'success',
            'text' => 'Generate Scan berhasil',
            'title' => 'Berhasil',
            'ada' => $ada,
            'baru' => $baru,
            'nama_ada' => $nama_ada,
            'nama_baru' => $nama_baru,
            'ada_pd' => $ada_pd,
            'baru_pd' => $baru_pd,
            'nama_ada_pd' => $nama_ada_pd,
            'nama_baru_pd' => $nama_baru_pd,
            'nama_hari' => $this->nama_hari(),
            'tanggal' => $this->tanggal(),
        ];
        return response()->json($data);
    }
    public function pengaturan(){
        $semester = Semester::where('periode_aktif', 1)->first();
        $data = [
            'semester' => Semester::orderBy('semester_id', 'DESC')->get(),
            'semester_id' => $semester->semester_id,
            'tanggal_mulai' => Carbon::createFromTimeStamp(strtotime($semester->tanggal_mulai))->format('Y-m-d'),
            'tanggal_selesai' => Carbon::createFromTimeStamp(strtotime($semester->tanggal_selesai))->format('Y-m-d'),
        ];
        return response()->json($data);
    }
    public function save(){
        $user = auth()->user();
        request()->validate(
            [
                'semester_id' => 'required',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date'
            ],
            [
                'semester_id.required' => 'Semester Aktif tidak boleh kosong!!',
                'tanggal_mulai.required' => 'Periode Tanggal Mulai tidak boleh kosong!!',
                'tanggal_selesai.required' => 'Periode Tanggal Selesai tidak boleh kosong!!',
            ]
        );
        $semester = Semester::find(request()->semester_id);
        $team = Team::firstOrCreate([
            'name' => $semester->nama,
            'display_name' => $semester->nama,
            'description' => $semester->nama,
        ]);
        if(!$user->hasRole('administrator', $team)){
            $user->attachRole('administrator', $team);
        }
        $semester->tanggal_mulai = request()->tanggal_mulai;
        $semester->tanggal_selesai = request()->tanggal_selesai;
        $semester->periode_aktif = 1;
        $semester->save();
        $roles = ['administrator', 'ptk', 'staf', 'pd'];
        $get_users = User::whereRoleIs($roles, $semester->nama)->select('id')->get();
        foreach($get_users as $user){
            $user_id[] = $user->id;
        }
        $users = User::whereNotIn('id', $user_id)->get();
        foreach($users as $user){
            if($user->has('ptk')){
                if(!$user->hasRole('ptk', $team)){
                    $user->attachRole('ptk', $team);
                }
            }
            if($user->has('pd')){
                if(!$user->hasRole('pd', $team)){
                    $user->attachRole('pd', $team);
                }
            }
            if($user->has('staf')){
                if(!$user->hasRole('staf', $team)){
                    $user->attachRole('staf', $team);
                }
            }
        }
        Semester::where('semester_id', '<>', request()->semester_id)->update(['periode_aktif' => 0]);
        $data = [
            'icon' => 'success',
            'text' => 'Pengaturan berhasil disimpan.',
            'title' => 'Berhasil',
        ];
        return response()->json($data);
    }
    public function getSemester(){
        return Semester::find(request()->semester_id);
    }
    public function index(){
        $user = auth()->user();
        $start = $this->getSemester()->tanggal_mulai;
        $end = Carbon::now()->format('Y-m-d');
        if($user->hasRole('ptk', request()->periode_aktif)){
            $profile = Ptk::find($user->ptk_id);
            $data = [
                'profile' => $profile,
                'rekap' => [
                    'aktif' => jml_hari_aktif_ptk($user->sekolah_id, $user->ptk_id, $start, $end)['jml_hari_aktif'],
                    'libur' => jml_hari_aktif_ptk($user->sekolah_id, $user->ptk_id, $start, $end)['libur'],
                    'alpa' => tidak_hadir_ptk($user->ptk_id, $start, $end),
                    'sakit' => izin_ptk($profile, 'sakit', $start, $end),
                    'izin' => izin_ptk($profile, 'izin', $start, $end),
                    'cuti' => izin_ptk($profile, 'cuti', $start, $end),
                    'tidak_hadir' => tidak_hadir_ptk($user->ptk_id, $start, $end),
                    'hadir' => total_hadir_ptk($user->ptk_id, $start, $end),
                ]
            ];
        } else {
            $profile = Peserta_didik::find($user->peserta_didik_id);
            $data = [
                'profile' => $profile,
                'rekap' => [
                    'aktif' => jml_hari_aktif_pd($user->sekolah_id, $user->peserta_didik_id, $start, $end)['jml_hari_aktif'],
                    'libur' => jml_hari_aktif_pd($user->sekolah_id, $user->peserta_didik_id, $start, $end)['libur'],
                    'alpa' => tidak_hadir_pd($user->peserta_didik_id, $start, $end),
                    'sakit' => izin_pd($profile, 'sakit', $start, $end),
                    'izin' => izin_pd($profile, 'izin', $start, $end),
                    'cuti' => izin_pd($profile, 'cuti', $start, $end),
                    'tidak_hadir' => tidak_hadir_pd($user->peserta_didik_id, $start, $end),
                    'hadir' => total_hadir_pd($user->peserta_didik_id, $start, $end),
                ]
            ];
        }
        return response()->json($data);
    }
}
