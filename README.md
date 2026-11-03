# SMANDA Presensi

> Sistem Presensi Digital berbasis Web dan Mobile-First UI yang dikembangkan menggunakan **Laravel 11**, **Bootstrap 5**, dan **MySQL**. Sistem ini dirancang untuk mendukung manajemen kehadiran siswa secara real-time dengan verifikasi geofencing GPS, swafoto wajah (*selfie*), serta manajemen multi-role yang terintegrasi.

---

## Hak Akses (Multi-Role RBAC)

Sistem ini memiliki 5 level peran (*role*) utama yang saling terhubung secara *real-time*:
1. **Operator**: Mengelola master data, akun pengguna, dan pemetaan kelas/rombel.
2. **Kesiswaan**: Menyetujui atau menolak pengajuan dispensasi (*dispen*) siswa[cite: 2].
3. **Wali Kelas**: Menyetujui atau menolak permohonan izin pribadi dan sakit siswa, serta memantau rekap absensi kelas binaan[cite: 2].
4. **Guru Mapel**: Memverifikasi kegiatan KBM secara fisik di dalam kelas[cite: 2].
5. **Siswa**: Melakukan presensi harian via GPS & kamera depan (*swafoto*), mengajukan izin/sakit/dispen, serta melihat histori kehadiran[cite: 2].

---

## Tech Stack

* **Framework Backend**: Laravel 11 (PHP 8.2+)
* **Database**: MySQL / MariaDB (Eloquent ORM)
* **Frontend UI**: Bootstrap 5, FontAwesome 6, Custom Mobile-Responsive Layout
* **Fitur Utama**: 
  * Geofencing Lokasi GPS
  * *Live Webcam Face Capture* (Swafoto Wajah)
  * Sistem Approval Berjenjang (Kesiswaan & Wali Kelas)
  * Waktu lokal real-time berbasis perangkat (*device time*)

---

## Panduan Instalasi & Menjalankan Project

Ikuti langkah-langkah di bawah ini untuk menjalankan project ini di komputer lokal Anda:

### 1. Kloning Repository
Buka terminal/command prompt, lalu jalankan perintah berikut:
```bash
git clone [https://github.com/Arfilal/smanda-presensi.git](https://github.com/Arfilal/smanda-presensi.git)
cd smanda-presensi
