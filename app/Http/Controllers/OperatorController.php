<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\MappingGuru;
use App\Models\MappingWaliKelas;
use App\Models\PengaturanJam;
use App\Models\HariLibur;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    // 1. Dashboard Operator
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalSiswa = Siswa::count();
        $totalGuruMapel = MappingGuru::count();
        $totalRombel = Siswa::distinct('kelas')->count('kelas');
        $logs = MappingGuru::with('user')->latest()->take(5)->get();

        return view('portal.operator.dashboard', compact('totalUsers', 'totalSiswa', 'totalGuruMapel', 'totalRombel', 'logs'));
    }

    // 2. Master Data Users
    public function users()
    {
        $users = User::all();
        return view('portal.operator.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:siswa,guru_mapel,wali_kelas,kesiswaan,operator',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    // 3. Master Data Siswa
    public function siswa()
    {
        $siswas = Siswa::with('user')->get();
        return view('portal.operator.siswa', compact('siswas'));
    }

    // 4. Mapping Guru Mapel
    public function mappingGuru()
    {
        $gurus = User::where('role', 'guru_mapel')->get();
        $mappings = MappingGuru::with('user')->get();
        return view('portal.operator.mapping_guru', compact('gurus', 'mappings'));
    }

    public function storeMappingGuru(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_kelas' => 'required|string',
        ]);

        MappingGuru::create($request->all());
        return back()->with('success', 'Relasi Guru Mapel berhasil ditambahkan.');
    }

    // 5. Mapping Wali Kelas
    public function mappingWali()
    {
        $walikelas = User::where('role', 'wali_kelas')->get();
        $mappings = MappingWaliKelas::with('user')->get();
        return view('portal.operator.mapping_wali', compact('walikelas', 'mappings'));
    }

    public function storeMappingWali(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_kelas' => 'required|string|unique:mapping_wali_kelas,nama_kelas',
        ]);

        MappingWaliKelas::create($request->all());
        return back()->with('success', 'Relasi Wali Kelas berhasil ditambahkan.');
    }

    // 6. Jadwal Presensi Khusus
    public function jadwal()
    {
        $jadwals = PengaturanJam::all();
        return view('portal.operator.jadwal', compact('jadwals'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        PengaturanJam::create($request->all());
        return back()->with('success', 'Jadwal khusus berhasil disimpan.');
    }

    // 7. Hari Libur
    public function hariLibur()
    {
        $liburs = HariLibur::all();
        return view('portal.operator.hari_libur', compact('liburs'));
    }

    public function storeHariLibur(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs',
            'keterangan' => 'required|string|max:50',
        ]);

        HariLibur::create($request->all());
        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }
}