@extends('layouts.kesiswaan')
@section('title', 'Dashboard Kesiswaan')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Dashboard Kesiswaan</h3>
    <p class="text-muted small">Monitor kehadiran harian siswa dan validasi pengajuan dispensasi sekolah secara terpusat.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">TOTAL SISWA</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">HADIR HARI INI</small>
            <h3 class="fw-bold text-success mt-1">{{ $totalHadirHariIni }} <span class="fs-6 text-muted fw-normal">hadir</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">DISPEN PENDING</small>
            <h3 class="fw-bold text-warning mt-1">{{ $totalDispenPending }} <span class="fs-6 text-muted fw-normal">pengajuan</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">STATUS ALPA</small>
            <h3 class="fw-bold text-danger mt-1">{{ $totalAlpha }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Antrean Pengajuan Dispensasi Terbaru</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA SISWA</th>
                    <th>KELAS</th>
                    <th>TANGGAL KEGIATAN</th>
                    <th>ALASAN / KEGIATAN</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDispen as $d)
                <tr>
                    <td><strong>{{ $d->siswa->nama_lengkap ?? '-' }}</strong></td>
                    <td><span class="badge bg-secondary bg-opacity-15 text-dark">{{ $d->siswa->kelas ?? '-' }}</span></td>
                    <td>{{ $d->tgl_mulai }} s/d {{ $d->tgl_selesai }}</td>
                    <td>{{ $d->alasan }}</td>
                    <td>
                        @if($d->status_approval == 'Pending')
                            <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                        @elseif($d->status_approval == 'Disetujui')
                            <span class="badge bg-success px-2 py-1">Disetujui</span>
                        @else
                            <span class="badge bg-danger px-2 py-1">Ditolak</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Belum ada pengajuan dispensasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection