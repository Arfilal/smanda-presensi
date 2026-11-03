<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'nisn',
        'nama_lengkap',
        'kelas',
        'jenis_kelamin',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }

    public function pengajuanIzins()
    {
        return $this->hasMany(PengajuanIzin::class, 'siswa_id');
    }

    public function rekapKbms()
    {
        return $this->hasMany(RekapKbm::class, 'siswa_id');
    }
}