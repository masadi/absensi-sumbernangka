<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sekolah;

class CekFoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cek:foto';

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
        $data = Sekolah::withWhereHas('pd', function($query){
            $query->whereNull('photo');
        })->get();
        foreach($data as $d){
            $this->info($d->nama.':');
            $i=1;
            foreach($d->pd as $pd){
                $this->info($i.'. '.$pd->nama);
                $i++;
            }
        }
        return Command::SUCCESS;
    }
}
