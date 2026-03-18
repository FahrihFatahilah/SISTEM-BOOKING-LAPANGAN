@extends('admin.layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Detail Booking #{{ $booking->id }}</h2>
                <p class="text-muted">Informasi lengkap booking lapangan</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Booking
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Booking Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Booking
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Pelanggan</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-person text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $booking->customer_name }}</div>
                                <div class="text-muted">{{ $booking->customer_phone }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Lapangan</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-grid text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $booking->field->name }}</div>
                                <div class="text-muted">{{ $booking->field->branch->name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Tanggal & Waktu</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-calendar-event text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $booking->booking_date->format('d F Y') }}</div>
                                <div class="text-muted">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-flag text-warning fs-4"></i>
                            </div>
                            <div>
                                {!! $booking->status_badge !!}
                                <div class="text-muted small">
                                    @if($booking->status === 'pending')
                                        Menunggu waktu mulai
                                    @elseif($booking->status === 'ongoing')
                                        Sedang berlangsung
                                    @elseif($booking->status === 'completed')
                                        Booking telah selesai
                                    @else
                                        Booking dibatalkan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($booking->notes)
                <div class="mt-4">
                    <h6 class="text-muted mb-2">Catatan</h6>
                    <div class="bg-light rounded p-3">
                        <i class="bi bi-chat-text me-2"></i>
                        {{ $booking->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="card mt-4">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Timeline Booking
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Booking Dibuat</h6>
                            <p class="text-muted mb-0">{{ $booking->created_at->format('d F Y, H:i') }}</p>
                            <small class="text-muted">Oleh: {{ $booking->user->name }}</small>
                        </div>
                    </div>
                    
                    @if($booking->status !== 'pending')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Status Diupdate</h6>
                            <p class="text-muted mb-0">{{ $booking->updated_at->format('d F Y, H:i') }}</p>
                            <small class="text-muted">Status: {{ ucfirst($booking->status) }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Booking Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    Ringkasan Pembayaran
                </h5>
            </div>
            <div class="card-body">
                @php
                    $startTime = \Carbon\Carbon::parse($booking->start_time);
                    $endTime = \Carbon\Carbon::parse($booking->end_time);
                    $duration = $endTime->diffInHours($startTime);
                @endphp
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Lapangan:</span>
                            <span class="fw-semibold">{{ $booking->field->name }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Harga per jam:</span>
                            <span>Rp {{ number_format($booking->field->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Durasi:</span>
                            <span>{{ $duration }} jam</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr class="my-2">
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between fw-bold text-success fs-5">
                            <span>Total:</span>
                            <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning-charge me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($booking->status === 'pending')
                    <form method="POST" action="{{ route('admin.live-booking.update-status', $booking) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="ongoing">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-play-circle me-2"></i>
                            Mulai Booking
                        </button>
                    </form>
                    @endif
                    
                    @if($booking->status === 'ongoing')
                    <form method="POST" action="{{ route('admin.live-booking.update-status', $booking) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i>
                            Selesaikan Booking
                        </button>
                    </form>
                    @endif
                    
                    @if(in_array($booking->status, ['pending', 'ongoing']))
                    <form method="POST" action="{{ route('admin.live-booking.update-status', $booking) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                            <i class="bi bi-x-circle me-2"></i>
                            Batalkan Booking
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Booking
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Field Information -->
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Info Lapangan
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <small class="text-muted">Nama:</small>
                        <div class="fw-semibold">{{ $booking->field->name }}</div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Cabang:</small>
                        <div>{{ $booking->field->branch->name }}</div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Alamat:</small>
                        <div>{{ $booking->field->branch->address }}</div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Jam Operasional:</small>
                        <div>{{ $booking->field->branch->open_time }} - {{ $booking->field->branch->close_time }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}
</style>
@endsection