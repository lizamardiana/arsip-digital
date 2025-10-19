@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile User')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>
                <h4>{{ auth()->user()->name }}</h4>
                <p class="text-muted">{{ auth()->user()->email }}</p>
                <span class="badge 
                    @if(auth()->user()->role == 'admin') bg-danger
                    @elseif(auth()->user()->role == 'staff') bg-warning
                    @else bg-info @endif">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Profile</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', auth()->user()->name) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', auth()->user()->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone', auth()->user()->phone) }}">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>

                <hr>

                <h6>Ubah Password</h6>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection