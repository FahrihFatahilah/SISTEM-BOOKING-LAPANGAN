@extends('admin.layouts.app')

@section('title', 'Jadwal Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Jadwal Member</h2>
                <p class="text-muted">Kelola jadwal tetap member bulanan</p>
            </div>
            <a href="{{ route('admin.member-schedules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Jadwal Member
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($schedules->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Lapangan</th>
                            <th>Hari</th>
                            <th>Waktu</th>
                            <th>Harga Bulanan</th>
                            <th>Kuota Bulan Ini</th>
                            <th>Mulai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $schedule->member_name }}</div>
                                    <small class="text-muted">{{ $schedule->member_phone }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $schedule->field->name }}</div>
                                    <small class="text-muted">{{ $schedule->field->branch->name }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $schedule->day_name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">Rp {{ number_format($schedule->monthly_price, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @php
                                    $remaining = $schedule->getRemainingQuota();
                                    $used = 4 - $remaining;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar {{ $remaining > 1 ? 'bg-success' : ($remaining == 1 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ ($used/4)*100 }}%"></div>
                                    </div>
                                    <small class="{{ $remaining > 1 ? 'text-success' : ($remaining == 1 ? 'text-warning' : 'text-danger') }}">
                                        {{ $remaining }}/4
                                    </small>
                                </div>
                            </td>
                            <td>{{ $schedule->start_date->format('d M Y') }}</td>
                            <td>
                                @if($schedule->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.member-schedules.show', $schedule) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.member-schedules.edit', $schedule) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($schedule->is_active)
                                    <form method="POST" action="{{ route('admin.member-schedules.generate', $schedule) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Generate 30 hari">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.member-schedules.destroy', $schedule) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menonaktifkan jadwal member ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $schedules->firstItem() }} - {{ $schedules->lastItem() }} dari {{ $schedules->total() }} jadwal
                </div>
                {{ $schedules->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-week text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada jadwal member</h5>
                <p class="text-muted">Silakan tambah jadwal member baru</p>
                <a href="{{ route('admin.member-schedules.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Jadwal Member
                </a>
            </div>
        @endif
    </div>
</div>
@endsection