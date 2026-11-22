<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class KesiswaanController extends Controller
{
    // 1. Dashboard Kesiswaan
    public function dashboard()
    {
        $totalSiswa = Siswa::count();
        $totalHadirHariIni = Presensi::whereDate('tgl_presensi', date('Y-m-d'))->where('status', 'hadir')->count();
        $totalDispenPending = PengajuanIzin::where('jenis_izin', 'dispen')->where('status_approval', 'Pending')->count();
        $totalAlpha = Presensi::whereDate('tgl_presensi', date('Y-m-d'))->where('status', 'alpa')->count();

        $recentDispen = PengajuanIzin::with('siswa')->where('jenis_izin', 'dispen')->latest()->take(5)->get();

        return view('portal.kesiswaan.dashboard', compact('totalSiswa', 'totalHadirHariIni', 'totalDispenPending', 'totalAlpha', 'recentDispen'));
    }

    // 2. Menu Approval Dispensasi (Dispen)
    public function dispen()
    {
        // Kesiswaan khusus menangani pengajuan dengan jenis 'dispen'
        $pengajuans = PengajuanIzin::with('siswa')->where('jenis_izin', 'dispen')->latest()->get();

        return view('portal.kesiswaan.dispen', compact('pengajuans'));
    }

    // Aksi Approval Dispen (Setujui / Tolak)
    public function updateDispen(Request $request, $id)
    {
        $validated = $request->validate([
            'status_approval' => 'required|in:Disetujui,Ditolak',
        ]);

        $izin = PengajuanIzin::findOrFail($id);
        $izin->update($validated);
        Presensi::sinkronkanPengajuan($izin->fresh());

        return back()->with('success', 'Status pengajuan dispensasi berhasil diperbarui.');
    }

    // 3. Menu Monitoring & Rekapitulasi Presensi
    public function monitoring()
    {
        $presensis = Presensi::with('siswa')->latest()->take(50)->get();

        return view('portal.kesiswaan.monitoring', compact('presensis'));
    }
}
