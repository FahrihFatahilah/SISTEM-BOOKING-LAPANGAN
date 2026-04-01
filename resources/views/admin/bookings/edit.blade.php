@extends('admin.layouts.app')

@section('title', 'Edit Booking')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Edit Booking #{{ $booking->id }}</h2>
                <p class="text-muted">Update informasi booking</p>
            </div>
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" id="editBookingForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tipe Booking</label>
                            <select class="form-select @error('booking_type') is-invalid @enderror" name="booking_type" required>
                                <option value="regular" {{ $booking->booking_type == 'regular' ? 'selected' : '' }}>Regular (Harian)</option>
                                <option value="member" {{ $booking->booking_type == 'member' ? 'selected' : '' }}>Member (Bulanan)</option>
                            </select>
                            @error('booking_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Lapangan</label>
                            <select class="form-select @error('field_id') is-invalid @enderror" name="field_id" id="editFieldSelect" required>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" 
                                            data-price="{{ $field->price_per_hour }}"
                                            {{ $booking->field_id == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }} - {{ $field->branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('field_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control @error('booking_date') is-invalid @enderror" 
                                   name="booking_date" value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}" 
                                   id="editBookingDate" required>
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="start_time" value="{{ old('start_time', $booking->start_time) }}" 
                                   id="editStartTime" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   name="end_time" value="{{ old('end_time', $booking->end_time) }}" 
                                   id="editEndTime" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ongoing" {{ $booking->status == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                        </div>
                        
                        <div class="col-12" id="editAvailabilityAlert" style="display: none;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Info Booking</h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> #{{ $booking->id }}</p>
                <p><strong>Dibuat:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Oleh:</strong> {{ $booking->user->name }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldSelect = document.getElementById('editFieldSelect');
    const bookingDate = document.getElementById('editBookingDate');
    const startTime = document.getElementById('editStartTime');
    const endTime = document.getElementById('editEndTime');
    const submitBtn = document.getElementById('editSubmitBtn');
    const alertDiv = document.getElementById('editAvailabilityAlert');
    
    function checkAvailability() {
        const fieldId = fieldSelect.value;
        const date = bookingDate.value;
        const start = startTime.value;
        const end = endTime.value;
        
        if (!fieldId || !date || !start || !end) {
            alertDiv.style.display = 'none';
            submitBtn.disabled = false;
            return;
        }
        
        fetch('/api/booking/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                field_id: fieldId,
                booking_date: date,
                start_time: start,
                end_time: end,
                exclude_booking_id: {{ $booking->id }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                alertDiv.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.className = 'btn btn-primary';
                submitBtn.innerHTML = 'Update Booking';
            } else {
                let conflictHtml = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lapangan tidak tersedia pada waktu tersebut';
                
                if (data.conflicts && data.conflicts.length > 0) {
                    conflictHtml += '<br><small class="mt-2 d-block"><strong>Bentrok dengan:</strong></small>';
                    data.conflicts.forEach(conflict => {
                        const badge = conflict.type === 'membership' ? 
                            '<span class="badge bg-warning text-dark">Member</span>' : 
                            '<span class="badge bg-info">Reguler</span>';
                        conflictHtml += `<small class="d-block">${badge} ${conflict.customer} (${conflict.time})</small>`;
                    });
                }
                
                conflictHtml += '</div>';
                
                alertDiv.innerHTML = conflictHtml;
                alertDiv.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.className = 'btn btn-danger';
                submitBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Tidak Tersedia';
            }
        })
        .catch(error => {
            console.error('Error checking availability:', error);
        });
    }
    
    // Event listeners
    fieldSelect.addEventListener('change', checkAvailability);
    bookingDate.addEventListener('change', checkAvailability);
    startTime.addEventListener('change', checkAvailability);
    endTime.addEventListener('change', checkAvailability);
    
    // Initial check
    checkAvailability();
});
</script>
@endpush