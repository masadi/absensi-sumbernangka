<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peserta_didik;
use Illuminate\Support\Facades\Http;

class BcPesan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bc:pesan';

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
        $text = "Assalamu'alaikum wr wb.\n\nKepada yth: \nBapak/Ibu Wali Siswa/i SMP Al Falah Sumber Gayam. \nDiinformasikan bahwa libur pasca ujian semester hanya pada hari *Minggu, 22 Desember 2024*\nDan untuk selanjutnya SMP Al Falah tetap masuk seperti biasa karena ada beberapa agenda kegiatan sekolah yang harus diikuti oleh siswa-siswi. \nDemikian informasi ini disampaikan, atas kerjasama dan dukungannya kami sampaikan terimakasih\n\nWassalamualaikum wr wb";
        $text = "Assalamu'alaikum wr wb.\n\nKepada yth:\nBapak/Ibu Wali Siswa/i SMP Al Falah Sumber Gayam.\nDiinformasikan bahwa Libur Tahun Baru dimulai pada hari Senin, 30 Desember 2024 s.d Rabu, 01 Januari 2025\nDan aktif kembali pada hari Kamis, 02 Januari 2025\n\nDemikian informasi ini disampaikan, atas kerjasama dan dukungannya kami ucapkan terimakasih\n\nWassalamualaikum wr wb";
        /*$response = Http::withToken('uec645f87f1074f1.6ad989adc88542a185495a5af1796b82')->post('https://api.mas-adi.net/api/v1/messages', [
            'recipient_type' => 'individual',
            'to' => '6287872729285',
            'type' => 'text',
            'text' => [
                'body' => $text
            ],
        ]);
        dump($response->status());*/
        $data = Peserta_didik::whereNotNull('wa')->whereHas('anggota_rombel', function($query){
            $query->where('semester_id', 20241);
        })->orderBy('nama')->get();
        foreach($data as $d){
            $response = Http::withToken('uec645f87f1074f1.6ad989adc88542a185495a5af1796b82')->post('https://api.mas-adi.net/api/v1/messages', [
                'recipient_type' => 'individual',
                'to' => $d->wa,
                'type' => 'text',
                'text' => [
                    'body' => $text
                ],
            ]);
            dump($response->status());
        }
        return Command::SUCCESS;
    }
}
