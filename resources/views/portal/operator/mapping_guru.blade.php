@extends('layouts.operator')
@section('title', 'Mapping Guru Mapel')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Mapping Guru Mapel</h3>
    <p class="text-muted small">Kelola hak penugasan akun pengguna ke dalam fungsionalitas Guru Mata Pelajaran.</p>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-stat p-4 bg-white">
            <h5 class="fw-bold mb-3">Tambah Relasi Guru Mapel</h5>
            <form action="{{ route('operator.mapping.guru.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Tenaga Pendidik / Guru</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Rombel Kelas Diampu</label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Cth: X MIPA 1" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #1b3167;">Simpan Tautan Relasi</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card card-stat p-4 bg-white">
            <h5 class="fw-bold mb-3">Relasi Akun Pengampu Guru Mapel</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>NAMA PENDIDIK / GURU</th>
                            <th>EMAIL</th>
                            <th>KELAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mappings as $m)
                        <tr>
                            <td><strong>{{ $m->user->name ?? '-' }}</strong></td>
                            <td>{{ $m->user->email ?? '-' }}</td>
                            <td><span class="badge bg-primary bg-opacity-15 text-primary px-2 py-1">{{ $m->nama_kelas }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection