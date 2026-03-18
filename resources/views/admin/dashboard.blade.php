@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Dashboard</h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}! 
            @if(auth()->user()->branch)
                <span class="badge bg-primary">{{ auth()->user()->branch->name }}</span>
            @endif
        </p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1">Booking Hari Ini</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_bookings_today'] }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-calendar-event fs-4"></i>
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
                        <h6 class="card-title text-white-50 mb-1">Sedang Berjalan</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['ongoing_bookings'] }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-play-circle fs-4"></i>
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
                        <h6 class="card-title text-white-50 mb-1">Selesai Hari Ini</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['completed_bookings_today'] }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-check-circle fs-4"></i>
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
                        <h6 class="card-title text-white-50 mb-1">Pendapatan Hari Ini</h6>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($stats['total_revenue_today'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Bookings -->
<div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-charge text-primary me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Booking Baru
                    </a>
                    
                    <a href="{{ route('admin.live-booking.index') }}" class="btn btn-success">
                        <i class="bi bi-broadcast me-2"></i>
                        Live Booking
                        <span class="live-indicator ms-2"></span>
                    </a>
                    
                    @can('view reports')
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-info">
                        <i class="bi bi-graph-up me-2"></i>
                        Lihat Laporan
                    </a>
                    @endcan
                </div>
                
                <hr class="my-4">
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ $stats['total_branches'] }}</h4>
                            <small class="text-muted">Cabang</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">{{ $stats['total_fields'] }}</h4>
                        <small class="text-muted">Lapangan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Booking Terbaru
                    </h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($recentBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Lapangan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                            <small class="text-muted">{{ $booking->customer_phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $booking->field->name }}</div>
                                            <small class="text-muted">{{ $booking->field->branch->name }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                                    <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                                    <td>{!! $booking->status_badge !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Belum ada booking terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto refresh dashboard setiap 30 detik
    setInterval(function() {
        // Refresh hanya bagian statistik
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                // Update statistik cards jika diperlukan
                console.log('Dashboard refreshed');
            })
            .catch(error => console.log('Refresh error:', error));
    }, 30000);
</script>
@endpush