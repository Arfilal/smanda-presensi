<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapKbm extends Model
{
    use HasFactory;

    protected $table = 'rekap_kbm';

    protected $fillable = [
        'user_id',
        'siswa_id',
        'tanggal',
        'kelas',
        'status_fisik',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}