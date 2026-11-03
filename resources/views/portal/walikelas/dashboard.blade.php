@extends('layouts.walikelas')
@section('title', 'Dashboard Wali Kelas')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Dashboard Wali Kelas</h3>
    <p class="text-muted small">Monitor kehadiran dan rekapitulasi harian siswa untuk kelas binaan: <span class="fw-bold text-primary">{{ $kelas }}</span></p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">SISWA BINAAN</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">HADIR HARI INI</small>
            <h3 class="fw-bold text-success mt-1">{{ $hadirHariIni }} <span class="fs-6 text-muted fw-normal">hadir</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">IZIN / SAKIT PENDING</small>
            <h3 class="fw-bold text-warning mt-1">{{ $izinPending }} <span class="fs-6 text-muted fw-normal">pengajuan</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">STATUS ALPA</small>
            <h3 class="fw-bold text-danger mt-1">{{ $alpaHariIni }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Siswa Kelas Binaan</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA LENGKAP SISWA</th>
                    <th>NISN</th>
                    <th>JENIS KELAMIN</th>
                    <th>NO HP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $s)
                <tr>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td>{{ $s->nisn }}</td>
                    <td>{{ $s->jenis_kelamin }}</td>
                    <td>{{ $s->no_hp }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Belum ada data siswa untuk kelas binaan ini. (Pastikan Operator sudah melakukan mapping wali kelas ke kelas yang sesuai, cth: X MIPA 1).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection