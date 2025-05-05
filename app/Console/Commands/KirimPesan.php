<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Absen;
use Carbon\Carbon;

class KirimPesan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kirim:pesan';

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
        $absen = Absen::whereDate('created_at', Carbon::today())->with(['absen_masuk_pertama', 'absen_masuk_kedua', 'absen_pulang'])->withWhereHas('pd', function($query){
            $query->whereNotNull('wa');
        })->get();
        foreach($absen as $a){
            $absen_masuk_pertama = ($a->absen_masuk_pertama) ? $a->absen_masuk_pertama->jam_scan : NULL;
            $absen_masuk_kedua = ($a->absen_masuk_kedua) ? $a->absen_masuk_kedua->jam_scan : NULL;
            $absen_pulang = ($a->absen_pulang) ? $a->absen_pulang->jam_scan : NULL;
            $text = [
                'body' => 'Informasi presensi Ananda *'.$a->pd->nama.'* pada tanggal '.now()->translatedFormat('d F Y')."\n".
                    'Absen Masuk Jam Pertama: '.$absen_masuk_pertama."\n".
                    'Absen Masuk Jam Kedua: '.$absen_masuk_kedua."\n".
                    'Absen Pulang: '.$absen_pulang."\n"
            ];
            $response = Http::withToken('uec645f87f1074f1.6ad989adc88542a185495a5af1796b82')->post('https://api.mas-adi.net/api/v1/messages', [
                'recipient_type' => 'individual',
                'to' => $a->pd->wa,
                'type' => 'text',
                'text' => $text
            ]);
            dump($response->status());
        }
        return Command::SUCCESS;
    }
}
