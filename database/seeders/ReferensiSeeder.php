<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Nama_hari;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Team;
use App\Models\Role;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Jam;

class ReferensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nama_hari = collect(["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"]);
        foreach($nama_hari as $hari){
            Nama_hari::updateOrCreate(['nama' => $hari]);
        }
        $this->command->info('Nama hari berhasil diproses');
        $role = Role::where('name', 'administrator')->first();
        User::whereNotNull('email')->delete();
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@smpalfalahsg.sch.id',
            'password' => bcrypt('12345678'),
        ]);
        $semester = [
            [
                'semester_id' => 20241,
                'tahun_ajaran_id' => 2024,
                'nama' => '2024/2025 Ganjil',
                'semester' => 1,
                'periode_aktif' => 1,
            ],
            [
                'semester_id' => 20242,
                'tahun_ajaran_id' => 2024,
                'nama' => '2024/2025 Genap',
                'semester' => 2,
                'periode_aktif' => 0,
            ]
        ];
        foreach($semester as $s){
            Semester::updateOrCreate([
                'semester_id' => $s['semester_id'],
                'tahun_ajaran_id' => $s['tahun_ajaran_id'],
                'nama' => $s['nama'],
                'semester' => $s['semester'],
                'periode_aktif' => $s['periode_aktif'],
                'tanggal_mulai' => ($s['semester'] == 1) ? $s['tahun_ajaran_id'].'-07-01' : ($s['tahun_ajaran_id'] + 1).'-01-01',
                'tanggal_selesai' => ($s['semester'] == 1) ? $s['tahun_ajaran_id'].'-12-31' : ($s['tahun_ajaran_id'] + 1).'-06-30',
            ]);
            $team = Team::updateOrCreate([
                'name' => $s['nama'],
                'display_name' => $s['nama'],
                'description' => $s['nama'],
            ]);
            $user->attachRole($role, $team);
        }
        $settings = [
            [
                'key' => 'jarak',
                'value' => '1000000'
            ],
            [
                'key' => 'app_name',
                'value' => 'Absensi'
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0'
            ],
        ];
        foreach($settings as $setting){
            Setting::updateOrCreate(
                [
                    'key' => $setting['key']
                ],
                [
                    'value' => $setting['value']
                ]
            );
        }
        $npsn = ['20527178'];
        $teams = Team::all();
        $semester = Semester::where('periode_aktif', 1)->first();
        foreach($npsn as $n){
            $this->sedot_sekolah($n, $teams, $semester);
        }
    }
    private function sedot_sekolah($npsn, $teams, $semester){
        $sync_sekolah = Http::get('http://sync.erapor-smk.net/api/v7/sekolah/'.$npsn);
        $sekolah = $sync_sekolah->object();
        if($sekolah->data){
            $sekolah = $sekolah->data;
            $sekolah_id = strtolower($sekolah->sekolah_id);
            $new_sekolah = Sekolah::updateOrCreate(
                ['sekolah_id' => $sekolah_id],
                [
                    'npsn' => $sekolah->npsn,
                    'nama' => $sekolah->nama,
                    'nss' => $sekolah->nss,
                    'alamat' => $sekolah->alamat_jalan,
                    'desa_kelurahan' => $sekolah->desa_kelurahan,
                    'kode_pos' => $sekolah->kode_pos,
                    'lintang' => $sekolah->lintang,
                    'bujur' => $sekolah->bujur,
                    'no_telp' => $sekolah->nomor_telepon,
                    'no_fax' => $sekolah->nomor_fax,
                    'email' => $sekolah->email,
                    'website' => $sekolah->website,
                    'status_sekolah' => $sekolah->status_sekolah,
                    'bentuk_pendidikan_id' => $sekolah->bentuk_pendidikan_id,
                ]
            );
        } else {
            $this->command->error($npsn.' gagal disimpan');
        }
    }
    private function generateEmail(){
        $random = Str::random(6);
        return strtolower($random).'@ariyametta.sch.id';
    }
}
