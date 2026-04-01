@extends('admin.layouts.app')

@section('title', 'Laporan Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Laporan Booking</h2>
                <p class="text-muted">
                    Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.booking.pdf', $request->all()) }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf me-2"></i>
                    Export PDF
                </a>
                <a href="{{ route('admin.reports.booking.excel', $request->all()) }}" class="btn btn-success">
                    <i class="bi bi-file-excel me-2"></i>
                    Export Excel
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
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
                <small>Member</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['regular_bookings'] }}</h3>
                <small>Regular</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['completed_bookings'] }}</h3>
                <small>Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['cancelled_bookings'] }}</h3>
                <small>Dibatalkan</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                <small>Revenue Regular</small>
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
                            <th>Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                            <td>
                                @if($booking->is_membership)
                                    <span class="badge bg-warning text-dark">👑 Member</span>
                                @else
                                    <span class="badge bg-info">🎯 Regular</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                </div>
                            </td>
                            <td>{{ $booking->field->name }}</td>
                            <td>{{ $booking->field->branch->name }}</td>
                            <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td>
                                @if($booking->is_membership)
                                    <span class="text-muted">Bulanan</span>
                                @else
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>{!! $booking->status_badge !!}</td>
                            <td>{{ $booking->user->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data booking</h5>
                <p class="text-muted">Tidak ada booking pada periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
@endsection