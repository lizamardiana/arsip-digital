@extends('layouts.app')

@section('title', 'Tambah Surat Masuk')
@section('page-title', 'Tambah Surat Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surat-masuk.index') }}">Surat Masuk</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('page-actions')
    <a href="{{ route('surat-masuk.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection

@section('content')
<div class="row mx-3"> <!-- Tambahkan margin kiri kanan -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                                <label for="tanggal_terima" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_terima" name="tanggal_terima" 
                                       value="{{ old('tanggal_terima', date('Y-m-d')) }}" required>
                                <div class="invalid-feedback">Harap isi tanggal terima.</div>
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
                        <label for="pengirim" class="form-label">Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pengirim" name="pengirim" 
                               value="{{ old('pengirim') }}" required>
                        <div class="invalid-feedback">Harap isi pengirim.</div>
                    </div>

                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="perihal" name="perihal" 
                               value="{{ old('perihal') }}" required>
                        <div class="invalid-feedback">Harap isi perihal.</div>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="ringkasan" name="ringkasan" rows="3">{{ old('ringkasan') }}</textarea>
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