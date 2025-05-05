<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Semester;
use App\Models\Team;

class GenerateAkses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:akses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $abilities = [
            [
                'role' => 'administrator',
                'akses' => ['Web', 'Beranda', 'Profile', 'Ref_Unit', 'Ref_Guru', 'Ref_Pd', 'Ref_Rombel', 'Kehadiran_Guru', 'Ketidakhadiran_Guru', 'Kehadiran_Pd', 'Ketidakhadiran_Pd', 'Rekapitulasi', 'Pengaturan_Umum', 'Pengaturan_libur', 'Pengaturan_Jam', 'Admin_Unit', 'Guru_Bp'],
                'action' => 'read',
            ],
            [
                'role' => 'unit',
                'akses' => ['Web', 'Beranda', 'Profile', 'Ref_Guru', 'Ref_Pd', 'Ref_Rombel', 'Kehadiran_Guru', 'Ketidakhadiran_Guru', 'Rekapitulasi', 'Pengaturan_Umum'],
                'action' => 'read',
            ],
            [
                'role' => 'ptk',
                'akses' => ['Web', 'Beranda', 'Profile'],
                'action' => 'read',
            ],
            [
                'role' => 'staf',
                'akses' => ['Web', 'Beranda', 'Profile', 'Ptk_Pd'],
                'action' => 'read',
            ],
            [
                'role' => 'pd',
                'akses' => ['Web', 'Beranda', 'Profile', 'Ptk_Pd', 'PD'],
                'action' => 'read',
            ],
            [
                'role' => 'bk',
                'akses' => ['Pelanggaran'],
                'action' => 'read',
            ],
            [
                'role' => 'walas',
                'akses' => ['Kehadiran_Pd', 'Ketidakhadiran_Pd'],
                'action' => 'read',
            ],
        ];
        Permission::truncate();
        foreach($abilities as $ab){
            $r = Role::where('name', $ab['role'])->first();
            if($r){
                foreach($ab['akses'] as $perm){
                    $permission = Permission::updateOrCreate([
                        'name' => $perm,
                        'display_name' => $perm,
                        'description' => 'read',
                    ]);
                    if(!$r->hasPermission($permission->name)){
                        $r->attachPermission($permission);
                    }
                }
            }
        }
        $users = User::whereNotNull('ptk_id')->orWhereNotNull('peserta_didik_id')->get();
        $all_role = ["administrator", "ptk", "staf", "pd", "walas"];
        $semester = Semester::orderBy('semester_id', 'DESC')->get();
        $i=1;
        foreach($users as $user){
            $this->info($i.'. '.$user->name);
            foreach($semester as $smt){
                if($user->hasRole($all_role, $smt->nama)){
                    $user->detachRoles($all_role, $smt->nama);
                }
                $team = Team::where('name', $smt->nama)->first();
                if($user->ptk_id){
                    $user->attachRole('ptk', $team);
                }
                if($user->peserta_didik_id){
                    $user->attachRole('pd', $team);
                }
                //dd($smt);
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
