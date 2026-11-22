<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'siswa_id',
        'tgl_presensi',
        'jam_masuk',
        'jam_pulang',
        'lokasi_lat',
        'lokasi_long',
        'foto_selfie',
        'status',
    ];

    public const STATUS_HADIR = 'hadir';

    public const STATUS_TERLAMBAT = 'terlambat';

    public const STATUS_PULANG_CEPAT = 'pulang_cepat';

    public const STATUS_SAKIT = 'sakit';

    public const STATUS_IZIN = 'izin';

    public const STATUS_DISPEN = 'dispen';

    public const STATUS_ALPA = 'alpa';

    public const JAM_MASUK_DEFAULT = '06:00';

    public const JAM_PULANG_DEFAULT = '16:00';

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public static function jadwalPresensi(CarbonInterface $tanggal): array
    {
        $tanggalString = $tanggal->toDateString();
        $jadwalKhusus = PengaturanJam::whereDate('tanggal', $tanggalString)->first();

        if ($jadwalKhusus) {
            return [
                'jam_masuk' => Carbon::parse($jadwalKhusus->jam_masuk)->format('H:i'),
                'jam_pulang' => Carbon::parse($jadwalKhusus->jam_pulang)->format('H:i'),
                'libur' => false,
                'keterangan' => $jadwalKhusus->keterangan,
            ];
        }

        $hariLibur = HariLibur::whereDate('tanggal', $tanggalString)->first();
        $akhirPekan = $tanggal->isWeekend();

        return [
            'jam_masuk' => self::JAM_MASUK_DEFAULT,
            'jam_pulang' => self::JAM_PULANG_DEFAULT,
            'libur' => $hariLibur !== null || $akhirPekan,
            'keterangan' => $hariLibur?->keterangan ?? ($akhirPekan ? 'Sabtu / Minggu' : null),
        ];
    }

    public static function waktuPresensi(CarbonInterface $tanggal, string $jam): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', $tanggal->toDateString().' '.$jam);
    }

    public static function presensiTanggal(Siswa $siswa, CarbonInterface $tanggal): ?Presensi
    {
        return self::whereBelongsTo($siswa)
            ->whereDate('tgl_presensi', $tanggal->toDateString())
            ->latest('id')
            ->first();
    }

    public static function sudahPresensiMasuk(?Presensi $presensi): bool
    {
        return $presensi !== null
            && filled($presensi->jam_masuk)
            && in_array($presensi->status, [self::STATUS_HADIR, self::STATUS_TERLAMBAT, self::STATUS_PULANG_CEPAT], true);
    }

    public static function statusBeranda(Siswa $siswa, CarbonInterface $sekarang): array
    {
        $jadwal = self::jadwalPresensi($sekarang);
        $presensi = self::presensiTanggal($siswa, $sekarang);
        $sudahMasuk = self::sudahPresensiMasuk($presensi);
        $sudahPulang = $sudahMasuk && filled($presensi->jam_pulang);

        if ($jadwal['libur']) {
            return [
                'jadwal' => $jadwal,
                'presensi' => $presensi,
                'sesi' => 'libur',
                'status_teks' => 'Hari Libur'.($jadwal['keterangan'] ? ' - '.$jadwal['keterangan'] : ''),
                'teks_tombol' => 'Hari Libur',
            ];
        }

        if (self::sedangIzin($presensi)) {
            return [
                'jadwal' => $jadwal,
                'presensi' => $presensi,
                'sesi' => 'izin',
                'status_teks' => strtoupper($presensi->status).' Disetujui',
                'teks_tombol' => 'Sedang Izin',
            ];
        }

        if ($sudahPulang) {
            return [
                'jadwal' => $jadwal,
                'presensi' => $presensi,
                'sesi' => 'selesai',
                'status_teks' => 'Sudah Presensi Pulang ('.$presensi->jam_pulang.')',
                'teks_tombol' => 'Presensi Selesai',
            ];
        }

        if ($sudahMasuk) {
            return [
                'jadwal' => $jadwal,
                'presensi' => $presensi,
                'sesi' => 'pulang',
                'status_teks' => 'Sudah Presensi Masuk ('.$presensi->jam_masuk.')',
                'teks_tombol' => 'Presensi Pulang',
            ];
        }

        $lewatiJadwal = $sekarang->greaterThanOrEqualTo(self::waktuPresensi($sekarang, $jadwal['jam_pulang']));

        return [
            'jadwal' => $jadwal,
            'presensi' => $presensi,
            'sesi' => 'masuk',
            'status_teks' => $presensi?->status === self::STATUS_ALPA && $lewatiJadwal ? 'Alpa' : 'Belum Absen',
            'teks_tombol' => 'Presensi Masuk',
        ];
    }

    public static function sinkronkanHariIni(Siswa $siswa, CarbonInterface $sekarang): ?Presensi
    {
        $jadwal = self::jadwalPresensi($sekarang);
        $presensi = self::presensiTanggal($siswa, $sekarang);

        if ($jadwal['libur']) {
            self::hapusAlpa($presensi);

            return null;
        }

        $pengajuan = PengajuanIzin::whereBelongsTo($siswa)
            ->where('status_approval', 'Disetujui')
            ->whereDate('tgl_mulai', '<=', $sekarang->toDateString())
            ->whereDate('tgl_selesai', '>=', $sekarang->toDateString())
            ->latest('id')
            ->first();

        if ($pengajuan) {
            self::sinkronkanPengajuan($pengajuan);

            return self::presensiTanggal($siswa, $sekarang);
        }

        if ($sekarang->lessThan(self::waktuPresensi($sekarang, $jadwal['jam_pulang']))) {
            self::hapusAlpa($presensi);

            return self::presensiTanggal($siswa, $sekarang);
        }

        if ($presensi === null) {
            return self::create([
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $sekarang->toDateString(),
                'status' => self::STATUS_ALPA,
            ]);
        }

        return $presensi;
    }

    public static function sinkronkanPengajuan(PengajuanIzin $pengajuan): void
    {
        $statusIzin = self::statusIzin($pengajuan->jenis_izin);

        if ($statusIzin === null) {
            return;
        }

        if ($pengajuan->status_approval !== 'Disetujui') {
            self::hapusStatusIzin($pengajuan, $statusIzin);

            return;
        }

        $siswa = $pengajuan->siswa;
        $mulai = Carbon::parse($pengajuan->tgl_mulai)->startOfDay();
        $selesai = Carbon::parse($pengajuan->tgl_selesai)->startOfDay();

        for ($tanggal = $mulai->copy(); $tanggal->lessThanOrEqualTo($selesai); $tanggal->addDay()) {
            if (self::jadwalPresensi($tanggal)['libur']) {
                continue;
            }

            $presensi = self::presensiTanggal($siswa, $tanggal);

            if ($presensi === null) {
                self::create([
                    'siswa_id' => $siswa->id,
                    'tgl_presensi' => $tanggal->toDateString(),
                    'status' => $statusIzin,
                ]);

                continue;
            }

            if (self::sudahPresensiMasuk($presensi) || $presensi->status === $statusIzin) {
                continue;
            }

            $presensi->update([
                'status' => $statusIzin,
                'jam_masuk' => null,
                'jam_pulang' => null,
            ]);
        }
    }

    public static function sinkronkanPengajuanBulanan(Siswa $siswa, CarbonInterface $bulan): void
    {
        self::pengajuanDisetujuiBulanan($siswa, $bulan)
            ->get()
            ->each(fn (PengajuanIzin $pengajuan) => self::sinkronkanPengajuan($pengajuan));
    }

    public static function sinkronkanAlpaBulanan(Siswa $siswa, CarbonInterface $bulan, ?CarbonInterface $sekarang = null): void
    {
        $sekarang ??= Carbon::now();
        $awal = $bulan->copy()->startOfMonth();

        if ($siswa->created_at) {
            $awal = $awal->max($siswa->created_at->copy()->startOfDay());
        }

        $akhir = $bulan->copy()->endOfMonth()->min($sekarang->copy()->subDay());

        if ($awal->greaterThan($akhir)) {
            return;
        }

        $pengajuans = self::pengajuanDisetujuiBulanan($siswa, $bulan)
            ->get(['tgl_mulai', 'tgl_selesai']);

        for ($tanggal = $awal->copy(); $tanggal->lessThanOrEqualTo($akhir); $tanggal->addDay()) {
            $tanggalString = $tanggal->toDateString();

            if (self::jadwalPresensi($tanggal)['libur']) {
                continue;
            }

            if (self::presensiTanggal($siswa, $tanggal) !== null) {
                continue;
            }

            $sedangIzin = $pengajuans->contains(
                fn (PengajuanIzin $pengajuan) => $tanggalString >= $pengajuan->tgl_mulai
                    && $tanggalString <= $pengajuan->tgl_selesai
            );

            if ($sedangIzin) {
                continue;
            }

            self::create([
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $tanggalString,
                'status' => self::STATUS_ALPA,
            ]);
        }
    }

    public static function ringkasanBulanan(Siswa $siswa, CarbonInterface $bulan): array
    {
        $awal = $bulan->copy()->startOfMonth()->toDateString();
        $akhir = $bulan->copy()->endOfMonth()->toDateString();

        $statusPresensi = self::whereBelongsTo($siswa)
            ->whereBetween('tgl_presensi', [$awal, $akhir])
            ->get(['status'])
            ->countBy('status');

        $jenisIzin = self::pengajuanDisetujuiBulanan($siswa, $bulan)
            ->get(['jenis_izin'])
            ->countBy('jenis_izin');

        return [
            'totalHadir' => $statusPresensi->get(self::STATUS_HADIR, 0),
            'totalTerlambat' => $statusPresensi->get(self::STATUS_TERLAMBAT, 0),
            'totalDispen' => $jenisIzin->get('dispen', 0),
            'totalSakit' => $jenisIzin->get('sakit', 0),
            'totalIzin' => $jenisIzin->get('izin', 0),
            'totalAlpha' => $statusPresensi->get(self::STATUS_ALPA, 0),
        ];
    }

    private static function pengajuanDisetujuiBulanan(Siswa $siswa, CarbonInterface $bulan): Builder
    {
        $awal = $bulan->copy()->startOfMonth()->toDateString();
        $akhir = $bulan->copy()->endOfMonth()->toDateString();

        return PengajuanIzin::whereBelongsTo($siswa)
            ->where('status_approval', 'Disetujui')
            ->where(function (Builder $query) use ($awal, $akhir) {
                $query->whereBetween('tgl_mulai', [$awal, $akhir])
                    ->orWhereBetween('tgl_selesai', [$awal, $akhir])
                    ->orWhere(function (Builder $query) use ($awal, $akhir) {
                        $query->where('tgl_mulai', '<', $awal)
                            ->where('tgl_selesai', '>', $akhir);
                    });
            });
    }

    private static function statusIzin(string $jenisIzin): ?string
    {
        return match ($jenisIzin) {
            'dispen' => self::STATUS_DISPEN,
            'sakit' => self::STATUS_SAKIT,
            'izin' => self::STATUS_IZIN,
            default => null,
        };
    }

    private static function sedangIzin(?Presensi $presensi): bool
    {
        return $presensi !== null
            && ! $presensi->jam_masuk
            && in_array($presensi->status, [self::STATUS_DISPEN, self::STATUS_SAKIT, self::STATUS_IZIN], true);
    }

    private static function hapusAlpa(?Presensi $presensi): void
    {
        if ($presensi?->status === self::STATUS_ALPA && ! $presensi->jam_masuk) {
            $presensi->delete();
        }
    }

    private static function hapusStatusIzin(PengajuanIzin $pengajuan, string $statusIzin): void
    {
        $siswa = $pengajuan->siswa;
        $mulai = Carbon::parse($pengajuan->tgl_mulai)->startOfDay();
        $selesai = Carbon::parse($pengajuan->tgl_selesai)->startOfDay();

        for ($tanggal = $mulai->copy(); $tanggal->lessThanOrEqualTo($selesai); $tanggal->addDay()) {
            $presensi = self::presensiTanggal($siswa, $tanggal);

            if ($presensi?->status === $statusIzin && ! $presensi->jam_masuk) {
                $presensi->delete();
            }
        }
    }
}
