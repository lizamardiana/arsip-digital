@extends('layouts.app')

@section('title', 'Tambah Surat Keluar')
@section('page-title', 'Tambah Surat Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surat-keluar.index') }}">Surat Keluar</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('page-actions')
    <a href="{{ route('surat-keluar.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection

@section('content')
<div class="row mx-3"> <!-- Tambahkan margin kiri kanan -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('surat-keluar.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" 
                                       value="{{ old('nomor_surat') }}" required>
                                <div class="invalid-feedback">Harap isi nomor surat.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" 
                                       value="{{ old('tanggal_surat') }}" required>
                                <div class="invalid-feedback">Harap isi tanggal surat.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_kirim" class="form-label">Tanggal Kirim <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" 
                                       value="{{ old('tanggal_kirim', date('Y-m-d')) }}" required>
                                <div class="invalid-feedback">Harap isi tanggal kirim.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file" class="form-label">File Surat</label>
                                <input type="file" class="form-control" id="file" name="file" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="form-text">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 5MB)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Tujuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tujuan" name="tujuan" 
                               value="{{ old('tujuan') }}" required>
                        <div class="invalid-feedback">Harap isi tujuan.</div>
                    </div>

                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="perihal" name="perihal" 
                               value="{{ old('perihal') }}" required>
                        <div class="invalid-feedback">Harap isi perihal.</div>
                    </div>

                    <div class="mb-3">
                        <label for="isi_ringkas" class="form-label">Isi Ringkas</label>
                        <textarea class="form-control" id="isi_ringkas" name="isi_ringkas" rows="3">{{ old('isi_ringkas') }}</textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

@push('scripts')
<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endpush