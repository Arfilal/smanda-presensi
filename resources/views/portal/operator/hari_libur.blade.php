@extends('layouts.operator')
@section('title', 'Master Data Hari Libur')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Master Data Hari Libur</h3>
    <p class="text-muted small">Kelola tanggal merah dan hari libur nasional untuk mencegah sistem mencatat Alpa.</p>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-stat p-4 bg-white">
            <h5 class="fw-bold mb-3">Tambah Hari Libur Baru</h5>
            <form action="{{ route('operator.libur.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Libur</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan / Nama Libur</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Cth: Libur Idul Fitri" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #1b3167;">Simpan Hari Libur</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card card-stat p-4 bg-white">
            <h5 class="fw-bold mb-3">Daftar Tanggal Merah & Hari Libur</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>TANGGAL</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($liburs as $l)
                        <tr>
                            <td><strong>{{ $l->tanggal }}</strong></td>
                            <td><span class="badge bg-danger bg-opacity-15 text-danger px-2 py-1">{{ $l->keterangan }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">Belum ada data hari libur yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection