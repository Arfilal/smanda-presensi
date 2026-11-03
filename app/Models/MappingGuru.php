<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingGuru extends Model
{
    use HasFactory;

    protected $table = 'mapping_guru';

    protected $fillable = [
        'user_id',
        'nama_kelas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}