@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen User</li>
@endsection

@section('page-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah User
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->id == auth()->id())
                                        <span class="badge bg-info">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role == 'staff')
                                        <span class="badge bg-warning">Staff</span>
                                    @else
                                        <span class="badge bg-info">User</span>
                                    @endif
                                </td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id != auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-delete" 
                                                    title="Hapus" data-item-name="user {{ $user->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-users fa-2x mb-3"></i><br>
                                    Tidak ada data user
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection