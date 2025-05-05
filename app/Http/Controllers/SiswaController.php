<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Sekolah;
use App\Models\Peserta_didik;
use App\Models\Anggota_rombel;
use App\Models\User;
use App\Models\Rombongan_belajar;
use Carbon\Carbon;
use ZipArchive;
use Storage;

class SiswaController extends Controller
{
    public function index(){
        $data_sekolah = [];
        $user = auth()->user();
        if($user->hasRole('administrator', request()->periode_aktif)){
            $data_sekolah = Sekolah::select('sekolah_id', 'nama')->get();
        }
        $data = Peserta_didik::withWhereHas('kelas', function($query) use ($user){
            $query->where('rombongan_belajar.semester_id', request()->semester_id);
            if($user->sekolah_id){
                $query->where('sekolah_id', $user->sekolah_id);
            }
        })->with([
            'sekolah' => function($query){
                $query->select('sekolah_id', 'nama', 'bentuk_pendidikan_id');
            }
        ])->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ILIKE', '%' . request()->q . '%');
            $query->orWhere('nisn', 'ILIKE', '%' . request()->q . '%');
        })
        ->when(request()->sekolah_id, function($query) {
            $query->where('sekolah_id', request()->sekolah_id);
        })->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data, 'data_sekolah' => $data_sekolah]);
    }
    public function pindah_rombel(){
        request()->validate(
            [
                'tingkat' => 'required',
                'rombongan_belajar_id' => 'required',
            ],
            [
                'tingkat.required' => 'Tingkat Kelas tidak boleh kosong!',
                'rombongan_belajar_id.required' => 'Rombel Tujuan tidak boleh kosong!',
            ],
        );
        $update = Anggota_rombel::where('semester_id', semester_id())->where('peserta_didik_id', request()->peserta_didik_id)->update([
            'rombongan_belajar_id' => request()->rombongan_belajar_id
        ]);
        if($update){
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Rombongan Belajar berhasil dipindah',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Rombongan Belajar gagal dipindah. Silahkan coba beberapa saat lagi',
            ];
        }
        return response()->json($data);
        request()->reset(['tingkat', 'rombongan_belajar_id']);
        request()->emit('close-modal');
        request()->alert('success', 'Rombongan Belajar berhasil dipindah', [
            'position' => 'center'
        ]);
    }
    public function get_siswa(){
        $data = [
            'siswa' => Peserta_didik::select('peserta_didik_id', 'nama')->withWhereHas('anggota_rombel', function($query){
                $query->select('peserta_didik_id', 'anggota_rombel_id');
                $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
            })->orderBy('nama')->get(),
            'petugas' => User::whereRoleIs(['bk'], request()->periode_aktif)->orderBy('name')->select('id', 'name')->get(),
        ];
        return response()->json($data);
    }
    public function import_pd(){
        request()->validate(
            [
                'file_excel' => 'required|mimes:zip',
                'sekolah_id' => 'required',
                'jumlah' => 'required|numeric|min:0|not_in:0',
            ],
            [
                'file_excel.required' => 'File Excel tidak boleh kosong',
                'file_excel.mimes' => 'File harus berupa file dengan tipe: zip.',
                'sekolah_id.required' => 'Unit tidak boleh kosong',
                'jumlah.required' => 'Jumlah Siswa tidak boleh kosong',
                'jumlah.numeric' => 'Jumlah harus berupa angka',
                'jumlah.min' => 'Jumlah tidak boleh dibawah 0 (nol)',
                'jumlah.not_in' => 'Jumlah harus di atas 0 (nol)',
            ]
        );
        $dir_path = 'berkas';
        $file_new_path = request()->file_excel->store('files', 'public');
        $zip = new ZipArchive();
        //$file_new_path = $file->storeAs($dir_path . 'zip' , $filename, 'local');
        $zipFile = $zip->open(Storage::disk('public')->path($file_new_path));
        if ($zipFile === TRUE) {
            $zip->extractTo(Storage::disk('public')->path($dir_path . '-zip')); 
            $zip->close();
        }
        $directory = $dir_path . '-zip';
        $files = Storage::disk('public')->files($directory);
        $collection = collect($files);
        $xlsx = $collection->filter(function ($value, $key) {
            $extension = pathinfo(storage_path($value), PATHINFO_EXTENSION);
            return $extension == 'xlsx';
        });
        $images = $collection->filter(function ($value, $key) {
            $extension = pathinfo(storage_path($value), PATHINFO_EXTENSION);
            return $extension != 'xlsx';
        });
        $mode_img = 0;
        foreach($images as $img){
            $folder = str_replace('berkas-zip', 'profile-photos', $img);
            if(Storage::disk('public')->move($img, $folder)){
                $mode_img++;
            }
        };
        $imported_data = (new FastExcel)->import(storage_path('/app/public/'.$xlsx->first()));
        $collection = collect($imported_data);
        $multiplied = $collection->map(function ($items, $key) {
            foreach($items as $k => $v){
                $k = str_replace('.','',$k);
                $k = str_replace(' ','_',$k);
                $k = str_replace('/','_',$k);
                $k = strtolower($k);
                $item[$k] = $v;
            }
            return $item;
        });
        $insert = 0;
        $foto = [];
        foreach($multiplied->all() as $urut => $pd){
            $rombel = Rombongan_belajar::where('nama', $pd['kelas'])->where('semester_id', semester_id())->first();
            if($rombel){
                $photo = NULL;
                if (Storage::disk('public')->exists('profile-photos/'.$pd['uuid'].'.jpg')) {
                    $photo = 'profile-photos/'.$pd['uuid'].'.jpg';
                }
                if (Storage::disk('public')->exists('profile-photos/'.$pd['uuid'].'.png')) {
                    $photo = 'profile-photos/'.$pd['uuid'].'.png';
                }
                if (Storage::disk('public')->exists('profile-photos/'.$pd['uuid'].'.jpeg')) {
                    $photo = 'profile-photos/'.$pd['uuid'].'.jpeg';
                }
                $foto[] = $photo;
                $insert++;
                $tanggal_lahir = NULL;
                try {
                    $tgl_lahir = str_replace("'", "", $pd['tanggal_lahir']);
                    $tanggal_lahir = Carbon::parse($tgl_lahir)->format('Y-m-d');
                } catch (\Throwable $th) {
                   dd($th->getMessage());
                }
                $peserta_didik = Peserta_didik::updateOrCreate(
                    [
                        'peserta_didik_id' => $pd['uuid']
                    ],
                    [
                        'sekolah_id' => request()->sekolah_id,
                        'nama' => $pd['nama_siswa'],
                        'no_induk' => $pd['nis'],
                        'nisn' => Str::padLeft($pd['nisn'], 10, 0),
                        'nik' => 0,
                        'jenis_kelamin' => $pd['jenis_kelamin'],
                        'photo' => $photo,
                        'tempat_lahir' => $pd['tempat_lahir'],
                        'tanggal_lahir' => $tanggal_lahir,
                        /*'agama_id' => $dapodik->agama_id,
                        'alamat' => $dapodik->alamat_jalan,
                        'desa_kelurahan' => $dapodik->desa_kelurahan,
                        'kecamatan' => $dapodik->wilayah->parrent_recursive->nama,
                        'kode_wilayah' => $dapodik->kode_wilayah,
                        'nama_ayah' => $dapodik->nama_ayah,
                        'nama_ibu' => $dapodik->nama_ibu_kandung,
                        'email' => $email,*/
                    ]
                );
                Anggota_rombel::updateOrCreate(
                    [
                        'anggota_rombel_id' => $pd['uuid'],
                    ],
                    [
                        'rombongan_belajar_id' => $rombel->rombongan_belajar_id,
                        'peserta_didik_id' => $pd['uuid'],
                        'semester_id' => semester_id(),
                    ]
                );
            } else {
                
            }
        }
        Storage::disk('public')->deleteDirectory($directory);
        $data = [
            'result' => $multiplied->all(),
            'icon' => ($insert) ? 'success' : 'error',
            'title' => ($insert) ? 'Berhasil' : 'Gagal',
            'text' => ($insert) ? 'Data Peserta Didik berhasil disimpan' : 'Data Peserta Didik gagal disimpan',
            'mode_img' => $mode_img,
            'semester' => semester_id(),
            'foto' => $foto,
        ];
        return response()->json($data);
    }
    public function delete(){
        $find = Peserta_didik::find(request()->peserta_didik_id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Peserta Didik berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Data Peserta Didik gagal dihapus. Silahkan coba beberapa saat lagi',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Data Peserta Didik tidak ditemukan',
            ];
        }
        return response()->json($data);
    }
    public function update_siswa(){
        $find = Peserta_didik::find(request()->peserta_didik_id);
        if($find){
            $find->nama = request()->nama;
            $find->nik = request()->nik;
            $find->no_induk = request()->no_induk;
            $find->nisn = request()->nisn;
            $find->wa = gantiformat(request()->wa);
            if($find->save()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Peserta Didik berhasil diperbaharui',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Data Peserta Didik gagal diperbaharui. Silahkan coba beberapa saat lagi',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Data Peserta Didik tidak ditemukan',
            ];
        }
        return response()->json($data);
    }
}
