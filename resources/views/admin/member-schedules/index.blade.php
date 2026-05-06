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
                                    $limit = $schedule->monthly_limit ?? 4;
                                    $remaining = $schedule->getRemainingQuota();
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar {{ $remaining > 1 ? 'bg-success' : ($remaining == 1 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ ($remaining/$limit)*100 }}%"></div>
                                    </div>
                                    <small class="{{ $remaining > 1 ? 'text-success' : ($remaining == 1 ? 'text-warning' : 'text-danger') }}">
                                        {{ $remaining }}/{{ $limit }}
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
                                    <a href="{{ route('admin.member-schedules.show', $schedule) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.member-schedules.edit', $schedule) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($schedule->is_active)
                                    <button type="button" class="btn btn-outline-success" title="Tambah Sesi" 
                                            data-bs-toggle="modal" data-bs-target="#addSessionModal{{ $schedule->id }}">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" title="Kurangi Sesi" 
                                            data-bs-toggle="modal" data-bs-target="#removeSessionModal{{ $schedule->id }}">
                                        <i class="bi bi-dash-circle"></i>
                                    </button>
                                    @endif
                                    <form method="POST" action="{{ route('admin.member-schedules.destroy', $schedule) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus membership ini? Booking pending akan ikut terhapus.')">
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

{{-- Modals Tambah/Kurangi Sesi --}}
@foreach($schedules as $schedule)
@if($schedule->is_active)
<div class="modal fade" id="addSessionModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.member-schedules.add-session', $schedule) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Sesi - {{ $schedule->member_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="2" required placeholder="Contoh: Ganti sesi batal karena hujan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="removeSessionModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.member-schedules.remove-session', $schedule) }}">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-dash-circle me-2"></i>Kurangi Sesi - {{ $schedule->member_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $pendingBookings = \App\Models\Booking::where('field_id', $schedule->field_id)
                            ->where('customer_name', $schedule->member_name)
                            ->where('is_membership', true)
                            ->where('status', 'pending')
                            ->orderBy('booking_date')
                            ->get();
                    @endphp
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Sesi <span class="text-danger">*</span></label>
                        <select name="booking_id" class="form-select" required>
                            <option value="">-- Pilih sesi --</option>
                            @foreach($pendingBookings as $b)
                                <option value="{{ $b->id }}">{{ $b->booking_date->format('d F Y') }} ({{ $b->booking_date->locale('id')->dayName }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="2" required placeholder="Contoh: Member izin tidak hadir"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-dash-circle me-1"></i> Kurangi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection