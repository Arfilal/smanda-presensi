@extends('layouts.walikelas')
@section('title', 'Rekap Absen Kelas Binaan')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Rekap Absen Kelas Binaan</h3>
    <p class="text-muted small">Laporan status kehadiran real-time harian untuk seluruh siswa di kelas <span class="fw-bold text-primary">{{ $kelas }}</span>.</p>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Tabel Kehadiran Hari Ini</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA SISWA</th>
                    <th>NISN</th>
                    <th>JAM MASUK</th>
                    <th>JAM PULANG</th>
                    <th>STATUS KEHADIRAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $s)
                @php $presensiHariIni = $s->presensis->first(); @endphp
                <tr>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td>{{ $s->nisn }}</td>
                    <td>{{ $presensiHariIni->jam_masuk ?? '-' }}</td>
                    <td>{{ $presensiHariIni->jam_pulang ?? '-' }}</td>
                    <td>
                        @if($presensiHariIni)
                            @if($presensiHariIni->status == 'hadir')
                                <span class="badge bg-success px-2 py-1">Hadir</span>
                            @elseif($presensiHariIni->status == 'terlambat')
                                <span class="badge bg-warning text-dark px-2 py-1">Terlambat</span>
                            @else
                                <span class="badge bg-danger px-2 py-1">{{ ucfirst($presensiHariIni->status) }}</span>
                            @endif
                        @else
                            <span class="badge bg-secondary px-2 py-1">Belum Absen / Alpa</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Belum ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection