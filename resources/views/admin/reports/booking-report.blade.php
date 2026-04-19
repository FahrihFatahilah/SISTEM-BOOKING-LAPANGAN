@extends('admin.layouts.app')

@section('title', 'Laporan Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Laporan Booking</h2>
                <p class="text-muted">
                    Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d F Y') }} -
                    {{ \Carbon\Carbon::parse($request->end_date)->format('d F Y') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.booking.pdf', $request->all()) }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf me-2"></i> Export PDF
                </a>
                <a href="{{ route('admin.reports.booking.excel', $request->all()) }}" class="btn btn-success">
                    <i class="bi bi-file-excel me-2"></i> Export Excel
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.booking') }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $request->start_date }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $request->end_date }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Semua</option>
                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ongoing" {{ $request->status == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                        <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select class="form-select" name="booking_type">
                        <option value="">Semua</option>
                        <option value="regular" {{ $request->booking_type == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="member" {{ $request->booking_type == 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>
                @if(auth()->user()->isOwner())
                <div class="col-md-2">
                    <label class="form-label">Cabang</label>
                    <select class="form-select" name="branch_id">
                        <option value="">Semua</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $request->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['total_bookings'] }}</h3>
                <small>Total Booking</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['member_bookings'] }}</h3>
                <small>👑 Member</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['regular_bookings'] }}</h3>
                <small>🎯 Regular</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($summary['regular_revenue'], 0, ',', '.') }}</h3>
                <small>Revenue Regular</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($summary['member_revenue'], 0, ',', '.') }}</h3>
                <small>Revenue Member</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                <small>Total Revenue</small>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <span class="badge bg-warning mb-1">Pending</span>
                <h5 class="mb-0">{{ $summary['pending_bookings'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <span class="badge bg-success mb-1">Berjalan</span>
                <h5 class="mb-0">{{ $summary['ongoing_bookings'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <span class="badge bg-secondary mb-1">Selesai</span>
                <h5 class="mb-0">{{ $summary['completed_bookings'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <span class="badge bg-danger mb-1">Dibatalkan</span>
                <h5 class="mb-0">{{ $summary['cancelled_bookings'] }}</h5>
            </div>
        </div>
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
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Cabang</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_date->format('d F Y') }}</td>
                            <td>
                                @if($booking->is_membership)
                                    <span class="badge bg-warning text-dark">👑 Member</span>
                                @else
                                    <span class="badge bg-info">🎯 Regular</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                @if($booking->customer_phone)
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                @endif
                            </td>
                            <td>{{ $booking->field->name }}</td>
                            <td><small class="text-muted">{{ $booking->field->branch->name }}</small></td>
                            <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td>
                                @if($booking->is_membership)
                                    <span class="fw-semibold text-warning">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                    <br><small class="text-muted">per sesi</small>
                                @else
                                    <span class="fw-semibold text-success">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td>{!! $booking->status_badge !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data booking</h5>
                <p class="text-muted">Coba ubah filter atau periode tanggal</p>
            </div>
        @endif
    </div>
</div>
@endsection
