<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal';
	protected $guarded = [];
    public $appends = ['tanggal_indo'];
    public function rombongan_belajar()
    {
        return $this->hasOne(Rombongan_belajar::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function ptk()
    {
        return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id');
    }
    public function getTanggalIndoAttribute()
	{
        return Carbon::parse($this->attributes['tanggal'])->translatedFormat('d F Y');
	}
    public function jadwal_ujian()
    {
        return $this->hasMany(Jadwal_ujian::class, 'jadwal_id', 'id');
    }
    public function catatan_ujian()
    {
        return $this->hasMany(Catatan_ujian::class, 'jadwal_id', 'id');
    }
}
