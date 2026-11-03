@extends('layouts.operator')
@section('title', 'Manajemen Jadwal Khusus')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Manajemen Jadwal Khusus</h3>
    <p class="text-muted small">Atur jadwal presensi darurat atau kegiatan khusus secara fleksibel.</p>
</div>

<div class="card card-stat p-4 bg-white mb-4">
    <h5 class="fw-bold mb-3">Tambah Jadwal Khusus</h5>
    <form action="{{ route('operator.jadwal.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Jam Masuk</label>
            <input type="time" name="jam_masuk" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Jam Pulang</label>
            <input type="time" name="jam_pulang" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Cth: Upacara / Rapat Guru">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100 fw-semibold text-white">Simpan Jadwal</button>
        </div>
    </form>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Jadwal Khusus (Real-Time)</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>TANGGAL</th>
                    <th>JAM MASUK</th>
                    <th>JAM PULANG</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $j)
                <tr>
                    <td><strong>{{ $j->tanggal }}</strong></td>
                    <td>{{ $j->jam_masuk }}</td>
                    <td>{{ $j->jam_pulang }}</td>
                    <td>{{ $j->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Belum ada jadwal khusus yang diatur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection