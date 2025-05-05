<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jadwal_ujian extends Model
{
    use HasFactory;
    protected $table = 'jadwal_ujian';
	protected $guarded = [];
    public $appends = ['tanggal_indo'];
    
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
    public function rombongan_belajar()
    {
        return $this->hasOne(Rombongan_belajar::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function mata_pelajaran()
    {
        return $this->hasOne(Mata_pelajaran::class, 'id', 'mata_pelajaran_id');
    }
    public function hari()
    {
        return $this->hasOne(Hari_ujian::class, 'id', 'hari_id');
    }
    public function soal_ujian()
    {
        return $this->hasOne(Soal_ujian::class);
    }
    public function getTanggalIndoAttribute()
	{
        return ($this->attributes['tanggal']) ? Carbon::parse($this->attributes['tanggal'])->translatedFormat('d F Y') : Carbon::now()->translatedFormat('d F Y');
	}
}
