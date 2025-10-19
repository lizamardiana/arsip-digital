@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

@php
    // Definisikan variabel di awal dengan sorting dan limit - HANYA 2 ITEM
    $validSuratMasuk = ($recentSuratMasuk ?? collect())
        ->filter(function($item) {
            return $item && is_object($item) && !empty($item->nomor_surat);
        })
        ->sortByDesc(function($item) {
            return $item->tanggal_surat ?? $item->created_at;
        })
        ->take(2);

    $validSuratKeluar = ($recentSuratKeluar ?? collect())
        ->filter(function($item) {
            return $item && is_object($item) && !empty($item->nomor_surat);
        })
        ->sortByDesc(function($item) {
            return $item->tanggal_surat ?? $item->created_at;
        })
        ->take(2);
@endphp

<div class="container-fluid">
    <!-- ROW 1: CARD STATISTIK DAN ACTIVITY -->
    <div class="row mb-4">
        <!-- Card Statistik -->
        <div class="col-md-8">
            <div class="row">
                <!-- Surat Masuk -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card bg-primary text-white shadow py-2 h-100">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                        Surat Masuk</div>
                    <div class="h5 mb-0 font-weight-bold text-white">
                        {{ $stats['total_surat_masuk'] ?? 0 }}
                    </div>
                    <small class="text-white-50">Bulan ini: {{ $stats['surat_masuk_bulan_ini'] ?? 0 }}</small>
                </div>
                <div class="col-auto">
                    <i class="fas fa-envelope fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Surat Keluar -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card bg-success text-white shadow py-2 h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        Surat Keluar</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">
                                        {{ $stats['total_surat_keluar'] ?? 0 }}
                                    </div>
                                    <small class="text-white-50">Bulan ini: {{ $stats['surat_keluar_bulan_ini'] ?? 0 }}</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-envelope fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surat Baru -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card bg-info text-white shadow py-2 h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        Surat Baru</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['surat_baru'] ?? 0 }}
                                    </div>
                                    <small class="text-white-50">7 hari terakhir</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-envelope fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART - TEPAT DI BAWAH CARD STATISTIK -->
            <div class="row mt-0">
                <div class="col-12">
                    <div class="card shadow" id="chartContainer">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Statistik Surat Tahun {{ $chartData['year'] ?? date('Y') }}
                            </h6>
                            @if(!empty($chartData['available_years']) && count($chartData['available_years']) > 1)
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-calendar-alt me-1"></i> Ganti Tahun
                                    <i class="fas fa-caret-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                                     aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Pilih Tahun:</div>
                                    @foreach($chartData['available_years'] as $availableYear)
                                    <a class="dropdown-item {{ $chartData['year'] == $availableYear ? 'active' : '' }}" 
                                       href="?year={{ $availableYear }}">
                                        Tahun {{ $availableYear }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            @php
                                $totalSuratMasuk = array_sum($chartData['surat_masuk'] ?? []);
                                $totalSuratKeluar = array_sum($chartData['surat_keluar'] ?? []);
                                $hasChartData = ($totalSuratMasuk + $totalSuratKeluar) > 0;
                            @endphp
                            
                            @if($hasChartData)
                            <div class="chart-area">
                                <canvas id="suratChart"></canvas>
                            </div>
                            <div class="mt-3 text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-primary">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $totalSuratMasuk }} Surat Masuk
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-success">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            {{ $totalSuratKeluar }} Surat Keluar
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-line fa-4x mb-3 opacity-50"></i>
                                <h6 class="mb-2">Tidak ada data surat</h6>
                                <p class="small mb-0">Belum ada surat masuk atau keluar untuk tahun {{ $chartData['year'] }}</p>
                                <p class="small">Data chart akan muncul otomatis ketika ada surat</p>
                                @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                                <a href="{{ route('surat-masuk.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-1"></i>Tambah Surat Pertama
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru - SEJAJAR DENGAN CARD & CHART -->
        <div class="col-md-4">
            <div class="card shadow" id="activityContainer">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body p-0 d-flex flex-column" style="height: 500px;">
                    <!-- Surat Masuk Terbaru - HANYA 2 ITEM TERBARU -->
                    <div class="activity-section">
                        <div class="p-3 border-bottom">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-envelope me-2"></i>Surat Masuk
                                <span class="badge bg-primary float-end">{{ $validSuratMasuk->count() }}</span>
                            </h6>
                        </div>
                        <div class="activity-scroll-container">
                            @if($validSuratMasuk->count() > 0)
                            <div class="activity-scroll-content">
                                @foreach($validSuratMasuk as $surat)
                                <div class="activity-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="small mb-1 font-weight-bold text-primary">
                                                {{ $surat->nomor_surat ?? 'No Number' }}
                                            </h6>
                                            <p class="small text-muted mb-1">
                                                {{ \Illuminate\Support\Str::limit($surat->perihal ?? 'No Subject', 40) }}
                                            </p>
                                        </div>
                                        <span class="small text-muted text-nowrap ms-2">
                                            @if($surat->tanggal_surat ?? false)
                                                {{ $surat->tanggal_surat->format('d/m/Y') }}
                                            @elseif($surat->created_at ?? false)
                                                {{ $surat->created_at->format('d/m/Y') }}
                                            @else
                                                No Date
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">Tidak ada surat masuk terbaru</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Surat Keluar Terbaru - HANYA 2 ITEM TERBARU -->
                    <div class="activity-section">
                        <div class="p-3 border-bottom">
                            <h6 class="font-weight-bold text-success mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Surat Keluar
                                <span class="badge bg-success float-end">{{ $validSuratKeluar->count() }}</span>
                            </h6>
                        </div>
                        <div class="activity-scroll-container">
                            @if($validSuratKeluar->count() > 0)
                            <div class="activity-scroll-content">
                                @foreach($validSuratKeluar as $surat)
                                <div class="activity-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="small mb-1 font-weight-bold text-success">
                                                {{ $surat->nomor_surat ?? 'No Number' }}
                                            </h6>
                                            <p class="small text-muted mb-1">
                                                {{ \Illuminate\Support\Str::limit($surat->perihal ?? 'No Subject', 40) }}
                                            </p>
                                        </div>
                                        <span class="small text-muted text-nowrap ms-2">
                                            @if($surat->tanggal_surat ?? false)
                                                {{ $surat->tanggal_surat->format('d/m/Y') }}
                                            @elseif($surat->created_at ?? false)
                                                {{ $surat->created_at->format('d/m/Y') }}
                                            @else
                                                No Date
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-paper-plane fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">Tidak ada surat keluar terbaru</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Container fluid untuk margin */
.container-fluid {
    padding-left: 25px;
    padding-right: 25px;
}

/* Styling untuk activity sections */
.activity-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0; /* Penting untuk scroll di flex container */
    border-bottom: 1px solid #e3e6f0;
}

.activity-section:last-child {
    border-bottom: none;
}

/* Container untuk scroll */
.activity-scroll-container {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}

/* Content untuk scroll */
.activity-scroll-content {
    min-height: min-content;
}

/* Scroll bar styling */
.activity-scroll-container::-webkit-scrollbar {
    width: 6px;
}

.activity-scroll-container::-webkit-scrollbar-track {
    background: #f8f9fc;
    border-radius: 3px;
}

.activity-scroll-container::-webkit-scrollbar-thumb {
    background: #d1d3e2;
    border-radius: 3px;
}

.activity-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #b7b9cc;
}

/* Hover effect untuk activity item */
.activity-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border-left: 3px solid transparent;
}

.activity-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa !important;
}

/* Warna border untuk surat masuk */
.activity-section:first-child .activity-item:hover {
    border-left-color: #4e73df;
}

/* Warna border untuk surat keluar */
.activity-section:last-child .activity-item:hover {
    border-left-color: #1cc88a;
}

/* Badge styling */
.badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Pastikan card activity container memiliki tinggi maksimum sama dengan chart */
#activityContainer {
    height: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .col-md-4 {
        margin-top: 1rem;
    }
    
    #activityContainer {
        height: auto;
        min-height: 400px;
    }
    
    .activity-scroll-container {
        max-height: 200px;
    }
}
</style>
@endpush

@push('scripts')
@if(isset($chartData) && !empty($chartData['labels']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Configuration
    const ctx = document.getElementById('suratChart').getContext('2d');
    const suratChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Surat Masuk',
                data: @json($chartData['surat_masuk']),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Surat Keluar',
                data: @json($chartData['surat_keluar']),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Function untuk menyesuaikan tinggi Activity Container dengan Chart Container
    function adjustActivityHeight() {
        const chartContainer = document.getElementById('chartContainer');
        const activityContainer = document.getElementById('activityContainer');
        
        if (chartContainer && activityContainer) {
            const chartHeight = chartContainer.offsetHeight;
            
            // Terapkan tinggi yang sama ke activity container
            if (chartHeight > 0) {
                activityContainer.style.height = chartHeight + 'px';
            }
        }
    }
    
    // Jalankan setelah semua element rendered
    setTimeout(adjustActivityHeight, 100);
    window.addEventListener('resize', adjustActivityHeight);
});
</script>
@endif
@endpush