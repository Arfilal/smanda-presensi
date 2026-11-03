<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Operator
        User::create([
            'name' => 'Admin Operator',
            'email' => 'operator@smanda.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);

        // 2. Akun Kesiswaan
        User::create([
            'name' => 'Bapak Kesiswaan',
            'email' => 'kesiswaan@smanda.com',
            'password' => Hash::make('password'),
            'role' => 'kesiswaan',
        ]);

        // 3. Akun Wali Kelas
        User::create([
            'name' => 'Siti Aminah, M.Pd',
            'email' => 'sitiaminah@smanda.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
        ]);

        // 4. Akun Guru Mapel
        User::create([
            'name' => 'Ahmad Fauzi, S.T',
            'email' => 'ahmadfauzi@smanda.com',
            'password' => Hash::make('password'),
            'role' => 'guru_mapel',
        ]);

        // 5. Akun Siswa
        $userSiswa = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@smanda.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        Siswa::create([
            'user_id' => $userSiswa->id,
            'nisn' => '0011223301',
            'nama_lengkap' => 'Andi Pratama',
            'kelas' => 'X MIPA 1',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
        ]);
    }
}