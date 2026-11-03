@extends('layouts.siswa')
@section('title', 'Detail Presensi')
@section('content')
<div class="card card-custom p-3 bg-white mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted" style="font-size: 10px;">DETAIL KEHADIRAN,</span>
            <h6 class="fw-bold text-dark mb-0">{{ $siswa->nama_lengkap ?? '-' }}</h6>
            <small class="text-primary" style="font-size: 10px;">Kelas: {{ $siswa->kelas ?? '-' }}</small>
        </div>
        <span class="badge bg-success px-3 py-2" style="font-size: 11px;">{{ strtoupper($presensi->status) }}</span>
    </div>
</div>

<div class="card card-custom p-4 bg-white mb-3 text-center">
    <h6 class="fw-bold text-dark mb-3" style="font-size: 13px;">Swafoto Wajah (Selfie) Kehadiran</h6>
    
    @if($presensi->foto_selfie)
        <div class="mx-auto rounded-3 overflow-hidden border shadow-sm" style="width: 180px; height: 220px;">
            <img src="{{ asset('storage/uploads/' . $presensi->foto_selfie) }}" alt="Foto Selfie" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @else
        <div class="p-4 bg-light rounded text-muted small">
            <i class="fa-solid fa-triangle-exclamation mb-2 fs-4 text-warning"></i><br>
            Foto selfie tidak tersedia untuk sesi presensi ini.
        </div>
    @endif
</div>

<div class="card card-custom p-3 bg-white mb-3">
    <h6 class="fw-bold mb-2 text-dark" style="font-size: 13px;">Informasi Waktu & Lokasi GPS</h6>
    <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Tanggal Presensi</span>
        <strong class="text-dark">{{ $presensi->tgl_presensi }}</strong>
    </div>
    <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Jam Masuk</span>
        <strong class="text-success">{{ $presensi->jam_masuk ?? '-' }}</strong>
    </div>
    <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Jam Pulang</span>
        <strong class="text-primary">{{ $presensi->jam_pulang ?? '-' }}</strong>
    </div>
    <div class="d-flex justify-content-between py-1 small">
        <span class="text-muted">Koordinat GPS</span>
        <span class="font-monospace text-muted" style="font-size: 10px;">{{ $presensi->lokasi_lat ?? '-' }}, {{ $presensi->lokasi_long ?? '-' }}</span>
    </div>
</div>

<div class="text-center mt-3">
    <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm btn-primary px-4 py-2 rounded-pill shadow-sm" style="background-color: #1b3167;">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Riwayat Absensi
    </a>
</div>
@endsection