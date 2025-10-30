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
                                       value="{{ old('nomor_surat') }}" required placeholder="Masukkan nomor surat">
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
                               value="{{ old('pengirim') }}" required placeholder="Masukkan nama pengirim">
                        <div class="invalid-feedback">Harap isi pengirim.</div>
                    </div>

                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="perihal" name="perihal" 
                               value="{{ old('perihal') }}" required placeholder="Masukkan perihal surat">
                        <div class="invalid-feedback">Harap isi perihal.</div>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="ringkasan" name="ringkasan" rows="3" placeholder="Masukkan ringkasan surat">{{ old('ringkasan') }}</textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">
                            <i class="fas fa-redo me-2"></i>Atur Ulang
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

/* Styling untuk form */
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #5a5c69;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn {
    border-radius: 0.35rem;
    font-weight: 600;
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

    // Validasi file size
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    alert('Ukuran file maksimal 5MB');
                    this.value = '';
                }
            }
        });
    }

    // Set tanggal minimum untuk input tanggal
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_surat').max = today;
    document.getElementById('tanggal_terima').max = today;
});
</script>
@endpush