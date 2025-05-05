<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Carbon\Carbon;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function ptk()
    {
        return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id')->where('jenis_ptk_id', '<>', 0);
    }
    public function pd()
    {
        return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
    }
    public function staf()
    {
        return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id')->where('jenis_ptk_id', 0);
    }
    public function sekolah()
    {
        return $this->hasOne(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
    public function getLastLoginAtAttribute($date)
    {
        return ($date) ? Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y H:i:s') : '';
    }
    public function getLoginTerakhirAttribute()
	{
        if($this->attributes['last_login_at']){
            return Carbon::parse($this->attributes['last_login_at'])->translatedFormat('d F Y').' Pukul '.Carbon::parse($this->attributes['last_login_at'])->format('H:i:s');
        } else {
            return '-';
        }
	}
    public function getStatusPasswordAttribute(){
        return (Hash::check($this->attributes['default_password'], $this->attributes['password'])) ? $this->attributes['default_password'] : 'custom';
    }
    public function abilities(){
        return $this->hasManyThrough(
            Ability::class,
            Role_user::class,
            'user_id', // Foreign key on the environments table...
            'role_id', // Foreign key on the deployments table...
            'user_id', // Local key on the projects table...
            'role_id' // Local key on the environments table...
        );
    }
    public function ujian_siswa()
    {
        return $this->belongsTo(Ujian_siswa::class, 'id', 'user_id');
    }
}
