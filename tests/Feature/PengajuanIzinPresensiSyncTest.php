<?php

namespace Tests\Feature;

use App\Models\MappingWaliKelas;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PengajuanIzinPresensiSyncTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_persetujuan_dispen_oleh_kesiswaan_memperbarui_kartu_beranda(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$siswaUser, $siswa] = $this->buatSiswa();
        $pengajuan = $this->buatPengajuan($siswa, 'dispen');
        $kesiswaan = $this->buatUser('kesiswaan');

        $this->actingAs($kesiswaan)->post(route('kesiswaan.dispen.update', $pengajuan->id), [
            'status_approval' => 'Disetujui',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengajuan_izin', [
            'id' => $pengajuan->id,
            'status_approval' => 'Disetujui',
        ]);
        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => 'dispen',
        ]);

        $this->actingAs($siswaUser)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalDispen', 1)
            ->assertViewHas('totalAlpha', 0)
            ->assertViewHas('sesiPresensi', 'izin')
            ->assertViewHas('teksStatusPresensi', 'DISPEN Disetujui');
    }

    #[DataProvider('jenisIzinWaliKelas')]
    public function test_persetujuan_izin_oleh_wali_kelas_memperbarui_kartu_beranda(string $jenisIzin, int $totalSakit, int $totalIzin): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$siswaUser, $siswa] = $this->buatSiswa();
        $pengajuan = $this->buatPengajuan($siswa, $jenisIzin);
        $waliKelas = $this->buatUser('wali_kelas');
        MappingWaliKelas::create([
            'user_id' => $waliKelas->id,
            'nama_kelas' => 'X-1',
        ]);

        $this->actingAs($waliKelas)->post(route('walikelas.izin.update', $pengajuan->id), [
            'status_approval' => 'Disetujui',
        ])->assertRedirect();

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => $jenisIzin,
        ]);

        $this->actingAs($siswaUser)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalSakit', $totalSakit)
            ->assertViewHas('totalIzin', $totalIzin)
            ->assertViewHas('totalAlpha', 0)
            ->assertViewHas('sesiPresensi', 'izin');
    }

    public function test_penolakan_pengajuan_menghapus_status_izin_dari_presensi(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$siswaUser, $siswa] = $this->buatSiswa();
        $pengajuan = $this->buatPengajuan($siswa, 'sakit', 'Disetujui');
        Presensi::create([
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => 'sakit',
        ]);
        $waliKelas = $this->buatUser('wali_kelas');
        MappingWaliKelas::create([
            'user_id' => $waliKelas->id,
            'nama_kelas' => 'X-1',
        ]);

        $this->actingAs($waliKelas)->post(route('walikelas.izin.update', $pengajuan->id), [
            'status_approval' => 'Ditolak',
        ])->assertRedirect();

        $this->assertDatabaseMissing('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => 'sakit',
        ]);
    }

    public function test_pengajuan_disetujui_yang_terlintas_bulan_tidak_dihitung_ulang(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$siswaUser, $siswa] = $this->buatSiswa();
        $this->buatPengajuan($siswa, 'izin', 'Disetujui', '2026-10-20', '2026-10-22');

        $this->actingAs($siswaUser)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalIzin', 0)
            ->assertViewHas('totalAlpha', 0);
    }

    public function test_backfill_alpa_tidak_menimpa_tanggal_izin_yang_disetujui(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$siswaUser, $siswa] = $this->buatSiswa();
        $siswa->forceFill(['created_at' => '2026-11-18 08:00:00'])->save();
        $this->buatPengajuan($siswa, 'sakit', 'Disetujui', '2026-11-19', '2026-11-19');

        $this->actingAs($siswaUser)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalAlpha', 2)
            ->assertViewHas('totalSakit', 1);

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-19',
            'status' => 'sakit',
        ]);

        foreach (['2026-11-18', '2026-11-20'] as $tanggal) {
            $this->assertDatabaseHas('presensis', [
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $tanggal,
                'status' => 'alpa',
            ]);
        }
    }

    public static function jenisIzinWaliKelas(): array
    {
        return [
            'sakit' => ['sakit', 1, 0],
            'izin' => ['izin', 0, 1],
        ];
    }

    private function buatPengajuan(Siswa $siswa, string $jenisIzin, string $status = 'Pending', string $mulai = '2026-11-23', string $selesai = '2026-11-23'): PengajuanIzin
    {
        return PengajuanIzin::create([
            'siswa_id' => $siswa->id,
            'tgl_pengajuan' => '2026-11-23',
            'tgl_mulai' => $mulai,
            'tgl_selesai' => $selesai,
            'jenis_izin' => $jenisIzin,
            'alasan' => 'Keperluan uji',
            'status_approval' => $status,
        ]);
    }

    private function buatSiswa(string $kelas = 'X-1'): array
    {
        $user = $this->buatUser('siswa');
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'nisn' => uniqid('nisn-'),
            'nama_lengkap' => 'Siswa Uji',
            'kelas' => $kelas,
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
        ]);

        return [$user, $siswa];
    }

    private function buatUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' Uji',
            'email' => uniqid($role.'-').'@example.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
