@extends('admin.layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Detail Member</h2>
                <p class="text-muted">{{ $userMembership->user->name }}</p>
            </div>
            <a href="{{ route('admin.user-memberships.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Info Member -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Member</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Nama</label>
                    <p class="fw-bold">{{ $userMembership->user->name }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p>{{ $userMembership->user->email }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Paket</label>
                    <p class="fw-bold">{{ $userMembership->membershipPackage->name }}</p>
                    <small class="text-muted">
                        {{ $userMembership->membershipPackage->sessions_per_week }}x/minggu, 
                        {{ $userMembership->membershipPackage->session_duration_hours }} jam
                    </small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Lapangan</label>
                    <p class="fw-bold">{{ $userMembership->field->name }}</p>
                    <small class="text-muted">{{ $userMembership->field->branch->name }}</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Periode</label>
                    <p>{{ $userMembership->start_date->format('d/m/Y') }} - {{ $userMembership->end_date->format('d/m/Y') }}</p>
                    @if($userMembership->end_date < now())
                        <span class="badge bg-danger">Expired</span>
                    @elseif($userMembership->end_date->diffInDays(now()) <= 7)
                        <span class="badge bg-warning">{{ $userMembership->end_date->diffInDays(now()) }} hari lagi</span>
                    @else
                        <span class="badge bg-success">{{ $userMembership->end_date->diffInDays(now()) }} hari lagi</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Jadwal Mingguan</label>
                    @php
                        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $scheduleDays = collect($userMembership->weekly_schedule)->map(fn($day) => $days[$day])->join(', ');
                    @endphp
                    <p class="fw-bold">{{ $scheduleDays }}</p>
                    <p>Jam: {{ \Carbon\Carbon::parse($userMembership->start_time)->format('H:i') }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Status</label>
                    <br>
                    @if($userMembership->status === 'active')
                        <span class="badge bg-success fs-6">Aktif</span>
                    @elseif($userMembership->status === 'expired')
                        <span class="badge bg-danger fs-6">Expired</span>
                    @else
                        <span class="badge bg-secondary fs-6">Dibatalkan</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jadwal Booking -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Jadwal Booking</h5>
                <div>
                    <span class="badge bg-info me-2">
                        Total: {{ $userMembership->membershipBookings->count() }} sesi
                    </span>
                    <span class="badge bg-success me-2">
                        Selesai: {{ $userMembership->membershipBookings->where('status', 'completed')->count() }}
                    </span>
                    <span class="badge bg-warning">
                        Terjadwal: {{ $userMembership->membershipBookings->where('status', 'scheduled')->count() }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userMembership->membershipBookings->sortBy('booking_date') as $booking)
                            <tr class="{{ $booking->booking_date->isPast() ? 'table-light' : '' }}">
                                <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->booking_date->locale('id')->dayName }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td>
                                    @if($booking->status === 'scheduled')
                                        @if($booking->booking_date->isToday())
                                            <span class="badge bg-primary">Hari Ini</span>
                                        @elseif($booking->booking_date->isFuture())
                                            <span class="badge bg-info">Terjadwal</span>
                                        @else
                                            <span class="badge bg-warning">Terlewat</span>
                                        @endif
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">No Show</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->status === 'scheduled' && $booking->booking_date->isFuture())
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="updateBookingStatus({{ $booking->id }}, 'completed')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <i class="bi bi-calendar-x text-muted"></i>
                                    <p class="text-muted mb-0">Belum ada jadwal booking</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateBookingStatus(bookingId, status) {
    if (!confirm('Yakin ingin mengubah status booking ini?')) return;
    
    fetch(`/admin/membership-bookings/${bookingId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengubah status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endsection