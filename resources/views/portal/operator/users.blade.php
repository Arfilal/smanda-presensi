@extends('layouts.operator')
@section('title', 'Master Data Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark">Master Data Users</h3>
        <p class="text-muted small mb-0">Kelola seluruh akun pengguna (role) yang memiliki akses ke dalam sistem.</p>
    </div>
</div>

<div class="card card-stat p-4 bg-white mb-4">
    <h5 class="fw-bold mb-3">Tambah Akun Pengguna Baru</h5>
    <form action="{{ route('operator.users.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Role / Akses</label>
            <select name="role" class="form-select" required>
                <option value="siswa">Siswa</option>
                <option value="guru_mapel">Guru Mapel</option>
                <option value="wali_kelas">Wali Kelas</option>
                <option value="kesiswaan">Kesiswaan</option>
                <option value="operator">Operator</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #1b3167;">Simpan Akun</button>
        </div>
    </form>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Data Akun Pengguna (Real-Time)</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>ID / NAMA PENGGUNA</th>
                    <th>ALAMAT EMAIL</th>
                    <th>HAK AKSES (ROLE)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>
                        <strong>{{ $u->name }}</strong><br>
                        <small class="text-muted">User ID: #{{ $u->id }}</small>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-secondary px-2 py-1 text-uppercase" style="font-size: 11px;">{{ $u->role }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection