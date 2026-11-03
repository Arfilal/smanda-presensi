@extends('layouts.kesiswaan')
@section('title', 'Monitoring & Rekapitulasi Presensi')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Monitoring & Rekapitulasi Presensi</h3>
    <p class="text-muted small">Pantau log riwayat kehadiran, waktu masuk/pulang, serta koordinat GPS siswa secara real-time.</p>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Log Kehadiran Siswa Terkini</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA SISWA</th>
                    <th>KELAS</th>
                    <th>TANGGAL PRESENSI</th>
                    <th>JAM MASUK</th>
                    <th>JAM PULANG</th>
                    <th>STATUS KEHADIRAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $pr)
                <tr>
                    <td><strong>{{ $pr->siswa->nama_lengkap ?? '-' }}</strong></td>
                    <td><span class="badge bg-secondary bg-opacity-15 text-dark px-2 py-1">{{ $pr->siswa->kelas ?? '-' }}</span></td>
                    <td>{{ $pr->tgl_presensi }}</td>
                    <td>{{ $pr->jam_masuk ?? '-' }}</td>
                    <td>{{ $pr->jam_pulang ?? '-' }}</td>
                    <td>
                        @if($pr->status == 'hadir')
                            <span class="badge bg-success px-2 py-1">Hadir</span>
                        @elseif($pr->status == 'terlambat')
                            <span class="badge bg-warning text-dark px-2 py-1">Terlambat</span>
                        @else
                            <span class="badge bg-danger px-2 py-1">{{ ucfirst($pr->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Belum ada riwayat data presensi siswa tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection