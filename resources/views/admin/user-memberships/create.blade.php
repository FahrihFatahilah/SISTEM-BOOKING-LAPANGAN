@extends('admin.layouts.app')

@section('title', 'Tambah Member')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Member Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.user-memberships.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pelanggan</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="membership_package_id" class="form-label">Paket Membership</label>
                                <select class="form-select @error('membership_package_id') is-invalid @enderror" 
                                        id="membership_package_id" name="membership_package_id" required>
                                    <option value="">Pilih Paket</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ old('membership_package_id') == $package->id ? 'selected' : '' }}
                                                data-sessions="{{ $package->sessions_per_week }}"
                                                data-duration="{{ $package->session_duration_hours }}"
                                                data-price="{{ number_format($package->price, 0, ',', '.') }}">
                                            {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('membership_package_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="package-info" class="mt-2 text-muted small"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="field_id" class="form-label">Lapangan</label>
                                <select class="form-select @error('field_id') is-invalid @enderror" id="field_id" name="field_id" required>
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
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                                       min="{{ now()->format('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jadwal Mingguan (Hari Bermain)</label>
                                <div class="border rounded p-3">
                                    @php
                                        $days = [
                                            1 => 'Senin',
                                            2 => 'Selasa', 
                                            3 => 'Rabu',
                                            4 => 'Kamis',
                                            5 => 'Jumat',
                                            6 => 'Sabtu',
                                            0 => 'Minggu'
                                        ];
                                    @endphp
                                    @foreach($days as $value => $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="weekly_schedule[]" 
                                                   value="{{ $value }}" id="day_{{ $value }}"
                                                   {{ in_array($value, old('weekly_schedule', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ $value }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('weekly_schedule')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih hari-hari bermain yang sama setiap minggu</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Jam bermain yang sama setiap hari terjadwal</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Catatan:</strong> Sistem akan otomatis membuat jadwal booking untuk seluruh periode membership berdasarkan hari dan jam yang dipilih.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.user-memberships.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('membership_package_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('package-info');
    
    if (option.value) {
        const sessions = option.dataset.sessions;
        const duration = option.dataset.duration;
        const price = option.dataset.price;
        
        infoDiv.innerHTML = `
            <strong>Detail Paket:</strong><br>
            • ${sessions}x sesi per minggu<br>
            • ${duration} jam per sesi<br>
            • Harga: Rp ${price}
        `;
    } else {
        infoDiv.innerHTML = '';
    }
});
</script>
@endsection