@extends('admin.layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Pengaturan Aplikasi</h2>
        <p class="text-muted">Kelola pengaturan sistem dan konfigurasi</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock me-2"></i>
                    Zona Waktu
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Zona Waktu Aplikasi</label>
                        <select class="form-select @error('app_timezone') is-invalid @enderror" name="app_timezone" required>
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ $currentTimezone == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('app_timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Zona waktu ini akan digunakan untuk semua tampilan waktu di aplikasi
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Waktu
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Waktu Server Saat Ini:</small>
                    <div class="fw-semibold">{{ now()->format('d F Y, H:i:s') }}</div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Zona Waktu Aktif:</small>
                    <div class="fw-semibold">{{ $currentTimezone }}</div>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <i class="bi bi-lightbulb me-1"></i>
                        Perubahan zona waktu akan mempengaruhi semua tampilan waktu di aplikasi termasuk booking dan laporan.
                    </small>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Pengaturan Lainnya
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">Backup Database</div>
                            <small class="text-muted">Backup otomatis setiap hari</small>
                        </div>
                        <span class="badge bg-success">Aktif</span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">Notifikasi Email</div>
                            <small class="text-muted">Email untuk booking baru</small>
                        </div>
                        <span class="badge bg-secondary">Nonaktif</span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">Auto Update Status</div>
                            <small class="text-muted">Update status booking otomatis</small>
                        </div>
                        <span class="badge bg-success">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection