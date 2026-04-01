@extends('admin.layouts.app')

@section('title', 'Paket Membership')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Paket Membership</h2>
                <p class="text-muted">Kelola paket membership bulanan</p>
            </div>
            <a href="{{ route('admin.membership-packages.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Paket
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($packages as $package)
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-1">{{ $package->name }}</h5>
                        <span class="badge {{ $package->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.membership-packages.edit', $package) }}">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.membership-packages.destroy', $package) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" 
                                            onclick="return confirm('Yakin ingin menghapus paket ini?')">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($package->description)
                <div class="mb-3">
                    <small class="text-muted">{{ $package->description }}</small>
                </div>
                @endif
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Harga:</span>
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Durasi:</span>
                        <span>{{ $package->duration_days }} hari</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Sesi per minggu:</span>
                        <span>{{ $package->sessions_per_week }}x</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Durasi sesi:</span>
                        <span>{{ $package->session_duration_hours }} jam</span>
                    </div>
                </div>
                
                <div class="row text-center mt-4">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">
                                {{ $package->userMemberships()->where('status', 'active')->count() }}
                            </h4>
                            <small class="text-muted">Member Aktif</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">
                            {{ $package->userMemberships()->count() }}
                        </h4>
                        <small class="text-muted">Total Member</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.membership-packages.edit', $package) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-card-checklist text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada paket membership</h5>
                <p class="text-muted">Silakan tambah paket membership untuk memulai</p>
                <a href="{{ route('admin.membership-packages.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Paket Pertama
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($packages->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $packages->links() }}
</div>
@endif
@endsection