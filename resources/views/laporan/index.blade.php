@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
<div class="row mx-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Buat Laporan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.generate') }}" method="GET" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis Laporan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                                </select>
                                <div class="invalid-feedback">Harap pilih jenis laporan.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date', date('Y-01-01')) }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf me-2"></i>Buat Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Keseluruhan -->
<div class="row mt-4 mx-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistik Keseluruhan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            @php
                                // Ambil semua data surat masuk dari database
                                $totalSuratMasuk = \App\Models\SuratMasuk::count();
                            @endphp
                            <h3 class="text-primary">{{ $totalSuratMasuk }}</h3>
                            <p class="text-muted mb-0">Total Surat Masuk</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            @php
                                // Ambil semua data surat keluar dari database
                                $totalSuratKeluar = \App\Models\SuratKeluar::count();
                            @endphp
                            <h3 class="text-success">{{ $totalSuratKeluar }}</h3>
                            <p class="text-muted mb-0">Total Surat Keluar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Export Semua Data</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('laporan.export-excel') }}?jenis=masuk" 
                       class="btn btn-success mb-2">
                        <i class="fas fa-file-excel me-2"></i>Export SEMUA Surat Masuk
                    </a>
                    <a href="{{ route('laporan.export-excel') }}?jenis=keluar" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export SEMUA Surat Keluar
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Export akan mencakup semua data tanpa filter tanggal
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Laporan Cepat untuk Surat Masuk dan Surat Keluar -->
<div class="row mt-4 mx-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Laporan Cepat</h5>
            </div>
            <div class="card-body">
                <!-- Surat Masuk -->
                <h6 class="text-primary mb-3"><i class="fas fa-inbox me-2"></i>Surat Masuk</h6>
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=masuk&start_date={{ date('Y-m-d') }}&end_date={{ date('Y-m-d') }}" 
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-calendar-day me-2"></i>Hari Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        @php
                            $weekStart = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                            $weekEnd = \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d');
                        @endphp
                        <a href="{{ route('laporan.generate') }}?jenis=masuk&start_date={{ $weekStart }}&end_date={{ $weekEnd }}" 
                           class="btn btn-outline-info w-100">
                            <i class="fas fa-calendar-week me-2"></i>Minggu Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=masuk&start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-d') }}" 
                           class="btn btn-outline-success w-100">
                            <i class="fas fa-calendar-alt me-2"></i>Bulan Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=masuk&start_date={{ date('Y-01-01') }}&end_date={{ date('Y-12-31') }}" 
                           class="btn btn-outline-warning w-100">
                            <i class="fas fa-calendar me-2"></i>Tahun Ini
                        </a>
                    </div>
                </div>

                <!-- Surat Keluar -->
                <h6 class="text-success mb-3"><i class="fas fa-paper-plane me-2"></i>Surat Keluar</h6>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=keluar&start_date={{ date('Y-m-d') }}&end_date={{ date('Y-m-d') }}" 
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-calendar-day me-2"></i>Hari Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=keluar&start_date={{ $weekStart }}&end_date={{ $weekEnd }}" 
                           class="btn btn-outline-info w-100">
                            <i class="fas fa-calendar-week me-2"></i>Minggu Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=keluar&start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-d') }}" 
                           class="btn btn-outline-success w-100">
                            <i class="fas fa-calendar-alt me-2"></i>Bulan Ini
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('laporan.generate') }}?jenis=keluar&start_date={{ date('Y-01-01') }}&end_date={{ date('Y-12-31') }}" 
                           class="btn btn-outline-warning w-100">
                            <i class="fas fa-calendar me-2"></i>Tahun Ini
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.row.mx-3 {
    margin-left: 1rem;
    margin-right: 1rem;
}

@media (max-width: 768px) {
    .row.mx-3 {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
}

.list-group-item:last-child {
    border-bottom: none;
}

.btn-group-vertical .btn {
    text-align: left;
}

.card.h-100 {
    height: 100%;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set max date untuk end_date ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('end_date').max = today;
    
    // Form validation - hanya validasi jenis laporan
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const jenis = document.getElementById('jenis');
        if (!jenis.value) {
            event.preventDefault();
            event.stopPropagation();
            jenis.focus();
        }
        form.classList.add('was-validated');
    }, false);

    // Set default start_date ke awal tahun
    document.getElementById('start_date').value = '{{ date('Y-01-01') }}';
});
</script>
@endpush