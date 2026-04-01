@extends('admin.layouts.app')

@section('title', 'Edit Aturan Harga')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Aturan Harga</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pricing-rules.update', $pricingRule) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rule_name" class="form-label">Nama Aturan</label>
                                <input type="text" class="form-control @error('rule_name') is-invalid @enderror" 
                                       id="rule_name" name="rule_name" value="{{ old('rule_name', $pricingRule->rule_name) }}" required>
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
                                        <option value="{{ $field->id }}" {{ old('field_id', $pricingRule->field_id) == $field->id ? 'selected' : '' }}>
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
                                  id="description" name="description" rows="2">{{ old('description', $pricingRule->description) }}</textarea>
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
                                        $selectedDays = old('days_of_week', $pricingRule->days_of_week);
                                    @endphp
                                    @foreach($days as $value => $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" 
                                                   value="{{ $value }}" id="day_{{ $value }}"
                                                   {{ in_array($value, $selectedDays) ? 'checked' : '' }}>
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
                                               id="start_time" name="start_time" 
                                               value="{{ old('start_time', \Carbon\Carbon::parse($pricingRule->start_time)->format('H:i')) }}" required>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">Jam Selesai</label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                               id="end_time" name="end_time" 
                                               value="{{ old('end_time', \Carbon\Carbon::parse($pricingRule->end_time)->format('H:i')) }}" required>
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
                                                   id="price_per_hour" name="price_per_hour" 
                                                   value="{{ old('price_per_hour', $pricingRule->price_per_hour) }}" 
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
                                                <option value="{{ $i }}" {{ old('priority', $pricingRule->priority) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 10 ? '(Tertinggi)' : ($i == 1 ? '(Terendah)' : '') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $pricingRule->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Status Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection