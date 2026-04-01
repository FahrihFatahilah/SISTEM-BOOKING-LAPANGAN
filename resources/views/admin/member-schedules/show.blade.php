@extends('admin.layouts.app')

@section('title', 'Detail Jadwal Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Detail Jadwal Member</h2>
                <p class="text-muted">{{ $memberSchedule->member_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.member-schedules.edit', $memberSchedule) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.member-schedules.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    Informasi Member
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Nama</label>
                        <div class="fw-semibold">{{ $memberSchedule->member_name }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Telepon</label>
                        <div class="fw-semibold">{{ $memberSchedule->member_phone }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Lapangan</label>
                        <div class="fw-semibold">{{ $memberSchedule->field->name }}</div>
                        <small class="text-muted">{{ $memberSchedule->field->branch->name }}</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Hari</label>
                        <div><span class="badge bg-info">{{ $memberSchedule->day_name }}</span></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Waktu</label>
                        <div><span class="badge bg-secondary">{{ $memberSchedule->start_time }} - {{ $memberSchedule->end_time }}</span></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Harga Bulanan</label>
                        <div class="fw-semibold text-success fs-5">Rp {{ number_format($memberSchedule->monthly_price, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Kuota Bulan Ini</label>
                        @php
                            $remaining = $memberSchedule->getRemainingQuota();
                            $used = 4 - $remaining;
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="progress me-2" style="width: 100px; height: 12px;">
                                <div class="progress-bar {{ $remaining > 1 ? 'bg-success' : ($remaining == 1 ? 'bg-warning' : 'bg-danger') }}" 
                                     style="width: {{ ($used/4)*100 }}%"></div>
                            </div>
                            <span class="fw-semibold {{ $remaining > 1 ? 'text-success' : ($remaining == 1 ? 'text-warning' : 'text-danger') }}">
                                {{ $used }}/4 terpakai
                            </span>
                        </div>
                        <small class="text-muted">Sisa {{ $remaining }} sesi</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Mulai</label>
                        <div class="fw-semibold">{{ $memberSchedule->start_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @if($memberSchedule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                    @if($memberSchedule->notes)
                    <div class="col-12">
                        <label class="form-label text-muted">Catatan</label>
                        <div class="fw-semibold">{{ $memberSchedule->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($memberSchedule->is_active)
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Aksi
                </h6>
            </div>
            <div class="card-body">
                @if($memberSchedule->is_active)
                <form method="POST" action="{{ route('admin.member-schedules.generate', $memberSchedule) }}" class="mb-3">
                    @csrf
                    @php $remaining = $memberSchedule->getRemainingQuota(); @endphp
                    <button type="submit" class="btn btn-success w-100" {{ $remaining <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Generate Sesi Berikutnya
                        @if($remaining <= 0)
                            <br><small>(Kuota bulan ini habis)</small>
                        @else
                            <br><small>(Maks {{ $remaining }} sesi lagi)</small>
                        @endif
                    </button>
                </form>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <small><strong>Sistem Kuota Member:</strong><br>
                    • Setiap member maksimal 4 sesi per bulan<br>
                    • Generate akan berhenti otomatis saat kuota habis<br>
                    • Kuota reset setiap awal bulan</small>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Booking Mendatang (30 Hari)
                </h6>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $booking->booking_date->locale('id')->dayName }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $booking->start_time }} - {{ $booking->end_time }}
                                        </span>
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
                        <h6 class="text-muted mt-3">Belum ada booking yang di-generate</h6>
                        <p class="text-muted">Klik tombol "Generate 30 Hari Ke Depan" untuk membuat booking otomatis</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection