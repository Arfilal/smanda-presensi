@extends('layouts.siswa')
@section('title', 'Riwayat Absensi')
@section('content')
<div class="card card-custom p-3 bg-white mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted" style="font-size: 10px;">HISTORI KEHADIRAN SAYA,</span>
            <h6 class="fw-bold text-dark mb-0">{{ $siswa->nama_lengkap ?? '-' }}</h6>
            <small class="text-primary" style="font-size: 10px;">Kelas: {{ $siswa->kelas ?? '-' }} | Periode: {{ $periode }}</small>
        </div>
        <i class="fa-solid fa-clock-rotate-left text-primary fs-4"></i>
    </div>
</div>

<!-- Filter Periode Bulan -->
<form action="{{ route('siswa.riwayat') }}" method="GET" class="card card-custom p-3 bg-white mb-3">
    <label class="form-label small fw-semibold text-muted mb-1">Pilih Periode Bulan:</label>
    <div class="input-group input-group-sm">
        <input type="month" name="periode" value="{{ $periode }}" class="form-control" required>
        <button type="submit" class="btn btn-primary px-3" style="background-color: #1b3167;">Cari</button>
    </div>
</form>

<div class="card card-custom p-3 bg-white">
    <h6 class="fw-bold mb-3" style="font-size: 13px;">Daftar Aktivitas Presensi</h6>
    
    @forelse($riwayats as $r)
    <div class="p-3 mb-2 border rounded-3 bg-light small d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <strong class="text-dark">{{ $r->tgl_presensi }}</strong>
                <span class="badge bg-success" style="font-size: 9px;">{{ strtoupper($r->status) }}</span>
            </div>
            <div class="text-muted" style="font-size: 11px;">
                Masuk: {{ $r->jam_masuk ?? '-' }} | Pulang: {{ $r->jam_pulang ?? '-' }}
            </div>
        </div>
        <!-- Tombol Menuju Menu Detail Terpisah -->
        <div>
            <a href="{{ route('siswa.riwayat.detail', $r->id) }}" class="btn btn-sm btn-primary py-1 px-3" style="font-size: 11px; background-color: #1b3167;">
                Detail <i class="fa-solid fa-chevron-right ms-1"></i>
            </a>
        </div>
    </div>
    @empty
    <p class="text-center text-muted small py-3">Tidak ada riwayat presensi pada periode ini.</p>
    @endforelse
</div>

<div class="text-center mt-3">
    <a href="{{ route('siswa.dashboard') }}" class="text-muted small text-decoration-none">Kembali ke Halaman Utama</a>
</div>
@endsection