@extends('admin.layouts.app')

@section('title', 'Daftar Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Daftar Booking</h2>
                <p class="text-muted">Kelola semua booking lapangan</p>
            </div>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Booking Baru
            </a>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                @if(auth()->user()->isOwner())
                <div class="col-md-3">
                    <label class="form-label">Cabang</label>
                    <select class="form-select" name="branch_id">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Booking Table -->
<div class="card">
    <div class="card-body">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Cabang</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $booking->id }}</span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->field->name }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $booking->field->branch->name }}</small>
                            </td>
                            <td>{{ $booking->booking_date->format('d F Y') }}</td>
                            <td>
                                <span class="badge bg-info">{{ $booking->start_time }} - {{ $booking->end_time }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </td>
                            <td>{!! $booking->status_badge !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(!auth()->user()->isStaff())
                                    <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus booking ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }} booking
                </div>
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada booking ditemukan</h5>
                <p class="text-muted">Silakan buat booking baru atau ubah filter pencarian</p>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Buat Booking Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection