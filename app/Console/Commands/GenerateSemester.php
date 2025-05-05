<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Semester;

class GenerateSemester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:semester';

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
        Semester::where('periode_aktif', 1)->update(['periode_aktif' => 0]);
        Semester::updateOrCreate([
            'semester_id' => 20241,
            'nama' => '2024/2025 Ganjil',
            'semester' => 1,
            'periode_aktif' => 1,
            'tahun_ajaran_id' => 2024,
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2026-12-30',
        ]);
        return Command::SUCCESS;
    }
}
