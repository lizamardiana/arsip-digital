@extends('layouts.app')

@section('title', 'Surat Keluar')
@section('page-title', 'Surat Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item active">Surat Keluar</li>
@endsection

@section('page-actions')
    <a href="{{ route('surat-keluar.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Surat
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mx-3"> <!-- Tambahkan margin kiri kanan -->
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Surat Keluar</h5>
                    
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('surat-keluar.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Cari nomor surat, tujuan, perihal...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select class="form-select" id="tahun" name="tahun">
                                    <option value="">Semua Tahun</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select class="form-select" id="bulan" name="bulan">
                                    <option value="">Semua Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2 w-100">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('surat-keluar.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-redo me-2"></i>Reset Filter
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mx-3"> <!-- Tambahkan margin kiri kanan -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Tanggal Surat</th>
                                <th>Tujuan</th>
                                <th>Perihal</th>
                                <th>Aksi</th> <!-- Hapus kolom Kategori dan Status -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratKeluar as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($suratKeluar->currentPage() - 1) * $suratKeluar->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->nomor_surat }}</strong>
                                    @if($item->file_path)
                                        <i class="fas fa-paperclip text-muted ms-1" title="Memiliki file"></i>
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_surat->format('d/m/Y') }}</td>
                                <td>{{ $item->tujuan }}</td>
                                <td>{{ Str::limit($item->perihal, 50) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('surat-keluar.show', $item) }}" 
                                           class="btn btn-info" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @php
                                            $canEdit = auth()->id() === $item->user_id || (auth()->user()->isAdmin ?? false);
                                            $canDelete = auth()->id() === $item->user_id || (auth()->user()->isAdmin ?? false);
                                        @endphp
                                        
                                        @if($canEdit)
                                        <a href="{{ route('surat-keluar.edit', $item) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pen"></i>
                                        </a>
                                        @endif
                                        
                                        @if($item->file_path)
                                        <a href="{{ route('surat-keluar.download', $item) }}" 
                                           class="btn btn-success" title="Download File">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                        
                                        @if($canDelete)
                                        <form action="{{ route('surat-keluar.destroy', $item) }}" method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-delete" 
                                                    title="Hapus" data-item-name="surat keluar">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted"> <!-- Sesuaikan colspan -->
                                    <i class="fas fa-paper-plane fa-2x mb-3"></i><br>
                                    Tidak ada data surat keluar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($suratKeluar->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $suratKeluar->firstItem() }} hingga {{ $suratKeluar->lastItem() }} 
                        dari {{ $suratKeluar->total() }} entri
                    </div>
                    <div>
                        {{ $suratKeluar->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Tambahkan margin untuk card */
.card.mx-3 {
    margin-left: 1rem;
    margin-right: 1rem;
}

/* Style untuk dropdown aktif */
.dropdown-item.active {
    background-color: #0d6efd;
    color: white;
}

/* Style untuk tombol aksi */
.btn-group .btn {
    margin: 0 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card.mx-3 {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
    
    .btn-group .btn {
        margin: 1px;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Konfirmasi hapus
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const itemName = this.getAttribute('data-item-name');
            
            if (confirm(`Apakah Anda yakin ingin menghapus ${itemName} ini?`)) {
                form.submit();
            }
        });
    });
});

// Highlight dropdown item aktif
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    
    dropdownItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
});
</script>
@endpush