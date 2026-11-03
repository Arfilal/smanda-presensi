@extends('layouts.walikelas')
@section('title', 'Approval Izin & Sakit Siswa')
@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Approval Izin & Sakit Siswa</h3>
    <p class="text-muted small">Verifikasi surat izin atau keterangan sakit dari siswa di kelas binaan Anda.</p>
</div>

<div class="card card-stat p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Pengajuan Izin / Sakit Masuk (Real-Time)</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted">
                <tr>
                    <th>NAMA SISWA</th>
                    <th>JENIS</th>
                    <th>TANGGAL MULAI & SELESAI</th>
                    <th>ALASAN</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr>
                    <td><strong>{{ $p->siswa->nama_lengkap ?? '-' }}</strong></td>
                    <td><span class="badge bg-secondary text-uppercase">{{ $p->jenis_izin }}</span></td>
                    <td>{{ $p->tgl_mulai }} s/d {{ $p->tgl_selesai }}</td>
                    <td>{{ $p->alasan }}</td>
                    <td>
                        @if($p->status_approval == 'Pending')
                            <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                        @elseif($p->status_approval == 'Disetujui')
                            <span class="badge bg-success px-2 py-1">Disetujui</span>
                        @else
                            <span class="badge bg-danger px-2 py-1">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($p->status_approval == 'Pending')
                            <form action="{{ route('walikelas.izin.update', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" name="status" value="Disetujui" class="btn btn-sm btn-success py-0 px-2" style="font-size: 11px;">Setujui</button>
                                <button type="submit" name="status" value="Ditolak" class="btn btn-sm btn-danger py-0 px-2" style="font-size: 11px;">Tolak</button>
                            </form>
                        @else
                            <span class="text-muted small fst-italic">Selesai diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Tidak ada pengajuan izin atau sakit dari kelas binaan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection