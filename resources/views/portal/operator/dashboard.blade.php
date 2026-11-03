@extends('layouts.operator')
@section('title', 'Dashboard Operator')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Dashboard Operator</h3>
    <p class="text-muted small">Manajemen basis data inti, hak akses pengguna, dan kontrol pemetaan wali kelas serta guru mapel.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">TOTAL ACCOUNTS</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalUsers }} <span class="fs-6 text-muted fw-normal">row</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">SISWA TERDAFTAR (AKTIF)</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">siswa</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">TENAGA PENDIDIK</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalGuruMapel }} <span class="fs-6 text-muted fw-normal">user</span></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white">
            <small class="text-muted fw-bold">TOTAL ROMBEL</small>
            <h3 class="fw-bold text-dark mt-1">{{ $totalRombel }} <span class="fs-6 text-muted fw-normal">grup</span></h3>
        </div>
    </div>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Log Aktivitas Pemetaan Terpadu</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA PENDIDIK / AKUN</th>
                    <th>ID USER</th>
                    <th>KELAS DITUJU</th>
                    <th>STATUS SISTEM</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><strong>{{ $log->user->name ?? '-' }}</strong></td>
                    <td>#{{ $log->user_id }}</td>
                    <td><span class="badge bg-primary bg-opacity-15 text-primary px-2 py-1">{{ $log->nama_kelas }}</span></td>
                    <td><span class="badge bg-success bg-opacity-15 text-success">Aktif Mengajar</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Belum ada aktivitas pemetaan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection