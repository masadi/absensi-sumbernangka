<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban_soal extends Model
{
    use HasFactory;
    protected $table = 'jawaban_soal';
	protected $guarded = [];
    public function jawaban_pd()
    {
        return $this->hasOne(Jawaban_pd::class);
    }
    public function jawaban_benar()
    {
        return $this->hasManyThrough(
            Anggota_rombel::class,
            Jawaban_pd::class,
            'jawaban_soal_id', // Foreign key on the environments table...
            'anggota_rombel_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'anggota_rombel_id' // Local key on the environments table...
        );
    }
    public function jawaban_salah()
    {
        return $this->hasManyThrough(
            Anggota_rombel::class,
            Jawaban_pd::class,
            'jawaban_soal_id', // Foreign key on the environments table...
            'anggota_rombel_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'anggota_rombel_id' // Local key on the environments table...
        );
    }
    public function soal_ujian()
    {
        return $this->belongsTo(Soal_ujian::class);
    }
}
