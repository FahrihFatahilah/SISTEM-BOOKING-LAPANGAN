@extends('admin.layouts.app')

@section('title', 'Manajemen Cabang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Manajemen Cabang</h2>
                <p class="text-muted">Kelola cabang dan lokasi lapangan</p>
            </div>
            @can('create branches')
            <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Cabang
            </a>
            @endcan
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($branches as $branch)
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-1">{{ $branch->name }}</h5>
                        <span class="badge {{ $branch->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.branches.show', $branch) }}">
                                <i class="bi bi-eye me-2"></i>Detail
                            </a></li>
                            @can('edit branches')
                            <li><a class="dropdown-item" href="{{ route('admin.branches.edit', $branch) }}">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a></li>
                            @endcan
                            @can('delete branches')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" 
                                            onclick="return confirm('Yakin ingin menghapus cabang ini?')">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-geo-alt text-muted me-2"></i>
                    <small class="text-muted">{{ $branch->address }}</small>
                </div>
                
                @if($branch->phone)
                <div class="mb-3">
                    <i class="bi bi-telephone text-muted me-2"></i>
                    <small class="text-muted">{{ $branch->phone }}</small>
                </div>
                @endif
                
                <div class="mb-3">
                    <i class="bi bi-clock text-muted me-2"></i>
                    <small class="text-muted">{{ $branch->open_time }} - {{ $branch->close_time }}</small>
                </div>
                
                <div class="row text-center mt-4">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ $branch->fields_count }}</h4>
                            <small class="text-muted">Lapangan</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success mb-1">{{ $branch->users_count }}</h4>
                            <small class="text-muted">Staff</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info mb-1">
                            {{ $branch->bookings()->whereDate('booking_date', today())->count() }}
                        </h4>
                        <small class="text-muted">Booking Hari Ini</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.branches.show', $branch) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>
                        Detail
                    </a>
                    @can('edit branches')
                    <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>
                        Edit
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada cabang</h5>
                <p class="text-muted">Silakan tambah cabang baru untuk memulai</p>
                @can('create branches')
                <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Cabang Pertama
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($branches->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $branches->links() }}
</div>
@endif
@endsection