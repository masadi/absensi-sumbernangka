<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class GenerateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:role';

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
        $roles = [
            [
                'name' => 'administrator',
                'display_name' => 'Super Admin',
                'description' => 'Administrator',
            ],
            [
                'name' => 'unit',
                'display_name' => 'Admin Unit',
                'description' => 'Admin Unit',
            ],
            [
                'name' => 'ptk',
                'display_name' => 'GTK',
                'description' => 'Guru dan Tenaga Kependidikan',
            ],
            [
                'name' => 'bk',
                'display_name' => 'Guru BP/BK',
                'description' => 'Guru BP/BK',
            ],
            [
                'name' => 'staf',
                'display_name' => 'Tenaga Kependidikan',
                'description' => 'Tenaga Kependidikan',
            ],
            [
                'name' => 'pd',
                'display_name' => 'Peserta Didik',
                'description' => 'Peserta Didik',
            ],
            [
                'name' => 'walas',
                'display_name' => 'Wali Kelas',
                'description' => 'Wali Kelas',
            ]
        ];
        foreach($roles as $role){
            Role::updateOrCreate(
                [
                    'name' => $role['name'],
                ],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
        return Command::SUCCESS;
    }
}
