@extends('admin.layouts.app')

@section('title', 'Live Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">
                    <i class="bi bi-broadcast text-danger me-2"></i>
                    Live Booking Hari Ini
                    <span class="live-indicator ms-2"></span>
                </h2>
                <p class="text-muted">Monitoring booking real-time - {{ now()->format('d F Y') }}</p>
            </div>
            <div class="text-end">
                <div class="badge bg-success fs-6 mb-2">
                    <i class="bi bi-clock me-1"></i>
                    <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                </div>
                <br>
                <button class="btn btn-primary btn-sm" onclick="refreshData()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;"></span>
                    Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Live Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event fs-1 mb-2"></i>
                <h4 class="mb-0" id="totalBookings">{{ $todayBookings->count() }}</h4>
                <small>Total Booking</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-clock fs-1 mb-2"></i>
                <h4 class="mb-0" id="pendingBookings">{{ $todayBookings->where('status', 'pending')->count() }}</h4>
                <small>Akan Datang</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-play-circle fs-1 mb-2"></i>
                <h4 class="mb-0" id="ongoingBookings">{{ $todayBookings->where('status', 'ongoing')->count() }}</h4>
                <small>Sedang Berjalan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-secondary text-white">
            <div class="card-body text-center">
                <i class="bi bi-check-circle fs-1 mb-2"></i>
                <h4 class="mb-0" id="completedBookings">{{ $todayBookings->where('status', 'completed')->count() }}</h4>
                <small>Selesai</small>
            </div>
        </div>
    </div>
</div>

<!-- Live Booking Table -->
<div class="card">
    <div class="card-header bg-transparent border-0">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Daftar Booking Hari Ini
        </h5>
    </div>
    <div class="card-body">
        @if($todayBookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="bookingTable">
                    <thead class="table-light">
                        <tr>
                            <th>Lapangan</th>
                            <th>Cabang</th>
                            <th>Pelanggan</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Sisa Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        @foreach($todayBookings as $booking)
                        <tr class="booking-row" data-id="{{ $booking->id }}">
                            <td>
                                <div class="fw-semibold">{{ $booking->field->name }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $booking->field->branch->name }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->customer_name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $booking->start_time }} - {{ $booking->end_time }}</span>
                            </td>
                            <td>{!! $booking->status_badge !!}</td>
                            <td>
                                <small class="text-muted">-</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="openStatusModal({{ $booking->id }}, '{{ $booking->status }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">Tidak ada booking hari ini</p>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Buat Booking Baru
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Notification Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="notificationContainer"></div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="bookingId">
                    <div class="mb-3">
                        <label class="form-label">Status Baru</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="pending">🔵 Akan Datang</option>
                            <option value="ongoing">🟢 Sedang Berjalan</option>
                            <option value="completed">🔴 Selesai</option>
                            <option value="cancelled">❌ Dibatalkan</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="updateStatus()">Update Status</button>
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
</style>
@endpush

@push('scripts')
<script>
let refreshInterval;
let lastBookingData = [];

document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

function startAutoRefresh() {
    refreshInterval = setInterval(refreshData, 10000);
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

function refreshData() {
    document.getElementById('loadingSpinner').style.display = 'inline-block';
    
    fetch('{{ route("admin.live-booking.data") }}')
        .then(response => response.json())
        .then(data => {
            updateBookingTable(data.bookings);
            updateStats(data.bookings);
            document.getElementById('lastUpdated').textContent = data.last_updated;
            checkForNotifications(data.bookings);
            document.getElementById('loadingSpinner').style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingSpinner').style.display = 'none';
            showNotification('Error memuat data', 'danger');
        });
}

function updateBookingTable(bookings) {
    const tbody = document.getElementById('bookingTableBody');
    
    if (bookings.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Tidak ada booking hari ini</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    bookings.forEach(booking => {
        html += `
            <tr class="booking-row" data-id="${booking.id}">
                <td><div class="fw-semibold">${booking.field_name}</div></td>
                <td><small class="text-muted">${booking.branch_name}</small></td>
                <td><div class="fw-semibold">${booking.customer_name}</div></td>
                <td><span class="badge bg-light text-dark">${booking.start_time} - ${booking.end_time}</span></td>
                <td>${booking.status_badge}</td>
                <td><small class="text-muted">${booking.time_remaining}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="openStatusModal(${booking.id}, '${booking.status}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updateStats(bookings) {
    const stats = {
        total: bookings.length,
        pending: bookings.filter(b => b.status === 'pending').length,
        ongoing: bookings.filter(b => b.status === 'ongoing').length,
        completed: bookings.filter(b => b.status === 'completed').length
    };
    
    document.getElementById('totalBookings').textContent = stats.total;
    document.getElementById('pendingBookings').textContent = stats.pending;
    document.getElementById('ongoingBookings').textContent = stats.ongoing;
    document.getElementById('completedBookings').textContent = stats.completed;
}

function checkForNotifications(currentBookings) {
    const newlyCompleted = currentBookings.filter(current => {
        const previous = lastBookingData.find(prev => prev.id === current.id);
        return previous && previous.status !== 'completed' && current.status === 'completed';
    });
    
    newlyCompleted.forEach(booking => {
        showNotification(`Booking ${booking.field_name} telah selesai!`, 'success', `Pelanggan: ${booking.customer_name}`);
    });
    
    lastBookingData = currentBookings;
}

function showNotification(message, type = 'info', subtitle = '') {
    const container = document.getElementById('notificationContainer');
    const id = 'notification-' + Date.now();
    
    const notification = document.createElement('div');
    notification.id = id;
    notification.className = `toast align-items-center text-white bg-${type} border-0`;
    notification.setAttribute('role', 'alert');
    
    notification.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${message}</strong>
                ${subtitle ? `<br><small>${subtitle}</small>` : ''}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(notification);
    
    const toast = new bootstrap.Toast(notification, {
        autohide: true,
        delay: 5000
    });
    
    toast.show();
    
    notification.addEventListener('hidden.bs.toast', function() {
        notification.remove();
    });
}

function openStatusModal(bookingId, currentStatus) {
    document.getElementById('bookingId').value = bookingId;
    document.getElementById('newStatus').value = currentStatus;
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function updateStatus() {
    const bookingId = document.getElementById('bookingId').value;
    const newStatus = document.getElementById('newStatus').value;
    
    if (!bookingId || !newStatus) {
        showNotification('Data tidak lengkap', 'danger');
        return;
    }
    
    fetch(`{{ url('admin/live-booking') }}/${bookingId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status berhasil diupdate', 'success');
            refreshData();
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
        } else {
            showNotification(data.message || 'Gagal mengupdate status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'danger');
    });
}
</script>
@endpush