@extends('layouts.siswa')
@section('title', 'Dashboard Siswa')
@section('content')
<!-- Header Profil Card -->
<div class="card card-custom p-4 bg-white mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted" style="font-size: 12px;">SELAMAT PAGI,</span>
            <h5 class="fw-bold text-dark mb-1">{{ $siswa->nama_lengkap ?? Auth::user()->name }}</h5>
            <div>
                <!-- Perbaikan: Menampilkan teks nama kelas dari database secara langsung -->
                <span class="fw-bold text-primary" style="font-size: 12px;">{{ $siswa->kelas ?? '-' }}</span>
                <small class="text-muted ms-1" style="font-size: 11px;">| SMAN 2 Cilacap</small>
            </div>
        </div>
        <div class="text-center bg-light p-3 rounded-circle shadow-sm" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-graduation-cap text-primary fs-4"></i>
        </div>
    </div>
</div>

<!-- Status Kehadiran Hari Ini -->
<div class="card card-custom p-3 bg-white mb-3 border-start border-4 border-primary">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted" style="font-size: 11px; font-weight:700;">STATUS KEHADIRAN HARI INI</span>
            <h6 class="fw-bold text-dark mb-0 mt-1">
                @if(in_array($sesiPresensi, ['libur', 'izin'], true))
                    <span class="text-muted">{{ $teksStatusPresensi }}</span>
                @elseif(in_array($sesiPresensi, ['pulang', 'selesai'], true))
                    <span class="text-success">{{ $teksStatusPresensi }}</span>
                @else
                    <span class="text-danger">{{ $teksStatusPresensi }}</span>
                @endif
            </h6>
            <small class="text-muted d-block mt-1" style="font-size: 10px;">
                @if($jadwalPresensi['libur'])
                    {{ $jadwalPresensi['keterangan'] ?? 'Hari Libur' }}
                @else
                    Jam presensi: {{ $jadwalPresensi['jam_masuk'] }} - {{ $jadwalPresensi['jam_pulang'] }}
                @endif
            </small>
        </div>
        <i class="fa-solid fa-circle-check text-success fs-3"></i>
    </div>
</div>

<!-- Kartu Statistik Bulanan -->
<div class="row g-2 mb-3">
    <div class="col-md-6 col-6">
        <div class="card card-custom p-3 bg-primary text-white h-100">
            <small style="font-size: 11px; opacity: 0.8;">HADIR BULAN INI</small>
            <h3 class="fw-bold mb-0 mt-1">{{ $totalHadir }} <span class="fs-6 fw-normal">Hari</span></h3>
        </div>
    </div>
    <div class="col-md-6 col-6">
        <div class="card card-custom p-3 bg-white border h-100">
            <small class="text-muted" style="font-size: 11px;">TERLAMBAT</small>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalTerlambat }} <span class="fs-6 text-muted fw-normal">Kali</span></h3>
        </div>
    </div>
    <div class="col-3">
        <div class="card card-custom p-2 bg-white text-center border">
            <small class="text-muted" style="font-size: 10px;">DISPEN</small>
            <h5 class="fw-bold text-primary mb-0">{{ $totalDispen }}</h5>
        </div>
    </div>
    <div class="col-3">
        <div class="card card-custom p-2 bg-white text-center border">
            <small class="text-muted" style="font-size: 10px;">SAKIT</small>
            <h5 class="fw-bold text-success mb-0">{{ $totalSakit }}</h5>
        </div>
    </div>
    <div class="col-3">
        <div class="card card-custom p-2 bg-white text-center border">
            <small class="text-muted" style="font-size: 10px;">IZIN</small>
            <h5 class="fw-bold text-warning mb-0">{{ $totalIzin }}</h5>
        </div>
    </div>
    <div class="col-3">
        <div class="card card-custom p-2 bg-white text-center border">
            <small class="text-muted" style="font-size: 10px;">ALPA</small>
            <h5 class="fw-bold text-danger mb-0">{{ $totalAlpha }}</h5>
        </div>
    </div>
</div>

<!-- Tombol Aksi Cepat -->
<div class="row g-2">
    <div class="col-6">
        <a href="{{ route('siswa.presensi') }}" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 shadow-sm {{ in_array($sesiPresensi, ['selesai', 'libur'], true) ? 'disabled pe-none opacity-50' : '' }}" style="font-size: 13px; background-color: #1b3167;">
            <i class="fa-solid fa-camera me-1"></i> {{ $teksTombolPresensi }}
        </a>
    </div>
    <div class="col-6">
        <a href="{{ route('siswa.izin') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold rounded-3 shadow-sm" style="font-size: 13px;">
            <i class="fa-solid fa-file-pen me-1"></i> Ajukan Izin/Dispen
        </a>
    </div>
</div>
@endsection