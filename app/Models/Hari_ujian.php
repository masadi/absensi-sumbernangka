<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hari_ujian extends Model
{
    use HasFactory;
    protected $table = 'hari_ujian';
	protected $guarded = [];
    
    public function jadwal_ujian()
    {
        return $this->hasMany(Jadwal_ujian::class, 'hari_id', 'id');
    }
}
