@extends('layouts.app')

@section('title', 'Detail Surat Keluar')
@section('page-title', 'Detail Surat Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surat-keluar.index') }}">Surat Keluar</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('surat-keluar.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('surat-keluar.edit', $suratKeluar) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        @if($suratKeluar->file_path)
        <a href="{{ route('surat-keluar.download', $suratKeluar) }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Download
        </a>
        @endif
    </div>
@endsection

@section('content')
<div class="row mx-3"> <!-- Tambahkan margin kiri kanan di row utama -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Surat Keluar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nomor Surat</th>
                                <td>: <strong>{{ $suratKeluar->nomor_surat }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal Surat</th>
                                <td>: {{ $suratKeluar->tanggal_surat->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Kirim</th>
                                <td>: {{ $suratKeluar->tanggal_kirim->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tujuan</th>
                                <td>: {{ $suratKeluar->tujuan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Dibuat Oleh</th>
                                <td>: {{ $suratKeluar->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>: {{ $suratKeluar->created_at->format('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Diperbarui Pada</th>
                                <td>: {{ $suratKeluar->updated_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>Perihal</h6>
                    <p class="border p-3 rounded bg-light">{{ $suratKeluar->perihal }}</p>
                </div>

                @if($suratKeluar->isi_ringkas)
                <div class="mb-3">
                    <h6>Isi Ringkas</h6>
                    <p class="border p-3 rounded bg-light">{{ $suratKeluar->isi_ringkas }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- File Attachment -->
        @if($suratKeluar->file_path)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">File Lampiran</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="{{ $suratKeluar->file_icon }} fa-4x"></i>
                </div>
                <h6>{{ $suratKeluar->file_name }}</h6>
                <p class="text-muted small">
                    {{ strtoupper($suratKeluar->file_extension) }} File
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('surat-keluar.download', $suratKeluar) }}" 
                       class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download File
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center text-muted">
                <i class="fas fa-file fa-3x mb-3"></i>
                <p>Tidak ada file lampiran</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Tambahkan margin untuk konten utama */
.row.mx-3 {
    margin-left: 1rem;
    margin-right: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row.mx-3 {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
}
</style>
@endpush