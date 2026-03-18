@extends('admin.layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Laporan & Analisis</h2>
        <p class="text-muted">Lihat laporan booking dan pendapatan</p>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1">Booking Bulan Ini</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\Booking::whereMonth('booking_date', now()->month)->count() }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-calendar-month fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1">Pendapatan Bulan Ini</h6>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format(\App\Models\Booking::whereMonth('booking_date', now()->month)->where('status', 'completed')->sum('total_price'), 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1">Rata-rata per Hari</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format(\App\Models\Booking::whereMonth('booking_date', now()->month)->count() / now()->day, 1) }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-graph-up fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1">Tingkat Penyelesaian</h6>
                        <h3 class="mb-0 fw-bold">
                            @php
                                $total = \App\Models\Booking::whereMonth('booking_date', now()->month)->count();
                                $completed = \App\Models\Booking::whereMonth('booking_date', now()->month)->where('status', 'completed')->count();
                                $rate = $total > 0 ? ($completed / $total) * 100 : 0;
                            @endphp
                            {{ number_format($rate, 1) }}%
                        </h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Options -->
<div class="row g-4">
    <!-- Laporan Booking -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Laporan Booking
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Generate laporan booking berdasarkan periode, cabang, atau lapangan tertentu</p>
                
                <form method="GET" action="{{ route('admin.reports.booking') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        @if(auth()->user()->isOwner())
                        <div class="col-12">
                            <label class="form-label">Cabang</label>
                            <select class="form-select" name="branch_id">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Lihat Laporan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Laporan Pendapatan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Laporan Pendapatan
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Analisis pendapatan harian atau bulanan dengan grafik dan tabel</p>
                
                <form method="GET" action="{{ route('admin.reports.revenue') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Laporan</label>
                            <select class="form-select" name="type" required>
                                <option value="daily">Harian</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>
                        @if(auth()->user()->isOwner())
                        <div class="col-md-6">
                            <label class="form-label">Cabang</label>
                            <select class="form-select" name="branch_id">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-bar-chart me-2"></i>
                            Lihat Laporan Pendapatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Export -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="bi bi-download me-2"></i>
                    Export Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking.pdf', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-danger">
                                <i class="bi bi-file-pdf me-2"></i>
                                Export PDF Bulan Ini
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking.excel', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-success">
                                <i class="bi bi-file-excel me-2"></i>
                                Export Excel Bulan Ini
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>
                                Lihat Laporan Hari Ini
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection