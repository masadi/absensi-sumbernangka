<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Ptk;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;

class PtkController extends Controller
{
    public function index(){
        $data = Ptk::with(['sekolah' => function($query){
            $query->select('sekolah_id', 'nama');
        }])
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            $query->orWhere('nuptk', 'ILIKE', '%' . request()->q . '%');
            $query->orWhere('email', 'ILIKE', '%' . request()->q . '%');
        })
        ->paginate(request()->per_page);
        $data_sekolah = [];
        if(auth()->user()->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = Sekolah::select('sekolah_id', 'nama')->get();
        }
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => $data_sekolah]);
    }
    public function get_ptk(){
        $data = [
            'ptk' => Ptk::where('sekolah_id', request()->sekolah_id)->orderBy('nama')->get(),
        ];
        return response()->json($data);
    }
    public function update_bp(){
        $insert = 0;
        $role = Role::where('name', 'bk')->first();
        $team = Team::where('name', request()->periode_aktif)->first();
        $data_ptk = Ptk::withWhereHas('user', function($query){
            $query->where('sekolah_id', request()->sekolah_id);
            $query->whereRoleIs(['bk'], request()->periode_aktif);
        })->whereNotIn('ptk_id', request()->ptk_id)->get();
        if($data_ptk->count()){
            foreach($data_ptk as $ptk){
                $ptk->user->detachRole($role, $team);
            }
        }
        foreach(request()->ptk_id as $ptk_id){
            $user = User::where('ptk_id', $ptk_id)->first();
            if(!$user->hasRole($role, request()->periode_aktif)){
                if($user->attachRole($role, request()->periode_aktif)){
                    $insert++;
                }
            }
            $user->sekolah_id = request()->sekolah_id;
            $user->save();
        }
        $data = [
            'icon' => ($insert) ? 'success' : 'danger',
            'title' => ($insert) ? 'Berhasil' : 'Gagal',
            'text' => ($insert) ? 'Guru BP/BK berhasil diperbaharui' : 'Guru BP/BK gagal diperbaharui. Silahkan coba beberapa saat lagi!',
            'insert' => $insert,
            'request' => request()->all(),
            'ptk_id' => request()->ptk_id,
            'team' => $team,
        ];
        return response()->json($data);
    }
    public function upload_ptk(){
        request()->validate(
            [
                'sekolah_id' => 'required',
                'file' => 'required|mimes:xlsx',
            ],
            [
                'file.mimes' => 'File harus berekstensi .XLSX',
                'file.required' => 'File tidak boleh kosong!!',
                'sekolah_id.required' => 'Unit tidak boleh kosong!!',
            ]
        );
        $file_excel = request()->file->store('public/file-excel');
        //request()->file_to_delete = Str::of($file_excel)->basename();
        $arrays = (new FastExcel)->import(storage_path('app/'.$file_excel));
        $result = [];
        foreach($arrays as $urut => $value){
            if($value && count($value) == 8){
                $result[$urut] = [
                    'no' => $value['no'],
                    'ptk_id' => $value['ptk_id'],
                    'nama' => $value['nama'],
                    'nuptk' => $value['nuptk'],
                    'jenis_kelamin' => $value['jenis_kelamin'],
                    'tempat_lahir' => $value['tempat_lahir'],
                    'tanggal_lahir' => (is_object($value['tanggal_lahir'])) ? $value['tanggal_lahir']->format('Y-m-d') : now()->format('Y-m-d'),
                    'email' => $value['email'],
                ];
            }
        }
        Storage::disk('public')->delete('file-excel/'.Str::of($file_excel)->basename());
        $data['sekolah_id'] = request()->sekolah_id;
        $data['guru'] = $result;
        return response()->json($data);
    }
    public function update_ptk(){
        request()->validate(
            [
                'nama' => 'required',
                'photo' => 'nullable|image|max:1024', // 1MB Max
                'sekolah_id' => 'required',
                'jenis_kelamin' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'email' => 'email|required',
            ],
            [
                'edit_nama.required' => 'Nama tidak boleh kosong!',
                'photo.image' => 'File foto harus berupa gambar',
                'photo.max' => 'File foto maksimal 1MB',
                'sekolah_id.required' => 'Unit tidak boleh kosong!',
                'jenis_kelamin.required' => 'Jenis Kelamin tidak boleh kosong!',
                'tempat_lahir.required' => 'Tempat Lahir tidak boleh kosong!',
                'tanggal_lahir.required' => 'Tanggal Lahir tidak boleh kosong!',
                'email.required' => 'Email tidak boleh kosong!',
                'email.email' => 'Email tidak valid',
            ]
        );
        $photo = NULL;
        if(request()->photo){
            $photo = request()->photo->store('public/profile-photos');
        }
        $ptk = Ptk::find(request()->ptk_id);
        $ptk->sekolah_id = request()->sekolah_id;
        $ptk->nama = request()->nama;
        $ptk->nuptk = request()->nuptk;
        $ptk->jenis_kelamin = request()->jenis_kelamin;
        $ptk->tempat_lahir = request()->tempat_lahir;
        $ptk->tanggal_lahir = request()->tanggal_lahir;
        $ptk->email = strtolower(request()->email);
        if($photo){
            $ptk->photo = str_replace('public/', '', $photo);
        }
        $ptk->save();
        $user = User::where('ptk_id', request()->ptk_id)->first();
        if($user){
            $user->email = strtolower(request()->email);
            if($photo){
                $user->profile_photo_path = str_replace('public/', '', $photo);
            }
            $user->save();
        } else {
            $role = Role::where('name', 'ptk')->first();
            $team = $this->get_team();
            $user = User::create(
                [
                    'ptk_id' => $ptk->ptk_id,
                    'name' => request()->nama,
                    'email' => strtolower(request()->email),
                    'sekolah_id' => $ptk->sekolah_id,
                    'profile_photo_path' => str_replace('public/', '', $photo),
                    'password' => bcrypt('12345678'),
                ]
            );
            if(!$user->hasRole($role, $team)){
                $user->attachRole($role, $team);
            }
        }
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data PTK berhasil diperbaharui',
        ];
        return response()->json($data);
    }
    public function get_team(){
        $semester = Semester::find(request()->semester_id);
        $team = Team::where('name', $semester->nama)->first();
        return $team;
    }
    public function add_ptk(){
        request()->validate(
            [
                'ptk_id.*' => 'required|uuid',
                'nama.*' => 'required',
                'nuptk.*' => 'nullable', // 1MB Max
                'jenis_kelamin.*' => 'required',
                'tempat_lahir.*' => 'required',
                'tanggal_lahir.*' => 'required',
                'email.*' => 'email|required',
            ],
            [
                'ptk_id.*.required' => 'ID PTK tidak boleh kosong!',
                'ptk_id.*.uuid' => 'ID PTK tidak valid!',
                'nama.*.required' => 'Nama tidak boleh kosong!',
                'jenis_kelamin.*.required' => 'Jenis Kelamin tidak boleh kosong!',
                'tempat_lahir.*.required' => 'Tempat Lahir tidak boleh kosong!',
                'tanggal_lahir.*.required' => 'Tanggal Lahir tidak boleh kosong!',
                'tanggal_lahir.*.date' => 'Tanggal Lahir tidak valid!',
                'email.*.required' => 'Email tidak boleh kosong!',
                'email.*.email' => 'Email tidak valid',
            ]
        );
        foreach(request()->ptk_id as $key => $ptk_id){
            Ptk::insert([
                'ptk_id' => request()->ptk_id[$key],
                'sekolah_id' => request()->sekolah_id,
                'nama' => request()->nama[$key],
                'nuptk' => request()->nuptk[$key],
                'jenis_kelamin' => request()->jenis_kelamin[$key],
                'tempat_lahir' => request()->tempat_lahir[$key],
                'tanggal_lahir' => request()->tanggal_lahir[$key],
                'email' => request()->email[$key],
                'jenis_ptk_id' => 0,
                'agama_id' => 1,
                'status_kepegawaian_id' => 1,
            ]);
        }
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data PTK Baru berhasil disimpan',
        ];
        return response()->json($data);
    }
}
