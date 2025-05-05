<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban_pd extends Model
{
    use HasFactory;
    protected $table = 'jawaban_pd';
	protected $guarded = [];
    public function pd()
    {
        return $this->hasOneThrough(
            Peserta_didik::class,
            Anggota_rombel::class,
            'anggota_rombel_id', // Foreign key on the cars table...
            'peserta_didik_id', // Foreign key on the owners table...
            'anggota_rombel_id', // Local key on the mechanics table...
            'peserta_didik_id' // Local key on the cars table...
        );
    }
    public function jawaban_soal()
    {
        return $this->belongsTo(Jawaban_soal::class);
    }
}
