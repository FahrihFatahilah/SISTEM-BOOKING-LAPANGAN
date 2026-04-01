@extends('admin.layouts.app')

@section('title', 'Aturan Harga')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Aturan Harga</h2>
                <p class="text-muted">Kelola harga per jam berdasarkan waktu dan hari</p>
            </div>
            <a href="{{ route('admin.pricing-rules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Aturan
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Aturan</th>
                        <th>Lapangan</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Harga/Jam</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td>
                            <strong>{{ $rule->rule_name }}</strong>
                            @if($rule->description)
                                <br><small class="text-muted">{{ $rule->description }}</small>
                            @endif
                        </td>
                        <td>{{ $rule->field->name }}</td>
                        <td>
                            <small>{{ $rule->days_of_week_text }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ \Carbon\Carbon::parse($rule->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($rule->end_time)->format('H:i') }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-success">
                                Rp {{ number_format($rule->price_per_hour, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $rule->priority }}</span>
                        </td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.pricing-rules.edit', $rule) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.pricing-rules.destroy', $rule) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus aturan ini?')">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-currency-dollar text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Belum ada aturan harga</h5>
                            <p class="text-muted">Silakan tambah aturan harga untuk mengatur harga per jam</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($rules->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $rules->links() }}
</div>
@endif

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Catatan:</strong>
    <ul class="mb-0 mt-2">
        <li>Aturan dengan prioritas lebih tinggi akan digunakan jika ada overlap waktu</li>
        <li>Jika tidak ada aturan yang cocok, akan menggunakan harga default lapangan</li>
        <li>Aturan berlaku untuk hari dan jam yang ditentukan</li>
    </ul>
</div>
@endsection