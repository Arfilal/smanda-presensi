<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    private function getDataSiswa()
    {
        return Siswa::where('user_id', Auth::id())->first();
    }

    public function dashboard()
    {
        $siswa = $this->getDataSiswa();
        $sekarang = Carbon::now();

        if (! $siswa) {
            return view('portal.siswa.dashboard', [
                'siswa' => null,
                'presensiHariIni' => null,
                'jadwalPresensi' => Presensi::jadwalPresensi($sekarang),
                'sesiPresensi' => 'masuk',
                'teksStatusPresensi' => 'Belum Absen',
                'teksTombolPresensi' => 'Presensi Masuk',
                'totalHadir' => 0,
                'totalTerlambat' => 0,
                'totalDispen' => 0,
                'totalSakit' => 0,
                'totalIzin' => 0,
                'totalAlpha' => 0,
            ]);
        }

        Presensi::sinkronkanPengajuanBulanan($siswa, $sekarang);
        Presensi::sinkronkanAlpaBulanan($siswa, $sekarang, $sekarang);
        Presensi::sinkronkanHariIni($siswa, $sekarang);

        $statusPresensi = Presensi::statusBeranda($siswa, $sekarang);
        $ringkasan = Presensi::ringkasanBulanan($siswa, $sekarang);

        return view('portal.siswa.dashboard', array_merge([
            'siswa' => $siswa,
            'presensiHariIni' => $statusPresensi['presensi'],
            'jadwalPresensi' => $statusPresensi['jadwal'],
            'sesiPresensi' => $statusPresensi['sesi'],
            'teksStatusPresensi' => $statusPresensi['status_teks'],
            'teksTombolPresensi' => $statusPresensi['teks_tombol'],
        ], $ringkasan));
    }

    public function presensiForm()
    {
        $siswa = $this->getDataSiswa();
        $sekarang = Carbon::now();

        if (! $siswa) {
            return view('portal.siswa.presensi', [
                'siswa' => null,
                'jadwalPresensi' => Presensi::jadwalPresensi($sekarang),
                'sesiPresensi' => 'masuk',
                'teksStatusPresensi' => 'Belum Absen',
            ]);
        }

        Presensi::sinkronkanHariIni($siswa, $sekarang);
        $statusPresensi = Presensi::statusBeranda($siswa, $sekarang);

        return view('portal.siswa.presensi', [
            'siswa' => $siswa,
            'jadwalPresensi' => $statusPresensi['jadwal'],
            'sesiPresensi' => $statusPresensi['sesi'],
            'teksStatusPresensi' => $statusPresensi['status_teks'],
        ]);
    }

    public function storePresensi(Request $request)
    {
        $siswa = $this->getDataSiswa();
        if (! $siswa) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        $validated = $request->validate([
            'lokasi_lat' => 'required',
            'lokasi_long' => 'required',
            'tipe' => 'required|in:masuk,pulang',
            'image' => 'required',
            'device_time' => 'required',
        ]);

        $sekarang = Carbon::now();
        $jadwal = Presensi::jadwalPresensi($sekarang);

        if ($jadwal['libur']) {
            return back()->with('error', 'Hari ini adalah hari libur, presensi tidak tersedia.');
        }

        $jamBuka = Presensi::waktuPresensi($sekarang, $jadwal['jam_masuk']);
        $jamTutup = Presensi::waktuPresensi($sekarang, $jadwal['jam_pulang']);

        if ($sekarang->lessThan($jamBuka) || $sekarang->greaterThan($jamTutup)) {
            return back()->with('error', 'Presensi hanya tersedia pukul '.$jadwal['jam_masuk'].' sampai '.$jadwal['jam_pulang'].'.');
        }

        $statusPresensi = Presensi::statusBeranda($siswa, $sekarang);

        if ($validated['tipe'] !== $statusPresensi['sesi']) {
            $pesan = $statusPresensi['sesi'] === 'pulang'
                ? 'Anda sudah melakukan presensi masuk hari ini.'
                : 'Anda belum melakukan presensi masuk.';

            return back()->with('error', $pesan);
        }

        $imgParts = explode(';', $validated['image']);
        $imgBase64 = base64_decode(explode(',', $imgParts[1])[1]);
        $fileName = 'selfie_'.$siswa->id.'_'.time().'.png';
        Storage::disk('public')->put('uploads/'.$fileName, $imgBase64);

        $jamServer = $sekarang->format('H:i:s');
        $tipePresensi = $validated['tipe'];

        $hasil = DB::transaction(function () use ($siswa, $sekarang, $tipePresensi, $jamServer, $validated, $fileName, $jadwal) {
            $presensi = Presensi::whereBelongsTo($siswa)
                ->whereDate('tgl_presensi', $sekarang->toDateString())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($tipePresensi === 'masuk') {
                if (Presensi::sudahPresensiMasuk($presensi)) {
                    return ['error' => 'Anda sudah melakukan presensi masuk hari ini.'];
                }

                $dataPresensi = [
                    'siswa_id' => $siswa->id,
                    'tgl_presensi' => $sekarang->toDateString(),
                    'jam_masuk' => $jamServer,
                    'lokasi_lat' => $validated['lokasi_lat'],
                    'lokasi_long' => $validated['lokasi_long'],
                    'foto_selfie' => $fileName,
                    'status' => $sekarang->greaterThan(Presensi::waktuPresensi($sekarang, $jadwal['jam_masuk']))
                        ? Presensi::STATUS_TERLAMBAT
                        : Presensi::STATUS_HADIR,
                ];

                if ($presensi?->status === Presensi::STATUS_ALPA) {
                    $presensi->update($dataPresensi);
                } else {
                    Presensi::create($dataPresensi);
                }

                return ['success' => 'Presensi masuk berhasil direkam.'];
            }

            if (! Presensi::sudahPresensiMasuk($presensi)) {
                return ['error' => 'Anda belum melakukan presensi masuk.'];
            }

            $presensi->update(['jam_pulang' => $jamServer]);

            return ['success' => 'Presensi pulang berhasil direkam.'];
        });

        return redirect()->route('siswa.dashboard')->with(
            isset($hasil['success']) ? 'success' : 'error',
            $hasil['success'] ?? $hasil['error']
        );
    }

    public function riwayat(Request $request)
    {
        $siswa = $this->getDataSiswa();
        $periode = $request->input('periode', date('Y-m'));
        $bulan = Carbon::createFromFormat('!Y-m', $periode) ?: Carbon::now()->startOfMonth();

        $riwayats = collect();
        if ($siswa) {
            Presensi::sinkronkanPengajuanBulanan($siswa, $bulan);
            Presensi::sinkronkanAlpaBulanan($siswa, $bulan, Carbon::now());
            $riwayats = Presensi::where('siswa_id', $siswa->id)
                ->where('tgl_presensi', 'LIKE', '%'.$periode.'%')
                ->latest()
                ->get();
        }

        return view('portal.siswa.riwayat', compact('siswa', 'riwayats', 'periode'));
    }

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
        if (! $siswa) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        $request->validate([
            'jenis_izin' => 'required|in:dispen,sakit,izin',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string',
        ]);

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
