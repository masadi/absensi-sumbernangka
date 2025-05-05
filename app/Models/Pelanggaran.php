<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pelanggaran extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran';
	protected $guarded = [];
    protected $appends = ['show_tanggal'];
    public function anggota_rombel()
    {
        return $this->hasOne(Anggota_rombel::class, 'anggota_rombel_id', 'anggota_rombel_id');
    }
    public function getShowTanggalAttribute(){
        return Carbon::createFromTimeStamp(strtotime($this->tanggal))->format('d/m/Y');
    }
    public function pd(){
        return $this->hasOneThrough(
            Peserta_didik::class,
            Anggota_rombel::class,
            'anggota_rombel_id', // Foreign key on the cars table...
            'peserta_didik_id', // Foreign key on the owners table...
            'anggota_rombel_id', // Local key on the mechanics table...
            'peserta_didik_id' // Local key on the cars table...
        );
    }
    public function kelas(){
        return $this->hasOneThrough(
            Rombongan_belajar::class,
            Anggota_rombel::class,
            'anggota_rombel_id', // Foreign key on the cars table...
            'rombongan_belajar_id', // Foreign key on the owners table...
            'anggota_rombel_id', // Local key on the mechanics table...
            'rombongan_belajar_id' // Local key on the cars table...
        );
    }
    public function petugas(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
