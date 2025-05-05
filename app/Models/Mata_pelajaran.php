<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mata_pelajaran extends Model
{
    use HasFactory;
    protected $table = 'mata_pelajaran';
	protected $guarded = [];
    protected $appends = ['nama_mapel'];
    public function getNamaMapelAttribute()
	{
        return '('.$this->attributes['kode'].') '.$this->attributes['nama'];
	}
}
