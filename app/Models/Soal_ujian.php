<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal_ujian extends Model
{
    use HasFactory;
    protected $table = 'soal_ujian';
	protected $guarded = [];
    public function jadwal_ujian()
    {
        return $this->belongsTo(Jadwal_ujian::class);
    }
    public function jawaban_soal()
    {
        return $this->hasMany(Jawaban_soal::class);
    }
    public function ujian_siswa()
    {
        return $this->hasOne(Ujian_siswa::class);
    }
    public function siswa()
    {
        return $this->hasMany(Ujian_siswa::class);
    }
}
