@extends('layouts.siswa')
@section('title', 'Form Presensi Siswa')
@section('content')
<div class="card card-custom p-3 bg-white mb-3">
    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">FORM PRESENSI MANDIRI</h6>
    <small class="text-muted" style="font-size: 11px;">{{ $siswa->nama_lengkap ?? '-' }} | {{ $siswa->kelas ?? '-' }}</small>
</div>

<!-- Informasi Waktu Device Real-Time -->
<div class="card card-custom p-3 bg-white mb-3 text-center">
    <small class="text-muted fw-bold" style="font-size: 10px;">WAKTU PERANGKAT ANDA</small>
    <h4 class="fw-bold text-primary mt-1 mb-0" id="liveClock">00:00:00</h4>
    <small class="text-muted" style="font-size: 10px;" id="liveDate">-</small>
</div>

<!-- 1. Verifikasi Geofencing -->
<div class="card card-custom p-3 bg-white mb-3 text-center">
    <small class="text-muted fw-bold" style="font-size: 10px;">1. VERIFIKASI GEOFENCING RADIUS</small>
    <div class="p-2 bg-light rounded-3 my-2 text-primary small fw-semibold">
        <i class="fa-solid fa-location-crosshairs fa-spin me-1"></i> <span id="gpsStatus">Mencari Lokasi GPS...</span>
    </div>
</div>

<!-- 2. Ambil Swafoto Wajah (Kamera Depan) -->
<div class="card card-custom p-3 bg-white mb-3 text-center">
    <small class="text-muted fw-bold mb-2 d-block" style="font-size: 10px;">2. AMBIL SWAFOTO WAJAH (SELFIE)</small>
    
    <div class="position-relative mx-auto rounded-3 overflow-hidden bg-dark shadow-sm" style="width: 100%; height: 220px;">
        <video id="video" width="100%" height="220px" autoplay playsinline style="object-fit: cover;"></video>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>

    <button type="button" id="snap" class="btn btn-sm btn-dark mt-2 rounded-pill px-4" style="font-size: 11px;">
        <i class="fa-solid fa-camera"></i> Ambil Foto
    </button>
    <div id="previewContainer" class="mt-2" style="display:none;">
        <span class="text-success small fw-bold"><i class="fa-solid fa-circle-check"></i> Foto Selfie Berhasil Diambil</span>
    </div>
</div>

<form action="{{ route('siswa.presensi.store') }}" method="POST" id="formPresensi">
    @csrf
    <input type="hidden" name="lokasi_lat" id="lokasi_lat">
    <input type="hidden" name="lokasi_long" id="lokasi_long">
    <input type="hidden" name="image" id="imageInput">
    <!-- Kirim waktu perangkat secara dinamis -->
    <input type="hidden" name="device_time" id="deviceTimeInput">

    <div class="mb-3">
        <label class="form-label small fw-semibold">Sesi Presensi:</label>
        <select name="tipe" class="form-select form-select-sm" required>
            <option value="masuk">Presensi Masuk (Check-In)</option>
            <option value="pulang">Presensi Pulang (Check-Out)</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2 rounded-pill shadow-sm" style="background-color: #1b3167; font-size: 13px;" id="btnKirim">Kirim Data Presensi</button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('siswa.dashboard') }}" class="text-muted small text-decoration-none">Kembali ke Halaman Utama</a>
</div>

<script>
    // Jam Real-Time Berdasarkan Device
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('liveClock').innerText = `${hours}:${minutes}:${seconds}`;
        document.getElementById('deviceTimeInput').value = `${hours}:${minutes}:${seconds}`;

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('liveDate').innerText = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Aktifkan Kamera Depan
    const video = document.getElementById('video');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false })
        .then(stream => { video.srcObject = stream; })
        .catch(err => { alert("Kamera depan tidak dapat diakses."); });

    // Ambil Foto dari Video
    document.getElementById('snap').addEventListener('click', function() {
        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth || 300;
        canvas.height = video.videoHeight || 220;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('imageInput').value = dataURL;
        document.getElementById('previewContainer').style.display = 'block';
    });

    // Ambil Lokasi GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('lokasi_lat').value = pos.coords.latitude;
            document.getElementById('lokasi_long').value = pos.coords.longitude;
            document.getElementById('gpsStatus').innerText = "Lokasi GPS Terverifikasi (Akurat)";
        }, function(err) {
            document.getElementById('gpsStatus').innerText = "Gagal Mendeteksi GPS";
        });
    }
</script>
@endsection