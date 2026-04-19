@extends('admin.layouts.app')

@section('title', 'Detail Lapangan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">{{ $field->name }}</h2>
                <p class="text-muted">{{ $field->branch->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.fields.edit', $field) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Info Lapangan -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Informasi Lapangan
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Nama</label>
                        <div class="fw-semibold">{{ $field->name }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Cabang</label>
                        <div class="fw-semibold">{{ $field->branch->name }}</div>
                    </div>
                    @if($field->description)
                    <div class="col-12">
                        <label class="form-label text-muted">Deskripsi</label>
                        <div>{{ $field->description }}</div>
                    </div>
                    @endif
                    <div class="col-6">
                        <label class="form-label text-muted">Harga Weekday/Jam</label>
                        <div class="fw-semibold text-success fs-5">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Harga Weekend/Jam</label>
                        @if($field->weekend_price_per_hour)
                            <div class="fw-semibold text-warning fs-5">Rp {{ number_format($field->weekend_price_per_hour, 0, ',', '.') }}</div>
                        @else
                            <div class="text-muted">Sama dengan weekday</div>
                        @endif
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            <span class="badge {{ $field->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $field->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Aktif/Nonaktif -->
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-toggles me-2"></i>Aksi
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.fields.toggle', $field) }}">
                    @csrf
                    @method('PATCH')
                    @if($field->is_active)
                        <button type="submit" class="btn btn-warning w-100"
                                onclick="return confirm('Yakin ingin menonaktifkan lapangan ini? Booking pending akan dibatalkan.')">
                            <i class="bi bi-pause-circle me-2"></i>Nonaktifkan Lapangan
                        </button>
                    @else
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-play-circle me-2"></i>Aktifkan Lapangan
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Statistik -->
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h4 class="text-primary mb-0">{{ $todayBookings }}</h4>
                            <small class="text-muted">Hari Ini</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h4 class="text-info mb-0">{{ $pendingBookings }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h4 class="text-success mb-0">{{ $completedThisMonth }}</h4>
                            <small class="text-muted">Selesai Bulan Ini</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h4 class="text-warning mb-0">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</h4>
                            <small class="text-muted">Pendapatan Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Terbaru -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>Booking Terbaru
                </h6>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Waktu</th>
                                    <th>Harga</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                        <small class="text-muted">{{ $booking->customer_phone }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($booking->is_membership)
                                            <span class="badge bg-purple" style="background-color: #7c3aed;">👑 Member</span>
                                        @else
                                            <span class="badge bg-info">🎯 Regular</span>
                                        @endif
                                    </td>
                                    <td>{!! $booking->status_badge !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-3">Belum ada booking</h6>
                    </div>
                @endif
            </div>
        </div>

        <!-- Member Schedules -->
        @if($memberSchedules->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Jadwal Member
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Member</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Harga/Bulan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberSchedules as $schedule)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.member-schedules.show', $schedule) }}" class="text-decoration-none">
                                        {{ $schedule->member_name }}
                                    </a>
                                </td>
                                <td><span class="badge bg-info">{{ $schedule->day_name }}</span></td>
                                <td><span class="badge bg-secondary">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span></td>
                                <td>Rp {{ number_format($schedule->monthly_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $schedule->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
