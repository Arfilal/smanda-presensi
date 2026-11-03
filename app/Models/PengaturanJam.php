<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanJam extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_jam';

    protected $fillable = [
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'keterangan',
    ];
}