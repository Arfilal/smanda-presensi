<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke Siswa (jika role siswa)
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    // Relasi ke Mapping Guru Mapel
    public function mappingGuru()
    {
        return $this->hasMany(MappingGuru::class, 'user_id');
    }

    // Relasi ke Mapping Wali Kelas
    public function mappingWaliKelas()
    {
        return $this->hasMany(MappingWaliKelas::class, 'user_id');
    }
}