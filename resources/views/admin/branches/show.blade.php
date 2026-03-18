@extends('admin.layouts.app')

@section('title', 'Detail Cabang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">{{ $branch->name }}</h2>
                <p class="text-muted">Detail informasi cabang</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nama Cabang</h6>
                        <p class="fw-semibold">{{ $branch->name }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <span class="badge {{ $branch->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Alamat</h6>
                        <p>{{ $branch->address }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Telepon</h6>
                        <p>{{ $branch->phone ?: '-' }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Jam Operasional</h6>
                        <p>{{ $branch->open_time }} - {{ $branch->close_time }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fields -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Lapangan</h5>
            </div>
            <div class="card-body">
                @if($branch->fields->count() > 0)
                    <div class="row g-3">
                        @foreach($branch->fields as $field)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="fw-semibold">{{ $field->name }}</h6>
                                <p class="text-muted small mb-2">{{ $field->description }}</p>
                                <div class="d-flex justify-content-between">
                                    <span class="text-success fw-semibold">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}/jam</span>
                                    <span class="badge {{ $field->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $field->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Belum ada lapangan</p>
                @endif
            </div>
        </div>
        
        <!-- Users -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Staff</h5>
            </div>
            <div class="card-body">
                @if($branch->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branch->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Belum ada staff</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $branch->fields->count() }}</h4>
                        <small class="text-muted">Lapangan</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $branch->users->count() }}</h4>
                        <small class="text-muted">Staff</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection