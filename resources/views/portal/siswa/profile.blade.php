@extends('layouts.siswa')
@section('title', 'Profil Siswa')
@section('content')
<div class="card card-custom p-4 bg-white text-center mb-3">
    <div class="mx-auto mb-2 bg-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
        <i class="fa-solid fa-user-graduate text-primary fs-3"></i>
    </div>
    <h6 class="fw-bold text-dark mb-1">{{ $siswa->nama_lengkap ?? '-' }}</h6>
    <span class="text-muted" style="font-size: 11px;">SISWA AKTIF AKADEMIK</span>
</div>

<div class="card card-custom p-3 bg-white mb-3">
    <h6 class="fw-bold mb-2 text-muted" style="font-size: 11px;">DETAIL INFORMASI BIODATA INDUK</h6>
    
    <div class="d-flex justify-content-between py-2 border-bottom small">
        <span class="text-muted">Nomor Induk (NISN)</span>
        <strong class="text-dark">{{ $siswa->nisn ?? '-' }}</strong>
    </div>
    <div class="d-flex justify-content-between py-2 border-bottom small">
        <span class="text-muted">Kelas / Rombel</span>
        <!-- Perbaikan: Menampilkan teks nama kelas dari database -->
        <strong class="text-primary">{{ $siswa->kelas ?? '-' }}</strong>
    </div>
    <div class="d-flex justify-content-between py-2 border-bottom small">
        <span class="text-muted">Alamat Email</span>
        <strong>{{ Auth::user()->email }}</strong>
    </div>
    <div class="d-flex justify-content-between py-2 small">
        <span class="text-muted">Otoritas Akun</span>
        <span class="badge bg-success">SISWA</span>
    </div>
</div>

<div class="text-center mt-3">
    <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-light text-muted w-100 py-2">Kembali ke Dashboard Utama</a>
</div>
@endsection