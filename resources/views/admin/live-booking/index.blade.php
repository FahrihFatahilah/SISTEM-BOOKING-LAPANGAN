@extends('admin.layouts.app')

@section('title', 'Live Booking - Jadwal Seminggu')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">
                    <i class="bi bi-calendar-week text-primary me-2"></i>
                    Jadwal Booking Seminggu
                    <span class="live-indicator ms-2"></span>
                </h2>
                <p class="text-muted">{{ $startOfWeek->format('d M') }} - {{ $endOfWeek->format('d M Y') }}</p>
            </div>
            <div class="text-end">
                <div class="badge bg-success fs-6 mb-2">
                    <i class="bi bi-clock me-1"></i>
                    <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                </div>
                <br>
                <button class="btn btn-primary btn-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Schedule Calendar -->
<div class="card">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="bi bi-calendar3 me-2"></i>
            Kalender Booking Mingguan
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="min-width: 1200px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Lapangan</th>
                        @foreach($schedule as $dayData)
                        <th class="text-center" style="width: 150px;">
                            <div class="fw-bold">{{ $dayData['day_name'] }}</div>
                            <small class="text-muted">{{ $dayData['date']->format('d M') }}</small>
                            @if($dayData['date']->isToday())
                                <span class="badge bg-primary ms-1">Hari Ini</span>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($fields as $field)
                    <tr>
                        <td class="align-middle bg-light">
                            <div class="fw-semibold">{{ $field->name }}</div>
                            <small class="text-muted">{{ $field->branch->name }}</small>
                        </td>
                        @foreach($schedule as $dayKey => $dayData)
                        <td class="p-2" style="vertical-align: top; min-height: 120px;">
                            @if(isset($dayData['fields'][$field->id]['bookings']) && count($dayData['fields'][$field->id]['bookings']) > 0)
                                @foreach($dayData['fields'][$field->id]['bookings'] as $booking)
                                <div class="booking-slot mb-2 p-2 rounded" 
                                     style="background: {{ $booking->is_membership ? '#fff3cd' : '#d1ecf1' }}; border-left: 4px solid {{ $booking->is_membership ? '#ffc107' : '#0dcaf0' }};">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="font-size: 0.85rem;">
                                                @if($booking->is_membership)
                                                    👑 {{ $booking->customer_name }}
                                                @else
                                                    🎯 {{ $booking->customer_name }}
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </div>
                                            <div class="mt-1">
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Pending</span>
                                                @elseif($booking->status == 'ongoing')
                                                    <span class="badge bg-success" style="font-size: 0.7rem;">Berjalan</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">Selesai</span>
                                                @else
                                                    <span class="badge bg-danger" style="font-size: 0.7rem;">Batal</span>
                                                @endif
                                                
                                                @if($booking->is_membership)
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Member</span>
                                                @else
                                                    <span class="badge bg-info" style="font-size: 0.7rem;">Regular</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-calendar-x"></i><br>
                                    Kosong
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mt-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event fs-1 mb-2"></i>
                <h4 class="mb-0">
                    @php
                        $totalBookings = 0;
                        foreach($schedule as $dayData) {
                            foreach($dayData['fields'] as $fieldData) {
                                $totalBookings += count($fieldData['bookings']);
                            }
                        }
                    @endphp
                    {{ $totalBookings }}
                </h4>
                <small>Total Booking Minggu Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-crown fs-1 mb-2"></i>
                <h4 class="mb-0">
                    @php
                        $memberBookings = 0;
                        foreach($schedule as $dayData) {
                            foreach($dayData['fields'] as $fieldData) {
                                $memberBookings += collect($fieldData['bookings'])->where('is_membership', true)->count();
                            }
                        }
                    @endphp
                    {{ $memberBookings }}
                </h4>
                <small>Booking Member</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-target fs-1 mb-2"></i>
                <h4 class="mb-0">{{ $totalBookings - $memberBookings }}</h4>
                <small>Booking Regular</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-cash fs-1 mb-2"></i>
                <h4 class="mb-0">
                    @php
                        $totalRevenue = 0;
                        foreach($schedule as $dayData) {
                            foreach($dayData['fields'] as $fieldData) {
                                foreach($fieldData['bookings'] as $booking) {
                                    if (!$booking->is_membership) {
                                        $totalRevenue += $booking->total_price;
                                    }
                                }
                            }
                        }
                    @endphp
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h4>
                <small>Revenue Regular</small>
            </div>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-4">
    <div class="card-body">
        <h6 class="card-title">Keterangan:</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-3" style="width: 20px; height: 20px; background: #fff3cd; border-left: 4px solid #ffc107;"></div>
                    <span>👑 <strong>Member:</strong> Booking membership bulanan</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="me-3" style="width: 20px; height: 20px; background: #d1ecf1; border-left: 4px solid #0dcaf0;"></div>
                    <span>🎯 <strong>Regular:</strong> Booking harian</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-warning text-dark me-2">Pending</span>
                    <span>Menunggu dimulai</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">Berjalan</span>
                    <span>Sedang berlangsung</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-secondary me-2">Selesai</span>
                    <span>Sudah selesai</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.live-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    background-color: #dc3545;
    border-radius: 50%;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.booking-slot {
    transition: all 0.3s ease;
    cursor: pointer;
}

.booking-slot:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table td {
    border-color: #dee2e6 !important;
}

.table th {
    border-color: #dee2e6 !important;
    background-color: #f8f9fa !important;
}
</style>
@endpush