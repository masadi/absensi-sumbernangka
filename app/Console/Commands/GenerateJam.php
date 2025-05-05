<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jam;
use App\Models\Jam_ptk;
use App\Models\Jam_hari;

class GenerateJam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:jam {id}';

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
        $jam = Jam::find($this->argument('id'));
        $jam->nama = $jam->nama.' Genap';
        $jam->semester_id = semester_id();
        $jam_ptk = Jam_ptk::where('jam_id', $jam->id)->get();
        $jam_hari = Jam_hari::where('jam_id', $jam->id)->get();
        unset($jam->id);
        $new = Jam::create($jam->toArray());
        foreach($jam_ptk as $ptk){
            unset($ptk->id);
            $ptk->jam_id = $new->id;
            Jam_ptk::create($ptk->toArray());
        }
        foreach($jam_hari as $hari){
            unset($hari->id);
            $hari->jam_id = $new->id;
            Jam_hari::create($hari->toArray());
        }
        return Command::SUCCESS;
    }
}
