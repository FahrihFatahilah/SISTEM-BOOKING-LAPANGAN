@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Tambah Jadwal Member</h2>
                <p class="text-muted">Buat jadwal tetap untuk member bulanan</p>
            </div>
            <a href="{{ route('admin.member-schedules.index') }}" class="btn btn-outline-secondary">
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
                    Form Jadwal Member
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.member-schedules.store') }}">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Member Information -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-person me-2"></i>
                                Informasi Member
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Member <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('member_name') is-invalid @enderror" 
                                   name="member_name" value="{{ old('member_name') }}" required>
                            @error('member_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('member_phone') is-invalid @enderror" 
                                   name="member_phone" value="{{ old('member_phone') }}">
                            @error('member_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Schedule Information -->
                        <div class="col-12 mt-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-calendar-event me-2"></i>
                                Jadwal Tetap
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Lapangan <span class="text-danger">*</span></label>
                            <select class="form-select @error('field_id') is-invalid @enderror" name="field_id" required>
                                <option value="">Pilih Lapangan</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }} - {{ $field->branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('field_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror" name="day_of_week" required>
                                <option value="">Pilih Hari</option>
                                <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Minggu</option>
                                <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Senin</option>
                                <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Selasa</option>
                                <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Rabu</option>
                                <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Kamis</option>
                                <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Jumat</option>
                                <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Sabtu</option>
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="start_time" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   name="end_time" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Harga Bulanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('monthly_price') is-invalid @enderror" 
                                       name="monthly_price" value="{{ old('monthly_price') }}" min="0" required>
                            </div>
                            @error('monthly_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('start_date')
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
                        <a href="{{ route('admin.member-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Cara Kerja:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Saat simpan, sistem langsung generate 4 sesi booking mulai dari <strong>Tanggal Mulai</strong></li>
                        <li>Setiap hari sistem otomatis generate sesi baru jika kuota belum penuh</li>
                        <li>Maksimal 4 sesi per bulan per member</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong>
                    <p class="mb-0">Pastikan jadwal tidak bentrok dengan member lain atau booking reguler.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection