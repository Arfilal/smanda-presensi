<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'siswa_id',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'jenis_izin',
        'alasan',
        'bukti_dokumen',
        'status_approval',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}