<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absen_masuk extends Model
{
    use HasFactory;
    protected $table = 'absen_masuk';
	protected $guarded = [];
	public $appends = ['jam_scan'];
	public function absen(){
		return $this->hasOne(Absen::class, 'id', 'absen_id');
	}
	public function getJamScanAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('H:i:s');
    }
}
