<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MappingGuru;
use App\Models\Siswa;
use App\Models\RekapKbm;

class GuruMapelController extends Controller
{
    // Mengambil daftar kelas yang diampu oleh guru yang sedang login
    private function getKelasDiampu()
    {
        return MappingGuru::where('user_id', Auth::id())->pluck('nama_kelas');
    }

    // 1. Dashboard Guru Mapel
    public function dashboard()
    {
        $kelasDiampu = $this->getKelasDiampu();
        $totalKelas = $kelasDiampu->count();
        
        // Total siswa di seluruh kelas yang diampu guru ini
        $totalSiswa = Siswa::whereIn('kelas', $kelasDiampu)->count();
        
        // Total verifikasi KBM hari ini
        $totalVerifikasi = RekapKbm::where('user_id', Auth::id())
            ->whereDate('tanggal', date('Y-m-d'))
            ->count();

        return view('portal.guru.dashboard', compact('totalKelas', 'totalSiswa', 'totalVerifikasi', 'kelasDiampu'));
    }

    // 2. Menu Verifikasi KBM (Fisik Siswa)
    public function verifikasiKbm(Request $request)
    {
        $kelasDiampu = $this->getKelasDiampu();
        
        // Ambil kelas yang dipilih dari filter, atau default ke kelas pertama
        $kelasPilihan = $request->input('kelas', $kelasDiampu->first());

        $siswas = collect();
        if ($kelasPilihan) {
            $siswas = Siswa::with(['user', 'presensis' => function($q) {
                $q->whereDate('tgl_presensi', date('Y-m-d'));
            }, 'rekapKbms' => function($q) use ($kelasPilihan) {
                $q->whereDate('tanggal', date('Y-m-d'))->where('kelas', $kelasPilihan);
            }])->where('kelas', $kelasPilihan)->get();
        }

        return view('portal.guru.verifikasi_kbm', compact('kelasDiampu', 'kelasPilihan', 'siswas'));
    }

    // Aksi Menyimpan Status Fisik KBM Siswa
    public function storeVerifikasiKbm(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'kelas' => 'required|string',
            'status_fisik' => 'required|in:hadir,bolos,hadir_manual',
        ]);

        RekapKbm::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'siswa_id' => $request->siswa_id,
                'tanggal' => date('Y-m-d'),
                'kelas' => $request->kelas,
            ],
            [
                'status_fisik' => $request->status_fisik,
            ]
        );

        return back()->with('success', 'Status verifikasi fisik KBM berhasil disimpan.');
    }
}