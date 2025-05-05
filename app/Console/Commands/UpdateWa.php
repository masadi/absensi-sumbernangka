<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Peserta_didik;

class UpdateWa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:wa';

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
        $data = (new FastExcel)->import(public_path('templates/peserta_didik.xlsx'), function ($line) {
            $find = Peserta_didik::find($line['peserta_didik_id']);
            if($find){
                $find->wa = gantiformat($line['wa']);
                if($find->save()){
                    $this->info($find->nama.' berhasil diupdate: '.gantiformat($line['wa']));
                } else {
                    $this->error($find->nama.' gagal diupdate');
                }
            } else {
                $this->error($line['nama'].' tidak ditemukan');
            }
        });
        return Command::SUCCESS;
    }
}
