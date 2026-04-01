@extends('admin.layouts.app')

@section('title', 'Tambah Paket Membership')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Paket Membership</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.membership-packages.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Paket</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" 
                                           min="0" step="1000" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Durasi (Hari)</label>
                                <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                                       id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" 
                                       min="1" required>
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sessions_per_week" class="form-label">Sesi per Minggu</label>
                                <select class="form-select @error('sessions_per_week') is-invalid @enderror" 
                                        id="sessions_per_week" name="sessions_per_week" required>
                                    <option value="">Pilih jumlah sesi</option>
                                    @for($i = 1; $i <= 7; $i++)
                                        <option value="{{ $i }}" {{ old('sessions_per_week') == $i ? 'selected' : '' }}>
                                            {{ $i }}x per minggu
                                        </option>
                                    @endfor
                                </select>
                                @error('sessions_per_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="session_duration_hours" class="form-label">Durasi Sesi (Jam)</label>
                                <select class="form-select @error('session_duration_hours') is-invalid @enderror" 
                                        id="session_duration_hours" name="session_duration_hours" required>
                                    <option value="">Pilih durasi</option>
                                    <option value="0.5" {{ old('session_duration_hours') == '0.5' ? 'selected' : '' }}>30 menit</option>
                                    <option value="1" {{ old('session_duration_hours') == '1' ? 'selected' : '' }}>1 jam</option>
                                    <option value="1.5" {{ old('session_duration_hours') == '1.5' ? 'selected' : '' }}>1.5 jam</option>
                                    <option value="2" {{ old('session_duration_hours') == '2' ? 'selected' : '' }}>2 jam</option>
                                    <option value="3" {{ old('session_duration_hours') == '3' ? 'selected' : '' }}>3 jam</option>
                                </select>
                                @error('session_duration_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.membership-packages.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Paket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection