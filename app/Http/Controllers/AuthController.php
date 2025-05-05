<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Sekolah;
use App\Models\User;
use App\Models\Guru;
use App\Models\Peserta_didik;
use App\Models\Role;
use App\Models\Team;
use App\Models\Semester;
use App\Models\Jadwal;
use App\Models\Rombongan_belajar;
use App\Models\Pembelajaran;
use App\Models\Tingkat_pendidikan;
use Validator;
use Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(
            [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
            ],
            [
                'email.required' => 'Email tidak boleh kosong',
                'email.exists' => 'Email tidak terdaftar',
                'password.required' => 'Password tidak boleh kosong'
            ]
        );

        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials))
        {
            return response()->json([
                'user' => NULL,
                'errors' => [
                    'password' => ['Password salah!'],
                ]
            ],401);
        }
        $semester = Semester::find(semester_id());
        if(!$semester){
            $semester = Semester::where('periode_aktif', 1)->first();
        }
        //$team = Team::where('name', $semester->nama)->first();
        $team = Team::firstOrCreate([
            'name' => $semester->nama,
            'display_name' => $semester->nama,
            'description' => $semester->nama,
        ]);
        $user = $request->user();
        if(!$user->peserta_didik_id && !$user->ptk_id){
            if(!$user->hasRole('administrator', $semester->nama)){
                $user->attachRole('administrator', $team);
            }
        }
        $user->save();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        $user->avatar = NULL;
        $user->role = $user->roles()->wherePivot(config('laratrust.foreign_keys.team'), $team->id)->get()->implode('display_name', ', ');
        $user->roles = $user->roles()->wherePivot(config('laratrust.foreign_keys.team'), $team->id)->get();
        $user->ability = $user->allPermissions(['description as action', 'name as subject'], $semester->nama);
        $user->semester = $semester;
        /*$abilities = [];
        foreach($user->ability as $ability){
            $output['action'] = $ability->action;
            $output['subject'] = $ability->subject;
            $abilities[] = $output;
        }*/
        $user->accessToken = $token;
        return response()->json([
            'userData' => $user,
        ]);
        return response()->json([
            'user' => $user,
            'userAbilities' => $user->ability,
            'accessToken' =>$token,
            'token_type' => 'Bearer',
            'semester_id' => ($semester) ? $semester->semester_id : NULL,
            'periode_aktif' => ($semester) ? $semester->nama : NULL,
        ]);
    }
    public function user(Request $request)
    {
        $profile = NULL;
        if($request->user()->hasRole('guru', periode_aktif())){
            $profile = Guru::find($request->user()->guru_id);
        }
        if($request->user()->hasRole('pd', periode_aktif())){
            $profile = Peserta_didik::find($request->user()->peserta_didik_id);
        }
        $jadwal = [];
        if($request->user()->hasRole(['guru', 'pd'], periode_aktif())){
            $jadwal = Jadwal::with([
                'pembelajaran' => function($query){
                    $query->where('semester_id', semester_id());
                },
                'guru',
                'kelas',
                'jam'
            ])->where(function($query) use ($request){
                $query->whereHas('pembelajaran', function($query) use ($request){
                    $query->where('semester_id', semester_id());
                    if($request->user()->guru_id){
                        $query->where('guru_id', $request->user()->guru_id);
                    } else {
                        $query->whereHas('anggota_rombel', function($query) use ($request){
                            $query->where('peserta_didik_id', $request->user()->peserta_didik_id);
                        });
                    }
                });
            })->get();
        }
        return response()->json([
            'user' => $request->user(),
            'role' => $request->user()->roles->unique()->pluck('display_name')->toArray(),
            'profile' => $profile,
            'guru_id' => $request->user()->guru_id,
            'peserta_didik_id' => $request->user()->peserta_didik_id,
            'jadwal' => $jadwal,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function semester(){
        $data = [
            'semester' => Semester::whereHas('tahun_ajaran', function($query){
                $query->where('periode_aktif', 1);
            })->orderBy('semester_id', 'DESC')->get(),
            'aktif' => Semester::find(get_setting('semester_id')),
        ];
        return response()->json($data);
    }
    public function generate(){
        /*
        $adminRole = Role::where('name', 'administrator')->first();
        $guruRole = Role::where('name', 'guru')->first();
        $kepsekRole = Role::where('name', 'kepsek')->first();
        $pengajarRole = Role::where('name', 'pengajar')->first();
        $kajurRole = Role::where('name', 'kajur')->first();
        $wakakurRole = Role::where('name', 'wakakur')->first();
        $wakahumasRole = Role::where('name', 'wakahumas')->first();
        $wakasiswaRole = Role::where('name', 'wakasiswa')->first();
        $bkRole = Role::where('name', 'bk')->first();
        $walasRole = Role::where('name', 'walas')->first();
        $piketRole = Role::where('name', 'piket')->first();
        $pdRole = Role::where('name', 'pd')->first();
        */
        $all_role = ["administrator", "ptk", "staf", "pd", "unit", "bk", "walas"];
        if(request()->jenis == 'ptk'){
            Guru::orderBy('guru_id')->chunk(200, function ($data) use ($all_role){
                foreach ($data as $d) {
                    $new_password = strtolower(Str::random(8));
                    $user = User::where('guru_id', $d->guru_id)->first();
                    if(!$user){
                        $user_email = $this->check_email($d, 'guru_id');
                        $user = User::create([
                            'name' => $d->nama_lengkap,
                            'email' => $user_email,
                            'password' => bcrypt($new_password),
                            'default_password' => $new_password,
                            'sekolah_id'	=> $d->sekolah_id,
                            'guru_id'	=> $d->guru_id,
                        ]);
                    } else {
                        $user->guru_id = $d->guru_id;
                        $user->name = $d->nama_lengkap;
                        $user->save();
                    }
                    $user->detachRoles($all_role, request()->periode_aktif);
                    $user->attachRole('guru', request()->periode_aktif);
                    $find = Rombongan_belajar::where('guru_id', $d->guru_id)->where('semester_id', request()->semester_id)->first();
                    if($find){
                        $user->attachRole('walas', request()->periode_aktif);
                    }
                    $find = Pembelajaran::where('guru_id', $d->guru_id)->where('semester_id', request()->semester_id)->first();
                    if($find){
                        $user->attachRole('pengajar', request()->periode_aktif);
                    }
                }
            });
            $data = [
                'icon' => 'success',
                'text' => 'Pengguna Guru berhasil diperbaharui',
                'title' => 'Berhasil',
            ];
        } else {
            Peserta_didik::doesntHave('pengguna')->whereHas('anggota_rombel', function($query){
                $query->where('semester_id', semester_id());
            })->orderBy('peserta_didik_id')->chunk(200, function ($data){
                foreach ($data as $d) {
                    $new_password = strtolower(Str::random(8));
                    $user = User::where('peserta_didik_id', $d->peserta_didik_id)->first();
                    if(!$user){
                        $user_email = $this->check_email($d, 'peserta_didik_id');
                        $user = User::create([
                            'name' => $d->nama,
                            'email' => $user_email,
                            'password' => bcrypt($new_password),
                            'default_password' => $new_password,
                            'sekolah_id'	=> $d->sekolah_id,
                            'peserta_didik_id'	=> $d->peserta_didik_id,
                        ]);
                    } else {
                        $user->peserta_didik_id = $d->peserta_didik_id;
                        $user->name = $d->nama_lengkap;
                        $user->save();
                    }
                    if(!$user->hasRole('pd', request()->periode_aktif)){
                        $user->attachRole('pd', request()->periode_aktif);
                    }
                }
            });
            $data = [
                'icon' => 'success',
                'text' => 'Pengguna Peserta Didik berhasil diperbaharui',
                'title' => 'Berhasil',
            ];
        }
        return response()->json($data);
    }
    private function check_email($user, $field){
        $loggedUser = auth()->user();
        $random = Str::random(8);
		$user->email = ($user->email != $loggedUser->email) ? $user->email : strtolower($random).'@smkn1sampang.sch.id';
		$user->email = strtolower($user->email);
        if($field == 'guru_id'){
            $find_user_email = User::where('email', $user->email)->where($field, '<>', $user->ptk_id)->first();
		} else {
            $find_user_email = User::where('email', $user->email)->where($field, '<>', $user->peserta_didik_id)->first();
		}
        $find_user_email = User::where('email', $user->email)->first();
		if($find_user_email){
			$user->email = strtolower($random).'@smkn1sampang.sch.id';
		}
        return $user->email;
    }
    public function list(){
        $team = Team::where('name', request()->periode_aktif)->first();
        $data = User::with([
            'roles' => function($query) use ($team){
                $query->wherePivot('team_id', $team->id);
            },
            'sekolah' => function($query){
                $query->select('sekolah_id', 'nama');
            },
            'pd' => function($query){
                $query->select('peserta_didik_id');
                $query->with(['kelas' => function($query){
                    $query->select('rombongan_belajar.rombongan_belajar_id', 'nama');
                }]);
            },
        ])->where(function($query){
            $query->whereRoleIs(['pd'], request()->periode_aktif);
            $query->whereNotNull('sekolah_id');
        })
        ->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })
        ->when(request()->tingkat, function($query) {
            $query->whereHas('pd.kelas', function($query){
                $query->where('tingkat_pendidikan_id', request()->tingkat);
                $query->where('rombongan_belajar.semester_id', request()->semester_id);
            });
        })
        ->when(request()->rombongan_belajar_id, function($query) {
            $query->whereHas('pd.kelas', function($query){
                $query->where('rombongan_belajar.rombongan_belajar_id', request()->rombongan_belajar_id);
            });
        })
        ->when(request()->q, function($query) {
            $query->where('name', 'ilike', '%'.request()->q.'%');
            $query->orWhere('email', 'ilike', '%'.request()->q.'%');
            $query->orWhereHas('pd.kelas', function($query){
                $query->where('nama', 'ilike', '%'.request()->q.'%');
            });
        })->paginate(request()->per_page);
        $data_sekolah = Sekolah::select('sekolah_id', 'nama')->whereNotNull('bentuk_pendidikan_id')->orderBy('bentuk_pendidikan_id')->get();
        $data_tingkat = Tingkat_pendidikan::whereHas('sekolah', function($query){
            $query->where('sekolah_id', request()->sekolah_id);
            $query->whereHas('rombongan_belajar', function($query){
                $query->where('semester_id', request()->semester_id);
            });
        })->orderBy('tingkat_pendidikan_id')->get();
        $data_rombel = Rombongan_belajar::where(function($query){
            $query->where('sekolah_id', request()->sekolah_id);
            $query->where('tingkat_pendidikan_id', request()->tingkat);
            $query->where('semester_id', request()->semester_id);
        })->orderBy('nama')->get();
        return response()->json([
            'status' => 'success', 
            'data' => $data, 
            'data_sekolah' => $data_sekolah,
            'data_tingkat' => $data_tingkat,
            'data_rombel' => $data_rombel,
            'request' => request()->request,
        ]);
    }
    public function detil(){
        $team = Team::where('name', periode_aktif())->first();
        $user = User::with(['roles' => function($query) use ($team){
            $query->wherePivot('team_id', $team->id);
        }])->find(request()->user_id);
        $data = [
            'user' => $user,
            //'roles' => ($user->hasRole('pd', periode_aktif())) ? Role::select('name', 'display_name')->whereIn('name', ['pd'])->get() : Role::select('name', 'display_name')->whereNotIn('name', ['administrator', 'guru'])->get(),
            'roles' => Role::select('id', 'name', 'display_name')->where(function($query) use ($user){
                if($user->hasRole('pd', periode_aktif())){
                    $query->whereIn('name', ['pd']);
                } else {
                    $query->whereNotIn('name', ['administrator', 'guru', 'pd', 'walas']);
                }
            })->get(),
            'akses' => $user->hasRole('guru', periode_aktif()),
        ];
        return response()->json($data);
    }
    private function kondisi(){
        return function($query){
            $query->whereRoleIs(['guru', 'pd'], request()->periode_aktif);
        };
    }
    public function hapus(){
        if(User::where('user_id', request()->user_id)->delete()){
            $data = [
                'icon' => 'success',
                'text' => 'Pengguna berhasil dihapus',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Pengguna gagal dihapus. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);        
    }
    public function reset_password(){
        $user = User::find(request()->user_id);
        $user->password = bcrypt($user->default_password);
        if($user->save()){
            $data = [
                'icon' => 'success',
                'text' => 'Password Pengguna berhasil direset',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'danger',
                'text' => 'Password Pengguna gagal direset. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data); 
    }
    public function foto(Request $request){
        $request->validate([
            'foto' => 'required|mimes:jpg,jpeg,png',
        ]);
        $foto = $request->foto->store('public/images');
        $generated_new_name = basename($foto);
        //$file_name = $request->foto->getClientOriginalName();
        //$generated_new_name = time() . '.' . $request->foto->getClientOriginalExtension();
        $user = User::with(['guru', 'pd'])->find(request()->user_id);
        $user->photo = $generated_new_name;
        $user->save();
        if($user->guru){
            $user->guru->photo = $generated_new_name;
            $user->guru->save();
        }
        if($user->pd){
            $user->pd->photo = $generated_new_name;
            $user->pd->save();
        }
        //$request->foto->move($upload_path, $generated_new_name);
        
        $data = [
            'icon' => 'success',
            'text' => 'Foto Profil berhasil diperbaharui',
            'title' => 'Berhasil',
            'foto' => $generated_new_name,
        ];
        return response()->json($data); 
    }
    public function ganti_password(){
        request()->validate([
            'password' => 'required|confirmed',
        ]);
        $user = request()->user();
        $user->password = bcrypt(request()->password);
        if($user->save()){
            $data = [
                'icon' => 'success',
                'text' => 'Password Anda berhasil diperbaharui',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'text' => 'Password gagal diperbaharui. Silahkan coba beberapa saat lagi',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function update_role(){
        $user = User::find(request()->user_id);
        $roles = Role::whereNotIn('name', array_filter(request()->hak_akses))->get();
        //$user->attachRole($guruRole, request()->periode_aktif);
        $user->attachRoles(array_filter(request()->hak_akses), periode_aktif());
        $user->detachRoles($roles, periode_aktif());
        $data = [
            'icon' => 'success',
            'text' => 'Hak akses pengguna berhasil diperbaharui',
            'title' => 'Berhasil',
        ];
        return response()->json($data);
    }
    public function profile(){
        return response()->json(auth()->user());
    }
    public function update_profile(){
        $user = auth()->user();
        if(request()->has('name')){
            request()->validate(
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                    'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
                ],
                [
                    'name.required' => 'Nama Lengkap tidak boleh kosong!',
                    'email.required' => 'Email tidak boleh kosong!',
                    'email.email' => 'Email tidak valid!',
                    'email.unique' => 'Email sudah terdaftar di Database!',
                    'photo.mimes' => 'Foto harus berekstensi jpg/jpeg/png',
                    'photo.max' => 'Ukuran foto tidak boleh lebih dari 1 MB!',
                ],
            );
            $user->name = request()->name;
            //$user->email = request()->email;
            $user->email = request()->email;
            //profile-photos
            if(request()->photo){
                $photo = request()->photo->store('public/profile-photos');
                $user->profile_photo_path = 'profile-photos/'.basename($photo);
            }
            if($user->save()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Profil Pengguna berhasil diperbaharui',
                ];
            } else {
                $data = [
                    'icon' => 'danger',
                    'title' => 'Gagal!',
                    'text' => 'Profil Pengguna gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $message = [
                //'current_password.required' => 'Kata sandi saat ini tidak boleh kosong',
                //'current_password.current_password' => 'Kata sandi salah',
                'password.required' => 'Kata sandi baru tidak boleh kosong',
                'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai dengan Kata sandi baru',
                'password_confirmation.required' => 'Konfirmasi kata sandi tidak boleh kosong',
            ];
            $rules = [
                //'current_password' => ['required', 'current_password'],
                'password' => [
                    'required',
                    'confirmed',
                ],
                'password_confirmation' => ['required'],
            ];
            $validator = Validator::make(request()->all(), $rules, $message)->validated();
            $user->password = Hash::make(request()->password);
            if($user->save()){
                $data = [
                    'success' => TRUE,
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Password Pengguna berhasil diperbaharui',
                ];
            } else {
                $data = [
                    'success' => FALSE,
                    'icon' => 'danger',
                    'title' => 'Gagal!',
                    'text' => 'Password Pengguna gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                ];
            }
        }
        return response()->json($data);
        $data = [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Profil Pengguna berhasil diperbaharui',
        ];
        return response()->json($data);
    }
}
