@extends('admin.layouts.app')

@section('title', 'Laporan')

@section('content')
<style>
    .stat-card {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .stat-card .card-body {
        min-height: 145px;
    }

    .stat-label {
        color: rgba(255, 255, 255, .78);
        font-size: .9rem;
        font-weight: 600;
        letter-spacing: .2px;
    }

    .stat-value {
        color: #fff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .25);
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, .22);
        color: #fff;
        font-size: 1.6rem;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.2);
    }

    .report-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .07);
        overflow: hidden;
    }

    .report-card .card-header {
        border-bottom: 0;
        padding: 1rem 1.25rem;
    }

    .quick-export-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .07);
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark mb-1">Laporan & Analisis</h2>
        <p class="text-muted mb-0">Lihat laporan booking dan pendapatan</p>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <div class="stat-label mb-2">Booking Bulan Ini</div>
                        <h3 class="mb-0 fw-bold stat-value">
                            {{ \App\Models\Booking::whereMonth('booking_date', now()->month)->count() }}
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <div class="stat-label mb-2">Pendapatan Booking</div>
                        <h3 class="mb-0 fw-bold stat-value">
                            Rp {{ number_format(\App\Models\Booking::whereMonth('booking_date', now()->month)->whereIn('status', ['pending', 'ongoing', 'completed'])->sum('total_price'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00c6ff 100%);">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <div class="stat-label mb-2">Rata-rata per Hari</div>
                        <h3 class="mb-0 fw-bold stat-value">
                            {{ number_format(\App\Models\Booking::whereMonth('booking_date', now()->month)->count() / now()->day, 1) }}
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body text-white" style="background: linear-gradient(135deg, #fa709a 0%, #f6b93b 100%);">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <div class="stat-label mb-2">Tingkat Penyelesaian</div>
                        <h3 class="mb-0 fw-bold stat-value">
                            @php
                                $total = \App\Models\Booking::whereMonth('booking_date', now()->month)->count();
                                $completed = \App\Models\Booking::whereMonth('booking_date', now()->month)->where('status', 'completed')->count();
                                $rate = $total > 0 ? ($completed / $total) * 100 : 0;
                            @endphp
                            {{ number_format($rate, 1) }}%
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
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
        <div class="card report-card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Laporan Booking
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted">Generate laporan booking berdasarkan periode, cabang, atau lapangan tertentu</p>
                
                <form method="GET" action="{{ route('admin.reports.booking') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        @if(auth()->user()->isOwner())
                        <div class="col-12">
                            <label class="form-label fw-semibold">Cabang</label>
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
                        <button type="submit" class="btn btn-primary btn-lg">
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
        <div class="card report-card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Laporan Pendapatan
                </h5>
            </div>

            <div class="card-body p-4">
                <p class="text-muted">Analisis pendapatan harian atau bulanan dengan grafik dan tabel</p>
                
                <form method="GET" action="{{ route('admin.reports.revenue') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipe Laporan</label>
                            <select class="form-select" name="type" required>
                                <option value="daily">Harian</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        @if(auth()->user()->isOwner())
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cabang</label>
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
                        <button type="submit" class="btn btn-success btn-lg">
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
        <div class="card quick-export-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="card-title mb-0 text-dark">
                    <i class="bi bi-download me-2"></i>
                    Export Cepat
                </h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking.pdf', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-danger btn-lg">
                                <i class="bi bi-file-pdf me-2"></i>
                                Export PDF Bulan Ini
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking.excel', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-success btn-lg">
                                <i class="bi bi-file-excel me-2"></i>
                                Export Excel Bulan Ini
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.reports.booking', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" 
                               class="btn btn-outline-primary btn-lg">
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