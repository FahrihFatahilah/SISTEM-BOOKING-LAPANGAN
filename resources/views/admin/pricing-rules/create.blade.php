@extends('admin.layouts.app')

@section('title', 'Tambah Aturan Harga')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Aturan Harga</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pricing-rules.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rule_name" class="form-label">Nama Aturan</label>
                                <input type="text" class="form-control @error('rule_name') is-invalid @enderror" 
                                       id="rule_name" name="rule_name" value="{{ old('rule_name') }}" 
                                       placeholder="Contoh: Harga Prime Time" required>
                                @error('rule_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="2" 
                                  placeholder="Deskripsi aturan harga">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hari Berlaku</label>
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
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" 
                                                   value="{{ $value }}" id="day_{{ $value }}"
                                                   {{ in_array($value, old('days_of_week', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ $value }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days_of_week')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Jam Mulai</label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                               id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">Jam Selesai</label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                               id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="price_per_hour" class="form-label">Harga per Jam</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('price_per_hour') is-invalid @enderror" 
                                                   id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour') }}" 
                                                   min="0" step="1000" required>
                                        </div>
                                        @error('price_per_hour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Prioritas</label>
                                        <select class="form-select @error('priority') is-invalid @enderror" 
                                                id="priority" name="priority" required>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('priority', 5) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 10 ? '(Tertinggi)' : ($i == 1 ? '(Terendah)' : '') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Prioritas lebih tinggi akan diprioritaskan jika ada overlap</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Contoh Penggunaan:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Prime Time:</strong> Senin-Jumat, 18:00-22:00, Rp 100.000/jam</li>
                            <li><strong>Weekend:</strong> Sabtu-Minggu, 08:00-22:00, Rp 120.000/jam</li>
                            <li><strong>Happy Hour:</strong> Senin-Jumat, 14:00-17:00, Rp 80.000/jam</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection