<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Sekolah;

class SedotSekolah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sedot:sekolah {npsn}';

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
        $sync_sekolah = Http::get('http://sync.erapor-smk.net/api/v7/sekolah/'.$this->argument('npsn'));
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
            $this->error($npsn.' gagal disimpan');
        }
        return Command::SUCCESS;
    }
}
