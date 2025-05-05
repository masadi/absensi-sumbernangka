<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nama_hari extends Model
{
    use HasFactory;
    protected $table = 'nama_hari';
	protected $guarded = [];
    
    public function jadwal_ujian()
    {
        return $this->hasMany(Jadwal_ujian::class, 'hari_id', 'id');
    }
}
