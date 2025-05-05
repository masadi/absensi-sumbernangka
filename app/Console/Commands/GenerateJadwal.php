<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Models\Jadwal_ujian;
use App\Models\Catatan_ujian;

class GenerateJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:jadwal';

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
        $jadwal = Jadwal_ujian::with(['rombongan_belajar'])->orderBy('id')->get();
        foreach($jadwal as $a){
            $new = Jadwal::updateOrCreate([
                'nama' => strtoupper($a->jenis).' '.$a->rombongan_belajar->nama,
                'tanggal' => now()->format('Y-m-d'),
                'ptk_id' => $a->rombongan_belajar->ptk_id,
                'rombongan_belajar_id' => $a->rombongan_belajar_id,
            ]);
            $a->jadwal_id = $new->id;
            $a->tanggal = now()->format('Y-m-d');
            $a->save();
            $b = Catatan_ujian::where('rombongan_belajar_id', $a->rombongan_belajar_id)->where('jenis', $a->jenis)->update(['jadwal_id' => $new->id]);
        }
        return Command::SUCCESS;
    }
}
