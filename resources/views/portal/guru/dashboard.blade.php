@extends('layouts.gurumapel')
@section('title', 'Dashboard Guru Mapel')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Dashboard Guru Mapel</h3>
    <p class="text-muted small">Ringkasan penugasan kelas mengajar dan verifikasi kehadiran fisik siswa.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">KELAS DIAMPU</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalKelas }} <span class="fs-6 text-muted fw-normal">rombel</span></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">TOTAL SISWA DIAJAR</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">VERIFIKASI KBM HARI INI</small>
            <h3 class="fw-bold text-success mt-1">{{ $totalVerifikasi }} <span class="fs-6 text-muted fw-normal">tercatat</span></h3>
        </div>
    </div>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Rombel Kelas Penugasan Anda</h5>
    <ul class="list-group list-group-flush">
        @forelse($kelasDiampu as $kls)
            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span class="fw-semibold text-dark"><i class="fa-solid fa-chalkboard text-primary me-2"></i> {{ $kls }}</span>
                <a href="{{ route('gurumapel.kbm', ['kelas' => $kls]) }}" class="btn btn-sm btn-outline-primary px-3">Buka Verifikasi KBM</a>
            </li>
        @empty
            <li class="list-group-item text-center text-muted py-3">Belum ada kelas yang di-mapping ke akun Anda. (Pastikan Operator sudah menautkan mapping guru mapel).</li>
        @endforelse
    </ul>
</div>
@endsection