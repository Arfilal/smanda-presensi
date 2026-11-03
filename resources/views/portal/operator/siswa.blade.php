@extends('layouts.operator')
@section('title', 'Master Data Siswa')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Master Data Siswa</h3>
    <p class="text-muted small">Kelola identitas, kelas, dan status akademik seluruh siswa SMAN 2 Cilacap.</p>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Siswa Terdaftar (Real-Time Database)</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA LENGKAP SISWA</th>
                    <th>NISN</th>
                    <th>ROMBEL KELAS</th>
                    <th>JENIS KELAMIN</th>
                    <th>NO HP</th>
                </tr>
            </thead>
            <tbody>
                @php use App\Models\Siswa; $siswas = Siswa::all(); @endphp
                @forelse($siswas as $s)
                <tr>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td>{{ $s->nisn }}</td>
                    <td><span class="badge bg-info bg-opacity-15 text-dark px-2 py-1">{{ $s->kelas }}</span></td>
                    <td>{{ $s->jenis_kelamin }}</td>
                    <td>{{ $s->no_hp }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Belum ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection