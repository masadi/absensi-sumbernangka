<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    public $incrementing = false;
	public $keyType = 'string';
	protected $table = 'sekolah';
	protected $primaryKey = 'sekolah_id';
	protected $guarded = [];
	public function ptk()
	{
		return $this->hasMany(Ptk::class, 'sekolah_id', 'sekolah_id');
	}
	public function pd()
	{
		return $this->hasMany(Peserta_didik::class, 'sekolah_id', 'sekolah_id');
	}
	public function libur()
    {
        return $this->hasManyThrough(
            Libur::class,
            Kategori_libur::class,
            'sekolah_id', // Foreign key on the absen table...
            'kategori_id', // Foreign key on the Absen_masuk table...
            'sekolah_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
	public function kasek()
	{
		return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id');
	}
	public function pengguna()
	{
		return $this->hasMany(User::class, 'sekolah_id', 'sekolah_id');
	}
	public function rombongan_belajar()
	{
		return $this->hasMany(Rombongan_belajar::class, 'sekolah_id', 'sekolah_id');
	}
}
