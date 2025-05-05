<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Sekolah;
use App\Models\Ptk;
use App\Models\Peserta_didik;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Models\Semester;
use App\Models\Rombongan_belajar;
use App\Models\Anggota_rombel;

class UnitController extends Controller
{
    public function index(){
        $data = Sekolah::withCount(['ptk', 'pd' => function($query){
            $query->whereHas('anggota_rombel', function($query){
                $query->where('semester_id', request()->semester_id);
            });
        }])->with(['pengguna' => function($query){
            $query->whereRoleIs(['bk'], request()->periode_aktif);
        }])->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            $query->orWhere('npsn', 'ILIKE', '%' . request()->q . '%');
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function toastr($icon, $title, $text){
        $data = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text,
        ];
        return $data;
    }
    public function sync_data(){
        $data = $this->toastr('danger', 'Sinkronisasi Rombel gagal', 'Pastikan Dapodik Anda sudah di sinkronisasi!');
        $sekolah = Sekolah::find(request()->sekolah_id);
        $semester = Semester::find(request()->semester_id);
        $team = Team::where('name', $semester->nama)->first();
        if(request()->data == 'ptk'){
            return $this->ambil_ptk($sekolah, $semester, $team);
        }
        if(request()->data == 'rombel'){
            return $this->ambil_rombel($sekolah, $semester, $team);
        }
        if(request()->data == 'pd'){
            return $this->ambil_pd($sekolah, $semester, $team);
        }
    }
    private function generateEmail(){
        $random = Str::random(6);
        return strtolower($random).'@ariyametta.sch.id';
    }
    public function ambil_ptk($sekolah, $semester, $team){
        $data = $this->toastr('error', 'Sinkronisasi PTK gagal', 'Pastikan Dapodik Anda sudah di sinkronisasi!');
        $response = Http::withHeaders([
            'x-api-key' => $sekolah->sekolah_id,
        ])->post('http://sync.erapor-smk.net/api/v7/dapodik/ptk', [
            'username_dapo'		=> $sekolah->email,
            'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
            'semester_id'		=> $semester->semester_id,
            'sekolah_id'		=> $sekolah->sekolah_id,
            'npsn'				=> $sekolah->npsn,
        ]);
        $insert = 0;
        $error = NULL;
        if($response->status() == 200){
            $all_data = $response->object();
            $role = Role::where('name', 'ptk')->first();
            if($all_data->dapodik){
                foreach($all_data->dapodik as $dapodik){
                    $email = ($dapodik->email) ?? $this->generateEmail();
                    $ptk = Ptk::updateOrCreate(
                        ['ptk_id' => strtolower($dapodik->ptk_id)],
                        [
                        'sekolah_id' => $sekolah->sekolah_id,
                        'nama' => strtoupper($dapodik->nama),
                        'nuptk' => ($dapodik->nuptk) ? $dapodik->nuptk : 123,
                        'jenis_kelamin' => $dapodik->jenis_kelamin,
                        'tempat_lahir' => $dapodik->tempat_lahir,
                        'tanggal_lahir' => $dapodik->tanggal_lahir,
                        'jenis_ptk_id' => $dapodik->ptk_terdaftar->jenis_ptk_id,
                        'agama_id' => $dapodik->agama_id,
                        'status_kepegawaian_id' => $dapodik->status_kepegawaian_id,
                        'email' => $email,
                        ]
                    );
                    $user = User::where('email', $email)->first();
                    if($user){
                        $user->ptk_id = strtolower($dapodik->ptk_id);
                        $user->save();
                    } else {
                        $user = User::updateOrCreate(
                            [
                                'ptk_id' => strtolower($dapodik->ptk_id)
                            ],
                            [
                                'email' => $email,
                                'sekolah_id' => $sekolah->sekolah_id,
                                'name' => strtoupper($dapodik->nama),
                                'password' => bcrypt('12345678'),
                            ]
                        );
                    }
                    if(!$user->hasRole($role, $team)){
                        $user->attachRole($role, $team);
                    }
                    $insert++;
                }
            }
        } else {
            $response = $response->object();
            $error = ($response) ? $response->message : 'Silahkan coba beberapa saat lagi';
        }
        if($error){
            $this->toastr('error', 'Gagal', 'Sinkronisasi PTK gagal. '.$response);
        }
        if($insert){
            $data = $this->toastr('success', 'Sukses', $insert.' PTK berhasil disimpan');
        }
        return response()->json($data);
    }
    public function ambil_rombel($sekolah, $semester, $team){
        $data = $this->toastr('error', 'Sinkronisasi Rombel gagal', 'Pastikan Dapodik Anda sudah di sinkronisasi!');
        $insert = 0;
        $error = NULL;
        $response = Http::withHeaders([
            'x-api-key' => $sekolah->sekolah_id,
        ])->post('http://sync.erapor-smk.net/api/v7/dapodik/rombongan_belajar', [
            'username_dapo'		=> $sekolah->email,
            'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
            'semester_id'		=> $semester->semester_id,
            'sekolah_id'		=> $sekolah->sekolah_id,
            'npsn'				=> $sekolah->npsn,
        ]);
        if($response->status() == 200){
            $all_data = $response->object();
            if($all_data->dapodik){
                foreach($all_data->dapodik as $dapodik){
                    Rombongan_belajar::updateOrCreate(
                        [
                            'rombongan_belajar_id' => $dapodik->rombongan_belajar_id,
                        ],
                        [
                            'nama' => $dapodik->nama,
                            'semester_id' => $dapodik->semester_id,
                            'sekolah_id' => $dapodik->sekolah_id,
                            'tingkat_pendidikan_id' => $dapodik->tingkat_pendidikan_id,
                            'ptk_id' => $dapodik->ptk_id,
                            'nama_jurusan' => ($dapodik->jurusan_sp) ? $dapodik->jurusan_sp->nama_jurusan_sp : NULL,
                        ]
                    );
                    $insert++;
                }
            }
        } else {
            $response = $response->object();
            $error = ($response) ? $response->message : 'Silahkan coba beberapa saat lagi';
        }
        if($error){
            $data = $this->toastr('danger', 'Gagal', 'Sinkronisasi PTK gagal. '.$response);
        }
        if($insert){
            $data = $this->toastr('success', 'Sukses', $insert.' Rombongan Belajar berhasil disimpan');
        }
        return response()->json($data);
    }
    public function ambil_pd($sekolah, $semester, $team){
        $data = $this->toastr('error', 'Sinkronisasi PD gagal', 'Pastikan Dapodik Anda sudah di sinkronisasi!');
        $insert = 0;
        $error = NULL;
        $response = Http::withHeaders([
            'x-api-key' => $sekolah->sekolah_id,
        ])->post('http://sync.erapor-smk.net/api/v7/dapodik/peserta_didik_aktif', [
            'username_dapo'		=> $sekolah->email,
            'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
            'semester_id'		=> $semester->semester_id,
            'sekolah_id'		=> $sekolah->sekolah_id,
            'npsn'				=> $sekolah->npsn,
        ]);
        if($response->successful()){
            $all_data = $response->object();
            $role = Role::where('name', 'pd')->first();
            if($all_data->dapodik){
                foreach($all_data->dapodik->data as $dapodik){
                    if($dapodik->email && $dapodik->email != ''){
                        $email = $dapodik->email;
                    } else {
                        $email = $this->generateEmail();
                    }
                    $peserta_didik = Peserta_didik::updateOrCreate(
                        ['peserta_didik_id' => strtolower($dapodik->peserta_didik_id)],
                        [
                        'sekolah_id' => $sekolah->sekolah_id,
                        'nama' => strtoupper($dapodik->nama),
                        'no_induk' => ($dapodik->registrasi_peserta_didik->nipd) ? $dapodik->registrasi_peserta_didik->nipd : 123,
                        'nisn' => $dapodik->nisn,
                        'nik' => $dapodik->nik,
                        'jenis_kelamin' => $dapodik->jenis_kelamin,
                        'tempat_lahir' => $dapodik->tempat_lahir,
                        'tanggal_lahir' => $dapodik->tanggal_lahir,
                        'agama_id' => $dapodik->agama_id,
                        'alamat' => $dapodik->alamat_jalan,
                        'desa_kelurahan' => $dapodik->desa_kelurahan,
                        'kecamatan' => $dapodik->wilayah->parrent_recursive->nama,
                        'kode_wilayah' => $dapodik->kode_wilayah,
                        'nama_ayah' => $dapodik->nama_ayah,
                        'nama_ibu' => $dapodik->nama_ibu_kandung,
                        'email' => $email,
                        ]
                    );
                    $user = User::updateOrCreate(
                        [
                            'peserta_didik_id' => strtolower($dapodik->peserta_didik_id)
                        ],
                        [
                            'email' => $email,
                            'sekolah_id' => $sekolah->sekolah_id,
                            'name' => strtoupper($dapodik->nama),
                            'password' => bcrypt('12345678'),
                        ]
                    );
                    if(!$user->hasRole($role, $team)){
                        $user->attachRole($role, $team);
                    }
                    
                    /*Rombongan_belajar::updateOrCreate(
                        [
                            'rombongan_belajar_id' => $dapodik->rombongan_belajar_id,
                        ],
                        [
                            'nama' => $dapodik->nama_rombongan_belajar,
                            'semester_id' => request()->semester_id,
                            'sekolah_id' => $this->sekolah_id,
                            'tingkat_pendidikan_id' => $dapodik->tingkat_pendidikan_id,
                            'ptk_id' => $dapodik->ptk_id,
                        ]
                    );*/
                    Anggota_rombel::updateOrCreate(
                        [
                            'anggota_rombel_id' => $dapodik->anggota_rombel->anggota_rombel_id,
                        ],
                        [
                            'rombongan_belajar_id' => $dapodik->anggota_rombel->rombongan_belajar_id,
                            'peserta_didik_id' => $dapodik->peserta_didik_id,
                            'semester_id' => $semester->semester_id,
                        ]
                    );
                    $insert++;
                }
            }
        }
        if($error){
            $data = $this->toastr('error', 'Gagal', 'Sinkronisasi PD gagal. Silahkan coba beberapa saat lagi');
        }
        if($insert){
            $data = $this->toastr('success', 'Sukses', $insert.' Peserta Didik berhasil disimpan');
        }
        return response()->json($data);
    }
    public function update_unit(){
        request()->validate(
            [
                'sekolah_id' => 'required',
                'nama' => 'required',
                'alamat' => 'required',
                'photo' => 'nullable|image',
            ],
            [
                'sekolah_id.required' => 'ID Unit tidak boleh kosong!',
                'nama.required' => 'Nama Unit tidak boleh kosong!',
                'alamat.required' => 'Alamat Unit tidak boleh kosong!',
                'photo.image' => 'Logo Unit harus berekstensi jpg, jpeg, png, bmp, gif, svg, atau webp!',
            ]
        );
        $sekolah = Sekolah::find(request()->sekolah_id);
        $sekolah->nama = request()->nama;
        $sekolah->alamat = request()->alamat;
        $photo = request()->photo->store('public/images');
        $sekolah->logo_sekolah = basename($photo);
        if($sekolah->save()){
            $data = $this->toastr('success', 'Sukses', 'Data Unit Berhasil diperbaharui');
        } else {
            $data = $this->toastr('danger', 'Gagal', 'Data Unit gagal diperbaharui. Silahkan coba beberapa saat lagi!');
        }
        return response()->json($data);
    }
    public function update_kasek(){
        $sekolah = Sekolah::find(request()->sekolah_id);
        $sekolah->ptk_id = request()->ptk_id;
        if($sekolah->save()){
            $data = $this->toastr('success', 'Sukses', 'Data Kepala Unit Berhasil diperbaharui');
        } else {
            $data = $this->toastr('danger', 'Gagal', 'Data Kepala Unit gagal diperbaharui. Silahkan coba beberapa saat lagi!');
        }
        return response()->json($data);
    }
    public function admin(){
        $data = User::with(['sekolah' => function($query){
            $query->select('sekolah_id', 'nama');
        }])->whereRoleIs(['unit'], request()->periode_aktif)
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('name', 'ILIKE', '%' . request()->q . '%')
            ->orWhere('email', 'ILIKE', '%' . request()->q . '%');
        })->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })->paginate(request()->per_page);
        return response()->json([
            'status' => 'success', 
            'data' => $data,
            'data_sekolah' => Sekolah::select('sekolah_id', 'nama')->get(),
        ]);
    }
    public function post_admin(){
        if(request()->aksi == 'hapus'){
            $find = User::find(request()->id);
            if($find->delete()){
                $data = $this->toastr('success', 'Berhasil', 'Admin Unit berhasil dihapus!');
            } else {
                $data = $this->toastr('error', 'Gagal', 'Admin Unit gagal dihapus. Silahkan coba beberapa saat lagi!');
            }
        }
        if(request()->aksi == 'baru'){
            request()->validate(
                [
                    'sekolah_id' => 'required',
                    'ptk_id' => 'required',
                    'email' => 'required|email',
                    //'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ],
                [
                    'sekolah_id.required' => 'Unit tidak boleh kosong!!',
                    'ptk_id.required' => 'Nama Pengguna tidak boleh kosong!!',
                    'email.required' => 'Email tidak boleh kosong!!',
                    'email.email' => 'Email tidak valid!!',
                    //'email.unique' => 'Email terdeteksi existing!!',
                    'password.required' => 'Password tidak boleh kosong!!',
                ]
            );
            $role = Role::where('name', 'unit')->first();
            $team = Team::where('name', request()->periode_aktif)->first();
            $ptk = Ptk::find(request()->ptk_id);
            $user = User::firstOrCreate(
                [
                    'email' => request()->email,
                ],
                [
                    'name' => $ptk->nama,
                    'sekolah_id' => request()->sekolah_id,
                    'password' => bcrypt(request()->password),
                    'ptk_id' => request()->ptk_id,
                ]
            );
            if(!$user->hasRole($role, $team)){
                $user->attachRole($role, $team);
            }
            $data = $this->toastr('success', 'Berhasil', 'Admin Unit berhasil disimpan');
        }
        return response()->json($data);
    }
    public function bp(){
        $data = User::with(['sekolah' => function($query){
            $query->select('sekolah_id', 'nama');
        }])->whereRoleIs(['bk'], request()->periode_aktif)
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('name', 'ILIKE', '%' . request()->q . '%')
            ->orWhere('email', 'ILIKE', '%' . request()->q . '%');
        })->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })->paginate(request()->per_page);
        return response()->json([
            'status' => 'success', 
            'data' => $data,
            'data_sekolah' => Sekolah::select('sekolah_id', 'nama')->get(),
        ]);
    }
    public function post_bp(){
        if(request()->aksi == 'hapus'){
            $find = User::find(request()->id);
            if($find->delete()){
                $data = $this->toastr('success', 'Berhasil', 'Guru BP/BK berhasil dihapus!');
            } else {
                $data = $this->toastr('error', 'Gagal', 'Guru BP/BK gagal dihapus. Silahkan coba beberapa saat lagi!');
            }
        }
        if(request()->aksi == 'baru'){
            request()->validate(
                [
                    'sekolah_id' => 'required',
                    'ptk_id' => 'required',
                    'email' => 'required|email',
                    //'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ],
                [
                    'sekolah_id.required' => 'Unit tidak boleh kosong!!',
                    'ptk_id.required' => 'Nama Pengguna tidak boleh kosong!!',
                    'email.required' => 'Email tidak boleh kosong!!',
                    'email.email' => 'Email tidak valid!!',
                    //'email.unique' => 'Email terdeteksi existing!!',
                    'password.required' => 'Password tidak boleh kosong!!',
                ]
            );
            $role = Role::where('name', 'bk')->first();
            $team = Team::where('name', request()->periode_aktif)->first();
            $user = User::firstOrCreate(
                [
                    'email' => request()->email,
                ],
                [
                    'name' => request()->nama,
                    'sekolah_id' => request()->sekolah_id,
                    'password' => bcrypt(request()->password),
                    'ptk_id' => request()->ptk_id,
                ]
            );
            if(!$user->hasRole($role, $team)){
                $user->attachRole($role, $team);
            }
            $data = $this->toastr('success', 'Berhasil', 'Guru BP/BK berhasil disimpan');
        }
        return response()->json($data);
    }
    public function sekolah(){
        $data = Sekolah::where('npsn', '<>', '-')->orderBy('bentuk_pendidikan_id')->get();
        return response()->json($data);
    }
}
