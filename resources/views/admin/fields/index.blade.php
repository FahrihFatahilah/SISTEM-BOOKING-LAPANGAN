@extends('admin.layouts.app')

@section('title', 'Manajemen Lapangan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Manajemen Lapangan</h2>
                <p class="text-muted">Kelola lapangan di semua cabang</p>
            </div>
            @can('create fields')
            <a href="{{ route('admin.fields.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Lapangan
            </a>
            @endcan
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($fields as $field)
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-1">{{ $field->name }}</h5>
                        <span class="badge {{ $field->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $field->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.fields.show', $field) }}">
                                <i class="bi bi-eye me-2"></i>Detail
                            </a></li>
                            @can('edit fields')
                            <li><a class="dropdown-item" href="{{ route('admin.fields.edit', $field) }}">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a></li>
                            @endcan
                            @can('delete fields')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.fields.destroy', $field) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" 
                                            onclick="return confirm('Yakin ingin menghapus lapangan ini?')">
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
                    <i class="bi bi-building text-muted me-2"></i>
                    <small class="text-muted">{{ $field->branch->name }}</small>
                </div>
                
                @if($field->description)
                <div class="mb-3">
                    <small class="text-muted">{{ $field->description }}</small>
                </div>
                @endif
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga per jam:</span>
                        <span class="fw-bold text-success">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="row text-center mt-4">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">
                                {{ $field->bookings()->whereDate('booking_date', today())->count() }}
                            </h4>
                            <small class="text-muted">Booking Hari Ini</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">
                            {{ $field->bookings()->where('status', 'completed')->whereMonth('created_at', now()->month)->count() }}
                        </h4>
                        <small class="text-muted">Selesai Bulan Ini</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.fields.show', $field) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>
                        Detail
                    </a>
                    @can('edit fields')
                    <a href="{{ route('admin.fields.edit', $field) }}" class="btn btn-primary btn-sm">
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
                <i class="bi bi-grid text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada lapangan</h5>
                <p class="text-muted">Silakan tambah lapangan baru untuk memulai</p>
                @can('create fields')
                <a href="{{ route('admin.fields.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Lapangan Pertama
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($fields->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $fields->links() }}
</div>
@endif
@endsection