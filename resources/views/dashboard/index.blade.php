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
                                    <i class="fas fa-paper-plane fa-2x text-white-50"></i>
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
                                    <div class="h5 mb-0 font-weight-bold text-white">
                                        {{ $stats['surat_baru'] ?? 0 }}
                                    </div>
                                    <small class="text-white-50">7 hari terakhir</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bell fa-2x text-white-50"></i>
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
                                <i class="fas fa-chart-line me-2"></i>Statistik Surat Tahun {{ $chartData['year'] ?? date('Y') }}
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

        <!-- Aktivitas Terbaru - FIXED HEIGHT APPROACH -->
        <div class="col-md-4">
            <div class="card shadow h-100" id="activityContainer">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body p-0 d-flex flex-column" style="height: calc(100% - 60px);">
                    
                    <!-- Surat Masuk Terbaru - FIXED HEIGHT SECTION -->
                    <div class="activity-section flex-fill" style="min-height: 240px; max-height: 240px;">
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-weight-bold text-primary mb-0">
                                <i class="fas fa-envelope me-2"></i>Surat Masuk
                                <span class="badge bg-primary float-end">{{ $validSuratMasuk->count() }}</span>
                            </h6>
                        </div>
                        <div class="activity-content h-100" style="overflow-y: auto;">
                            @if($validSuratMasuk->count() > 0)
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
                                            <small class="text-primary">
                                                <i class="fas fa-calendar me-1"></i>
                                                @if($surat->tanggal_surat ?? false)
                                                    {{ $surat->tanggal_surat->format('d M Y') }}
                                                @elseif($surat->created_at ?? false)
                                                    {{ $surat->created_at->format('d M Y') }}
                                                @else
                                                    No Date
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="h-100 d-flex align-items-center justify-content-center text-center text-muted p-4">
                                    <div>
                                        <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                        <p class="small mb-0">Tidak ada surat masuk terbaru</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Surat Keluar Terbaru - FIXED HEIGHT SECTION -->
                    <div class="activity-section flex-fill" style="min-height: 240px; max-height: 240px;">
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-weight-bold text-success mb-0">
                                <i class="fas fa-paper-plane me-2"></i>Surat Keluar
                                <span class="badge bg-success float-end">{{ $validSuratKeluar->count() }}</span>
                            </h6>
                        </div>
                        <div class="activity-content h-100" style="overflow-y: auto;">
                            @if($validSuratKeluar->count() > 0)
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
                                            <small class="text-success">
                                                <i class="fas fa-calendar me-1"></i>
                                                @if($surat->tanggal_surat ?? false)
                                                    {{ $surat->tanggal_surat->format('d M Y') }}
                                                @elseif($surat->created_at ?? false)
                                                    {{ $surat->created_at->format('d M Y') }}
                                                @else
                                                    No Date
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="h-100 d-flex align-items-center justify-content-center text-center text-muted p-4">
                                    <div>
                                        <i class="fas fa-paper-plane fa-2x mb-2 opacity-50"></i>
                                        <p class="small mb-0">Tidak ada surat keluar terbaru</p>
                                    </div>
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

/* FIXED: Activity container styling - menggunakan fixed height */
#activityContainer {
    height: 580px; /* Fixed height untuk konsistensi */
}

.activity-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid #e3e6f0;
}

.activity-section:last-child {
    border-bottom: none;
}

/* Content area dengan scroll yang stabil */
.activity-content {
    flex: 1;
    overflow-y: auto;
}

/* Scroll bar styling yang konsisten */
.activity-content::-webkit-scrollbar {
    width: 6px;
}

.activity-content::-webkit-scrollbar-track {
    background: #f8f9fc;
    border-radius: 3px;
}

.activity-content::-webkit-scrollbar-thumb {
    background: #d1d3e2;
    border-radius: 3px;
}

.activity-content::-webkit-scrollbar-thumb:hover {
    background: #b7b9cc;
}

/* Hover effect untuk activity item */
.activity-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border-left: 3px solid transparent;
    background: #fff;
}

.activity-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%) !important;
    border-left-color: #4e73df;
}

.activity-section:last-child .activity-item:hover {
    border-left-color: #1cc88a;
}

/* Badge styling */
.badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Chart area styling */
.chart-area {
    position: relative;
    height: 300px;
    margin: 0 auto;
}

/* Stat card styling */
.stat-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.bg-primary-soft {
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
    border-color: rgba(78, 115, 223, 0.2);
}

.bg-success-soft {
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(28, 200, 138, 0.05) 100%);
    border-color: rgba(28, 200, 138, 0.2);
}

/* Card header improvement */
.card-header {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
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
        min-height: 500px;
    }
    
    .activity-section {
        min-height: 200px !important;
        max-height: 200px !important;
    }
    
    .chart-area {
        height: 250px;
    }
}

/* Animation for cards */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Empty state styling */
.text-muted i {
    opacity: 0.5;
}
</style>
@endpush

@push('scripts')
@if(isset($chartData) && !empty($chartData['labels']))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing enhanced chart...');
    
    const ctx = document.getElementById('suratChart');
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    try {
        // Gradient colors
        const gradientMasuk = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradientMasuk.addColorStop(0, 'rgba(78, 115, 223, 0.3)');
        gradientMasuk.addColorStop(1, 'rgba(78, 115, 223, 0.05)');
        
        const gradientKeluar = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradientKeluar.addColorStop(0, 'rgba(28, 200, 138, 0.3)');
        gradientKeluar.addColorStop(1, 'rgba(28, 200, 138, 0.05)');
        
        const suratChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Surat Masuk',
                    data: @json($chartData['surat_masuk']),
                    borderColor: '#4e73df',
                    backgroundColor: gradientMasuk,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#4e73df',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }, {
                    label: 'Surat Keluar',
                    data: @json($chartData['surat_keluar']),
                    borderColor: '#1cc88a',
                    backgroundColor: gradientKeluar,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#1cc88a',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#5a5c69'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#5a5c69',
                        bodyColor: '#5a5c69',
                        borderColor: '#e3e6f0',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' surat';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11
                            },
                            color: '#858796'
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Surat',
                            color: '#5a5c69',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#858796'
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            color: '#5a5c69',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });
        
        console.log('Enhanced chart created successfully');
        
    } catch (error) {
        console.error('Error creating enhanced chart:', error);
    }
});
</script>
@else
<script>
console.log('No chart data available');
</script>
@endif
@endpush