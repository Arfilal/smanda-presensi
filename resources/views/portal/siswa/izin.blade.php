@extends('layouts.siswa')
@section('title', 'Form Pengajuan Izin / Dispen')
@section('content')
<div class="card card-custom p-3 bg-white mb-3">
    <h6 class="fw-bold mb-3" style="font-size: 13px;">Form Pengajuan Dokumen Informasi</h6>
    
    <form action="{{ route('siswa.izin.store') }}" method="POST">
        @csrf
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Pilih Jenis Pengajuan:</label>
            <select name="jenis_izin" class="form-select form-select-sm" required>
                <option value="dispen">Dispensasi (Lomba / Kegiatan)</option>
                <option value="sakit">Sakit</option>
                <option value="izin">Izin Pribadi</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="form-control form-control-sm" required>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" class="form-control form-control-sm" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold text-muted">Alasan / Keterangan</label>
            <textarea name="alasan" class="form-control form-control-sm" rows="2" placeholder="Tuliskan keterangan secara detail..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold py-2 rounded-pill" style="background-color: #1b3167;">Kirim Form Pengajuan</button>
    </form>
</div>

<div class="card card-custom p-3 bg-white">
    <h6 class="fw-bold mb-2" style="font-size: 13px;">Riwayat Ajuan Anda</h6>
    @forelse($pengajuans as $p)
        <div class="p-2 border-bottom small">
            <div class="d-flex justify-content-between">
                <span class="fw-bold text-uppercase text-primary" style="font-size: 11px;">Jenis: {{ $p->jenis_izin }}</span>
                <span class="badge {{ $p->status_approval == 'Disetujui' ? 'bg-success' : ($p->status_approval == 'Pending' ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size: 9px;">{{ $p->status_approval }}</span>
            </div>
            <span class="text-muted" style="font-size: 10px;">{{ $p->tgl_mulai }} s/d {{ $p->tgl_selesai }}</span><br>
            <span class="text-dark" style="font-size: 11px;">Ket: {{ $p->alasan }}</span>
        </div>
    @empty
        <p class="text-center text-muted small py-2">Belum ada riwayat ajuan.</p>
    @endforelse
</div>
@endsection