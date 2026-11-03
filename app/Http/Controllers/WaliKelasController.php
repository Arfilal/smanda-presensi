<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MappingWaliKelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PengajuanIzin;

class WaliKelasController extends Controller
{
    // Mengambil nama kelas binaan berdasarkan user yang sedang login
    private function getKelasBinaan()
    {
        $mapping = MappingWaliKelas::where('user_id', Auth::id())->first();
        return $mapping ? $mapping->nama_kelas : null;
    }

    // 1. Dashboard Wali Kelas
    public function dashboard()
    {
        $kelas = $this->getKelasBinaan();
        
        // Jika belum dimapping ke kelas manapun, berikan nilai default kosong
        if (!$kelas) {
            return view('portal.walikelas.dashboard', [
                'kelas' => 'Belum Dimapping',
                'totalSiswa' => 0,
                'hadirHariIni' => 0,
                'izinPending' => 0,
                'alpaHariIni' => 0,
                'siswas' => collect()
            ]);
        }

        $siswas = Siswa::where('kelas', $kelas)->get();
        $siswaIds = $siswas->pluck('id');

        $totalSiswa = $siswas->count();
        $hadirHariIni = Presensi::whereIn('siswa_id', $siswaIds)->whereDate('tgl_presensi', date('Y-m-d'))->where('status', 'hadir')->count();
        $izinPending = PengajuanIzin::whereIn('siswa_id', $siswaIds)->whereIn('jenis_izin', ['izin', 'sakit'])->where('status_approval', 'Pending')->count();
        $alpaHariIni = Presensi::whereIn('siswa_id', $siswaIds)->whereDate('tgl_presensi', date('Y-m-d'))->where('status', 'alpa')->count();

        return view('portal.walikelas.dashboard', compact('kelas', 'totalSiswa', 'hadirHariIni', 'izinPending', 'alpaHariIni', 'siswas'));
    }

    // 2. Menu Approval Izin / Sakit
    public function izinSakit()
    {
        $kelas = $this->getKelasBinaan();
        $siswaIds = Siswa::where('kelas', $kelas)->pluck('id');

        // Wali kelas memvalidasi jenis izin 'izin' atau 'sakit' dari kelas binaannya
        $pengajuans = PengajuanIzin::with('siswa')
            ->whereIn('siswa_id', $siswaIds)
            ->whereIn('jenis_izin', ['izin', 'sakit'])
            ->latest()
            ->get();

        return view('portal.walikelas.izin_sakit', compact('pengajuans', 'kelas'));
    }

    // Aksi Approval Izin / Sakit
    public function updateIzin(Request $request, $id)
    {
        $request->validate([
            'status_approval' => 'required|in:Disetujui,Ditolak',
        ]);

        $izin = PengajuanIzin::findOrFail($id);
        $izin->update([
            'status_approval' => $request->status_approval,
        ]);

        return back()->with('success', 'Status pengajuan izin/sakit berhasil diperbarui.');
    }

    // 3. Menu Rekap Absen Kelas Binaan
    public function rekapKelas()
    {
        $kelas = $this->getKelasBinaan();
        $siswas = Siswa::with(['presensis' => function($q) {
            $q->whereDate('tgl_presensi', date('Y-m-d'));
        }])->where('kelas', $kelas)->get();

        return view('portal.walikelas.rekap', compact('siswas', 'kelas'));
    }
}