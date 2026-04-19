@extends('admin.layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Laporan Pendapatan</h2>
                <p class="text-muted">
                    Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d F Y') }} -
                    {{ \Carbon\Carbon::parse($request->end_date)->format('d F Y') }}
                    ({{ $request->type == 'daily' ? 'Harian' : 'Bulanan' }})
                </p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $request->start_date }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $request->end_date }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-select" required>
                    <option value="daily" {{ $request->type == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="monthly" {{ $request->type == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            @if(auth()->user()->isOwner())
            <div class="col-md-2">
                <label class="form-label">Cabang</label>
                <select name="branch_id" class="form-select">
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
        </form>
    </div>
</div>

<!-- Summary Cards -->
@php
    $totalBookingRegular = $bookingRevenues->sum('regular_revenue');
    $totalBookingMember = $bookingRevenues->sum('member_revenue');
    $totalBookingRevenue = $bookingRevenues->sum('total_revenue');
    $totalSalesRevenue = $salesRevenues instanceof \Illuminate\Support\Collection ? $salesRevenues->sum('sales_revenue') : 0;
    $totalAllRevenue = $totalBookingRevenue + $totalSalesRevenue;
    $totalBookings = $bookingRevenues->sum('total_bookings');
    $totalSales = $salesRevenues instanceof \Illuminate\Support\Collection ? $salesRevenues->sum('total_sales') : 0;
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <h5 class="mb-1">Rp {{ number_format($totalAllRevenue, 0, ',', '.') }}</h5>
                <small>Total Semua</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="mb-1">Rp {{ number_format($totalBookingRegular, 0, ',', '.') }}</h5>
                <small>🎯 Regular ({{ $bookingRevenues->sum('regular_bookings') }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h5 class="mb-1">Rp {{ number_format($totalBookingMember, 0, ',', '.') }}</h5>
                <small>👑 Member ({{ $bookingRevenues->sum('member_bookings') }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="mb-1">Rp {{ number_format($totalBookingRevenue, 0, ',', '.') }}</h5>
                <small>Total Booking ({{ $totalBookings }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="mb-1">Rp {{ number_format($totalSalesRevenue, 0, ',', '.') }}</h5>
                <small>Penjualan ({{ $totalSales }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h5 class="mb-1">{{ $totalBookings + $totalSales }}</h5>
                <small>Total Transaksi</small>
            </div>
        </div>
    </div>
</div>

<!-- Booking Revenue Table -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="bi bi-calendar-event me-2"></i>
            Pendapatan Booking {{ $request->type == 'daily' ? 'Harian' : 'Bulanan' }}
        </h5>
    </div>
    <div class="card-body">
        @if($bookingRevenues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $request->type == 'daily' ? 'Tanggal' : 'Bulan' }}</th>
                            <th>Total Booking</th>
                            <th>Regular</th>
                            <th>Member</th>
                            <th>Revenue Regular</th>
                            <th>Revenue Member</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookingRevenues as $revenue)
                        <tr>
                            <td>
                                @if($request->type == 'daily')
                                    {{ \Carbon\Carbon::parse($revenue->date)->format('d F Y') }}
                                @else
                                    {{ \Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}
                                @endif
                            </td>
                            <td><span class="badge bg-primary">{{ $revenue->total_bookings }}</span></td>
                            <td><span class="badge bg-info">{{ $revenue->regular_bookings }}</span></td>
                            <td><span class="badge bg-warning text-dark">{{ $revenue->member_bookings }}</span></td>
                            <td><span class="fw-semibold text-success">Rp {{ number_format($revenue->regular_revenue, 0, ',', '.') }}</span></td>
                            <td><span class="fw-semibold text-warning">Rp {{ number_format($revenue->member_revenue, 0, ',', '.') }}</span></td>
                            <td><span class="fw-bold">Rp {{ number_format($revenue->total_revenue, 0, ',', '.') }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th><span class="badge bg-primary">{{ $bookingRevenues->sum('total_bookings') }}</span></th>
                            <th><span class="badge bg-info">{{ $bookingRevenues->sum('regular_bookings') }}</span></th>
                            <th><span class="badge bg-warning text-dark">{{ $bookingRevenues->sum('member_bookings') }}</span></th>
                            <th><span class="fw-bold text-success">Rp {{ number_format($totalBookingRegular, 0, ',', '.') }}</span></th>
                            <th><span class="fw-bold text-warning">Rp {{ number_format($totalBookingMember, 0, ',', '.') }}</span></th>
                            <th><span class="fw-bold">Rp {{ number_format($totalBookingRevenue, 0, ',', '.') }}</span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Tidak ada data booking pada periode ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Sales Revenue Table -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="bi bi-cart me-2"></i>
            Pendapatan Penjualan (POS) {{ $request->type == 'daily' ? 'Harian' : 'Bulanan' }}
        </h5>
    </div>
    <div class="card-body">
        @if($salesRevenues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $request->type == 'daily' ? 'Tanggal' : 'Bulan' }}</th>
                            <th>Jumlah Transaksi</th>
                            <th>Revenue Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesRevenues as $sale)
                        <tr>
                            <td>
                                @if($request->type == 'daily')
                                    {{ \Carbon\Carbon::parse($sale->date)->format('d F Y') }}
                                @else
                                    {{ \Carbon\Carbon::create($sale->year, $sale->month)->format('F Y') }}
                                @endif
                            </td>
                            <td><span class="badge bg-success">{{ $sale->total_sales }}</span></td>
                            <td><span class="fw-semibold text-success">Rp {{ number_format($sale->sales_revenue, 0, ',', '.') }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th><span class="badge bg-success">{{ $salesRevenues->sum('total_sales') }}</span></th>
                            <th><span class="fw-bold text-success">Rp {{ number_format($totalSalesRevenue, 0, ',', '.') }}</span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Tidak ada data penjualan pada periode ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Info -->
<div class="card">
    <div class="card-body">
        <h6 class="card-title">Keterangan:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-info-circle text-info me-2"></i><strong>Revenue Booking:</strong> Pendapatan dari booking regular</li>
                    <li><i class="bi bi-crown text-warning me-2"></i><strong>Sesi Member:</strong> Member bayar bulanan, tidak dihitung per sesi</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-cart text-success me-2"></i><strong>Revenue Penjualan:</strong> Pendapatan dari POS/kasir</li>
                    <li><i class="bi bi-cash text-primary me-2"></i><strong>Total:</strong> Gabungan booking + penjualan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
