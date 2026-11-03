@extends('layouts.gurumapel')
@section('title', 'Verifikasi Fisik KBM')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Verifikasi Kehadiran Fisik KBM</h3>
    <p class="text-muted small">Validasi kehadiran fisik siswa secara langsung di dalam kelas saat jam mata pelajaran berlangsung.</p>
</div>

<!-- Filter Kelas -->
<div class="card card-stat p-3 bg-white mb-4">
    <form action="{{ route('gurumapel.kbm') }}" method="GET" class="row align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Pilih Rombel Kelas Diampu:</label>
            <select name="kelas" class="form-select" onchange="this.form.submit()">
                @foreach($kelasDiampu as $k)
                    <option value="{{ $k }}" {{ $kelasPilihan == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Siswa Kelas: <span class="text-primary">{{ $kelasPilihan ?? 'Tidak ada' }}</span></h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA SISWA</th>
                    <th>NISN</th>
                    <th>STATUS PRESENSI GPS</th>
                    <th>STATUS FISIK KBM (VALIDASI GURU)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $s)
                @php 
                    $presensiGPS = $s->presensis->first(); 
                    $rekapFisik = $s->rekapKbms->first();
                @endphp
                <tr>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td>{{ $s->nisn }}</td>
                    <td>
                        @if($presensiGPS)
                            <span class="badge bg-success bg-opacity-15 text-success px-2 py-1">GPS: {{ ucfirst($presensiGPS->status) }}</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-15 text-dark px-2 py-1">Belum Absen GPS</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('gurumapel.kbm.store') }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $s->id }}">
                            <input type="hidden" name="kelas" value="{{ $kelasPilihan }}">
                            
                            <select name="status_fisik" class="form-select form-select-sm" style="width: 160px;" required>
                                <option value="hadir" {{ optional($rekapFisik)->status_fisik == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="bolos" {{ optional($rekapFisik)->status_fisik == 'bolos' ? 'selected' : '' }}>Bolos</option>
                                <option value="hadir_manual" {{ optional($rekapFisik)->status_fisik == 'hadir_manual' ? 'selected' : '' }}>Hadir Manual</option>
                            </select>
                            
                            <button type="submit" class="btn btn-sm btn-primary px-3" style="background-color: #1b3167;">Simpan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Tidak ada data siswa pada kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection