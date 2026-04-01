@extends('admin.layouts.app')

@section('title', 'Booking Baru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Booking Baru</h2>
                <p class="text-muted">Buat booking lapangan baru</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Form Booking
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Customer Information -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-person me-2"></i>
                                Informasi Pelanggan
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   name="customer_phone" value="{{ old('customer_phone') }}" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Booking Information -->
                        <div class="col-12 mt-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-calendar-event me-2"></i>
                                Informasi Booking
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tipe Booking <span class="text-danger">*</span></label>
                            <select class="form-select @error('booking_type') is-invalid @enderror" 
                                    name="booking_type" required>
                                <option value="">Pilih Tipe Booking</option>
                                <option value="regular" {{ old('booking_type') == 'regular' ? 'selected' : '' }}>Regular (Harian)</option>
                                <option value="member" {{ old('booking_type') == 'member' ? 'selected' : '' }}>Member (Bulanan)</option>
                            </select>
                            @error('booking_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Lapangan <span class="text-danger">*</span></label>
                            <select class="form-select @error('field_id') is-invalid @enderror" 
                                    name="field_id" id="fieldSelect" required>
                                <option value="">Pilih Lapangan</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" 
                                            data-price="{{ $field->price_per_hour }}"
                                            data-branch="{{ $field->branch->name }}"
                                            {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }} - {{ $field->branch->name }} 
                                        (Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}/jam)
                                    </option>
                                @endforeach
                            </select>
                            @error('field_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Booking <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('booking_date') is-invalid @enderror" 
                                   name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" id="bookingDate" required>
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="start_time" value="{{ old('start_time') }}" id="startTime" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   name="end_time" value="{{ old('end_time') }}" id="endTime" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>
                            Simpan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Booking Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    Ringkasan Booking
                </h6>
            </div>
            <div class="card-body">
                <div id="bookingSummary">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-info-circle fs-1"></i>
                        <p class="mt-2">Pilih lapangan dan waktu untuk melihat ringkasan</p>
                    </div>
                </div>
                
                <div id="availabilityCheck" style="display: none;">
                    <div class="alert alert-info">
                        <i class="bi bi-clock me-2"></i>
                        <small>Mengecek ketersediaan...</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Field Information -->
        <div class="card mt-3" id="fieldInfo" style="display: none;">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Lapangan
                </h6>
            </div>
            <div class="card-body">
                <div id="fieldDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldSelect = document.getElementById('fieldSelect');
    const bookingDate = document.getElementById('bookingDate');
    const startTime = document.getElementById('startTime');
    const endTime = document.getElementById('endTime');
    const submitBtn = document.getElementById('submitBtn');
    
    function updateSummary() {
        const fieldId = fieldSelect.value;
        const date = bookingDate.value;
        const start = startTime.value;
        const end = endTime.value;
        
        if (!fieldId || !date || !start || !end) {
            document.getElementById('bookingSummary').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-info-circle fs-1"></i>
                    <p class="mt-2">Pilih lapangan dan waktu untuk melihat ringkasan</p>
                </div>
            `;
            document.getElementById('fieldInfo').style.display = 'none';
            return;
        }
        
        const selectedOption = fieldSelect.options[fieldSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price);
        const branchName = selectedOption.dataset.branch;
        const fieldName = selectedOption.text.split(' - ')[0];
        
        // Calculate duration and total price
        const startDate = new Date(`2000-01-01 ${start}`);
        const endDate = new Date(`2000-01-01 ${end}`);
        const duration = (endDate - startDate) / (1000 * 60 * 60); // hours
        const totalPrice = duration * price;
        
        if (duration <= 0) {
            document.getElementById('bookingSummary').innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Jam selesai harus lebih besar dari jam mulai
                </div>
            `;
            return;
        }
        
        // Update summary
        document.getElementById('bookingSummary').innerHTML = `
            <div class="row g-2">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Lapangan:</span>
                        <span class="fw-semibold">${fieldName}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Cabang:</span>
                        <span>${branchName}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal:</span>
                        <span>${new Date(date).toLocaleDateString('id-ID')}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Waktu:</span>
                        <span>${start} - ${end}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Durasi:</span>
                        <span>${duration} jam</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Harga/jam:</span>
                        <span>Rp ${price.toLocaleString('id-ID')}</span>
                    </div>
                </div>
                <hr>
                <div class="col-12">
                    <div class="d-flex justify-content-between fw-bold text-primary">
                        <span>Total:</span>
                        <span>Rp ${totalPrice.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            </div>
        `;
        
        // Show field info
        document.getElementById('fieldInfo').style.display = 'block';
        document.getElementById('fieldDetails').innerHTML = `
            <div class="row g-2">
                <div class="col-12">
                    <small class="text-muted">Harga per jam:</small>
                    <div class="fw-semibold text-success">Rp ${price.toLocaleString('id-ID')}</div>
                </div>
            </div>
        `;
        
        // Check availability
        checkAvailability(fieldId, date, start, end);
    }
    
    function checkAvailability(fieldId, date, start, end) {
        document.getElementById('availabilityCheck').style.display = 'block';
        
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
                end_time: end
            })
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('availabilityCheck').style.display = 'none';
                
                if (data.available) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Simpan Booking';
                    submitBtn.className = 'btn btn-primary';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Tidak Tersedia';
                    submitBtn.className = 'btn btn-danger';
                    
                    let conflictHtml = '<div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle me-2"></i>Lapangan tidak tersedia pada waktu tersebut';
                    
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
                    
                    document.getElementById('bookingSummary').innerHTML += conflictHtml;
                }
            })
            .catch(error => {
                console.error('Error checking availability:', error);
                document.getElementById('availabilityCheck').style.display = 'none';
            });
    }
    
    // Event listeners
    fieldSelect.addEventListener('change', updateSummary);
    bookingDate.addEventListener('change', updateSummary);
    startTime.addEventListener('change', updateSummary);
    endTime.addEventListener('change', updateSummary);
    
    // Initial update
    updateSummary();
});
</script>
@endpush