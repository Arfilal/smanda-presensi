<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    private function getDataSiswa()
    {
        return Siswa::where('user_id', Auth::id())->first();
    }

    // 1. Dashboard Siswa (Ref: Gambar 4.2)
    public function dashboard()
    {
        $siswa = $this->getDataSiswa();
        if (!$siswa) {
            return view('portal.siswa.dashboard', ['siswa' => null]);
        }

        $bulanIni = date('m');
        $tahunIni = date('Y');

        $totalHadir = Presensi::where('siswa_id', $siswa->id)->whereMonth('tgl_presensi', $bulanIni)->whereYear('tgl_presensi', $tahunIni)->where('status', 'hadir')->count();
        $totalTerlambat = Presensi::where('siswa_id', $siswa->id)->whereMonth('tgl_presensi', $bulanIni)->whereYear('tgl_presensi', $tahunIni)->where('status', 'terlambat')->count();
        $totalDispen = PengajuanIzin::where('siswa_id', $siswa->id)->where('jenis_izin', 'dispen')->where('status_approval', 'Disetujui')->count();
        $totalSakit = PengajuanIzin::where('siswa_id', $siswa->id)->where('jenis_izin', 'sakit')->where('status_approval', 'Disetujui')->count();
        $totalIzin = PengajuanIzin::where('siswa_id', $siswa->id)->where('jenis_izin', 'izin')->where('status_approval', 'Disetujui')->count();
        $totalAlpha = Presensi::where('siswa_id', $siswa->id)->whereMonth('tgl_presensi', $bulanIni)->whereYear('tgl_presensi', $tahunIni)->where('status', 'alpa')->count();

        $presensiHariIni = Presensi::where('siswa_id', $siswa->id)->whereDate('tgl_presensi', date('Y-m-d'))->first();

        return view('portal.siswa.dashboard', compact('siswa', 'totalHadir', 'totalTerlambat', 'totalDispen', 'totalSakit', 'totalIzin', 'totalAlpha', 'presensiHariIni'));
    }

    // 2. Form Presensi Kamera Depan & GPS (Ref: Gambar 4.3)
    public function presensiForm()
    {
        $siswa = $this->getDataSiswa();
        return view('portal.siswa.presensi', compact('siswa'));
    }

    public function storePresensi(Request $request)
    {
        $siswa = $this->getDataSiswa();
        if (!$siswa) return back()->with('error', 'Siswa tidak ditemukan.');

        $request->validate([
            'lokasi_lat' => 'required',
            'lokasi_long' => 'required',
            'tipe' => 'required|in:masuk,pulang',
            'image' => 'required',
            'device_time' => 'required', // Jam dari device siswa
        ]);

        $tgl = date('Y-m-d');
        $jamDevice = $request->device_time; // Menggunakan jam sesuai device masing-masing

        // Simpan gambar selfie ke storage public/uploads
        $imgParts = explode(';', $request->image);
        $imgBase64 = base64_decode(explode(',', $imgParts[1])[1]);
        $fileName = 'selfie_' . $siswa->id . '_' . time() . '.png';
        Storage::disk('public')->put('uploads/' . $fileName, $imgBase64);

        if ($request->tipe == 'masuk') {
            $cek = Presensi::where('siswa_id', $siswa->id)->whereDate('tgl_presensi', $tgl)->first();
            if ($cek) return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');

            Presensi::create([
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $tgl,
                'jam_masuk' => $jamDevice, // Waktu sinkron dengan device
                'lokasi_lat' => $request->lokasi_lat,
                'lokasi_long' => $request->lokasi_long,
                'foto_selfie' => $fileName,
                'status' => 'hadir',
            ]);
            return redirect()->route('siswa.dashboard')->with('success', 'Presensi masuk berhasil direkam.');
        } else {
            $presensi = Presensi::where('siswa_id', $siswa->id)->whereDate('tgl_presensi', $tgl)->first();
            if (!$presensi) return back()->with('error', 'Anda belum melakukan presensi masuk.');

            $presensi->update(['jam_pulang' => $jamDevice]); // Waktu pulang sinkron dengan device
            return redirect()->route('siswa.dashboard')->with('success', 'Presensi pulang berhasil direkam.');
        }
    }

    // 3. Histori & Riwayat Absensi (Ref: Gambar 4.4 & 4.5)
    public function riwayat(Request $request)
    {
        $siswa = $this->getDataSiswa();
        $periode = $request->input('periode', date('Y-m'));

        $riwayats = collect();
        if ($siswa) {
            $riwayats = Presensi::where('siswa_id', $siswa->id)
                ->where('tgl_presensi', 'LIKE', '%' . $periode . '%')
                ->latest()
                ->get();
        }

        return view('portal.siswa.riwayat', compact('siswa', 'riwayats', 'periode'));
    }

    // 4. Form Pengajuan Izin / Sakit / Dispen (Ref: Gambar 4.6)
    public function izinForm()
    {
        $siswa = $this->getDataSiswa();
        $pengajuans = collect();
        if ($siswa) {
            $pengajuans = PengajuanIzin::where('siswa_id', $siswa->id)->latest()->get();
        }
        return view('portal.siswa.izin', compact('siswa', 'pengajuans'));
    }

    public function storeIzin(Request $request)
    {
        $siswa = $this->getDataSiswa();
        if (!$siswa) return back()->with('error', 'Siswa tidak ditemukan.');

        $request->validate([
            'jenis_izin' => 'required|in:dispen,sakit,izin',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string',
        ]);

        // Catatan Alur: 
        // Jika jenis_izin == 'dispen' -> otomatis masuk ke menu Approval Kesiswaan
        // Jika jenis_izin == 'izin' atau 'sakit' -> otomatis masuk ke menu Approval Wali Kelas
        PengajuanIzin::create([
            'siswa_id' => $siswa->id,
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'jenis_izin' => $request->jenis_izin,
            'alasan' => $request->alasan,
            'status_approval' => 'Pending',
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim (Dispen ke Kesiswaan, Izin/Sakit ke Wali Kelas).');
    }

    // 5. Profil Siswa (Ref: Gambar 4.7)
    public function profile()
    {
        $siswa = $this->getDataSiswa();
        return view('portal.siswa.profile', compact('siswa'));
    }

    public function detailPresensi($id)
    {
        $siswa = $this->getDataSiswa();
        $presensi = Presensi::where('id', $id)->where('siswa_id', $siswa->id)->firstOrFail();

        return view('portal.siswa.detail_presensi', compact('siswa', 'presensi'));
    }
}