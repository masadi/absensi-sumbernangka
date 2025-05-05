<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Ujian_siswa extends Model
{
    use HasFactory;
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'ujian_siswa';
	protected $primaryKey = 'ujian_siswa_id';
	protected $guarded = [];
}
