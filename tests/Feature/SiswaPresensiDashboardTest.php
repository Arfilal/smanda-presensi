<?php

namespace Tests\Feature;

use App\Models\HariLibur;
use App\Models\PengaturanJam;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiswaPresensiDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_menampilkan_alpa_terbaru_setelah_jam_pulang_tanpa_presensi(): void
    {
        $this->travelTo('2026-11-23 16:01:00');
        [$user, $siswa] = $this->buatSiswa();

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalAlpha', 1)
            ->assertViewHas('teksStatusPresensi', 'Alpa')
            ->assertViewHas('sesiPresensi', 'masuk');

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => 'alpa',
        ]);
    }

    public function test_menghapus_alpa_sebelum_jam_pulang_agar_siswa_dapat_presensi(): void
    {
        $this->travelTo('2026-11-23 15:59:00');
        [$user, $siswa] = $this->buatSiswa();
        Presensi::create([
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'status' => 'alpa',
        ]);

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalAlpha', 0)
            ->assertViewHas('teksStatusPresensi', 'Belum Absen')
            ->assertViewHas('sesiPresensi', 'masuk');

        $this->assertDatabaseMissing('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
        ]);
    }

    public function test_menandai_sabtu_minggu_sebagai_hari_libur(): void
    {
        $this->travelTo('2026-11-22 17:01:00');
        [$user, $siswa] = $this->buatSiswa();

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('sesiPresensi', 'libur')
            ->assertViewHas('teksTombolPresensi', 'Hari Libur')
            ->assertViewHas('totalAlpha', 0);

        $this->assertDatabaseMissing('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-22',
        ]);
    }

    public function test_tidak_mencatat_alpa_pada_tanggal_libur_yang_diatur_operator(): void
    {
        $this->travelTo('2026-11-25 17:01:00');
        [$user, $siswa] = $this->buatSiswa();
        HariLibur::create([
            'tanggal' => '2026-11-25',
            'keterangan' => 'Libur Sekolah',
        ]);

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('sesiPresensi', 'libur')
            ->assertViewHas('teksStatusPresensi', 'Hari Libur - Libur Sekolah');

        $this->assertDatabaseMissing('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-25',
        ]);
    }

    public function test_jadwal_khusus_operator_menggeser_waktu_alpa(): void
    {
        $this->travelTo('2026-11-22 17:01:00');
        [$user, $siswa] = $this->buatSiswa();
        PengaturanJam::create([
            'tanggal' => '2026-11-22',
            'jam_masuk' => '16:00',
            'jam_pulang' => '17:00',
            'keterangan' => 'Kegiatan Sekolah',
        ]);

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('sesiPresensi', 'masuk')
            ->assertViewHas('totalAlpha', 1);

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-22',
            'status' => 'alpa',
        ]);
    }

    public function test_presensi_mengikuti_jam_khusus_operator(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-11-22 16:00:00');
        [$user, $siswa] = $this->buatSiswa();
        PengaturanJam::create([
            'tanggal' => '2026-11-22',
            'jam_masuk' => '16:00',
            'jam_pulang' => '17:00',
            'keterangan' => 'Kegiatan Sekolah',
        ]);

        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('masuk'))
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('success', 'Presensi masuk berhasil direkam.');

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-22',
            'jam_masuk' => '16:00:00',
            'status' => 'hadir',
        ]);
    }

    public function test_tombol_berubah_menjadi_presensi_pulang_setelah_check_in(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-11-23 06:00:00');
        [$user, $siswa] = $this->buatSiswa();

        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('masuk'))
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('success', 'Presensi masuk berhasil direkam.');

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('sesiPresensi', 'pulang')
            ->assertViewHas('teksTombolPresensi', 'Presensi Pulang')
            ->assertViewHas('totalHadir', 1);

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'jam_masuk' => '06:00:00',
            'jam_pulang' => null,
            'status' => 'hadir',
        ]);
    }

    public function test_check_out_menyelesaikan_presensi_hari_ini(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-11-23 06:30:00');
        [$user, $siswa] = $this->buatSiswa();
        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('masuk'));

        $this->travelTo('2026-11-23 15:00:00');
        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('pulang'))
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('success', 'Presensi pulang berhasil direkam.');

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('sesiPresensi', 'selesai')
            ->assertViewHas('teksTombolPresensi', 'Presensi Selesai');

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'jam_pulang' => '15:00:00',
        ]);
    }

    public function test_menolak_presensi_di_luar_jam_yang_diatur(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-11-23 05:59:00');
        [$user] = $this->buatSiswa();

        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('masuk'))
            ->assertSessionHas('error', 'Presensi hanya tersedia pukul 06:00 sampai 16:00.');

        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_menolak_check_in_ulang_setelah_sudah_presensi_masuk(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-11-23 07:00:00');
        [$user, $siswa] = $this->buatSiswa();
        Presensi::create([
            'siswa_id' => $siswa->id,
            'tgl_presensi' => '2026-11-23',
            'jam_masuk' => '06:30:00',
            'status' => 'hadir',
        ]);

        $this->actingAs($user)->post(route('siswa.presensi.store'), $this->dataPresensi('masuk'))
            ->assertSessionHas('error', 'Anda sudah melakukan presensi masuk hari ini.');

        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_backfill_alpa_sebelum_pembaruan_melewati_akhir_pekan_dan_hari_ini(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        [$user, $siswa] = $this->buatSiswa();
        $siswa->forceFill(['created_at' => '2026-11-18 08:00:00'])->save();

        $this->actingAs($user)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertViewHas('totalAlpha', 3);

        foreach (['2026-11-18', '2026-11-19', '2026-11-20'] as $tanggal) {
            $this->assertDatabaseHas('presensis', [
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $tanggal,
                'status' => 'alpa',
            ]);
        }

        foreach (['2026-11-21', '2026-11-22', '2026-11-23'] as $tanggal) {
            $this->assertDatabaseMissing('presensis', [
                'siswa_id' => $siswa->id,
                'tgl_presensi' => $tanggal,
            ]);
        }
    }

    private function buatSiswa(string $kelas = 'X-1'): array
    {
        $user = User::create([
            'name' => 'Siswa Uji',
            'email' => uniqid('siswa-').'@example.test',
            'password' => 'password',
            'role' => 'siswa',
        ]);

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

    private function dataPresensi(string $tipe): array
    {
        return [
            'lokasi_lat' => '-7.73',
            'lokasi_long' => '109.00',
            'tipe' => $tipe,
            'image' => 'data:image/png;base64,'.base64_encode('gambar-uji'),
            'device_time' => '06:30:00',
        ];
    }
}
