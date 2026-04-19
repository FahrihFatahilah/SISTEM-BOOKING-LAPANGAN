@extends('admin.layouts.app')

@section('title', 'Detail Jadwal Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Detail Jadwal Member</h2>
                <p class="text-muted">{{ $memberSchedule->member_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.member-schedules.edit', $memberSchedule) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.member-schedules.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    Informasi Member
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Nama</label>
                        <div class="fw-semibold">{{ $memberSchedule->member_name }}</div>
                    </div>
                    @if($memberSchedule->member_phone)
                    <div class="col-12">
                        <label class="form-label text-muted">Telepon</label>
                        <div class="fw-semibold">{{ $memberSchedule->member_phone }}</div>
                    </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label text-muted">Lapangan</label>
                        <div class="fw-semibold">{{ $memberSchedule->field->name }}</div>
                        <small class="text-muted">{{ $memberSchedule->field->branch->name }}</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Hari</label>
                        <div><span class="badge bg-info">{{ $memberSchedule->day_name }}</span></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Waktu</label>
                        <div><span class="badge bg-secondary">{{ $memberSchedule->start_time }} - {{ $memberSchedule->end_time }}</span></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Harga Bulanan</label>
                        <div class="fw-semibold text-success fs-5">Rp {{ number_format($memberSchedule->monthly_price, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Tanggal Mulai</label>
                        <div class="fw-semibold">{{ $memberSchedule->start_date->format('d F Y') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @if($memberSchedule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                    @if($memberSchedule->notes)
                    <div class="col-12">
                        <label class="form-label text-muted">Catatan</label>
                        <div class="fw-semibold">{{ $memberSchedule->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kuota -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Kuota Sesi
                </h6>
            </div>
            <div class="card-body">
                @php
                    $remaining = $memberSchedule->getRemainingQuota();
                    $used = 4 - $remaining;
                    $endDate = $memberSchedule->end_date;
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small class="fw-semibold">{{ $memberSchedule->start_date->format('d F Y') }} - {{ $endDate->format('d F Y') }}</small>
                    <small class="{{ $remaining > 1 ? 'text-success' : ($remaining >= 1 ? 'text-warning' : 'text-danger') }}">{{ $used }}/4 sesi</small>
                </div>
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar {{ $remaining > 1 ? 'bg-success' : ($remaining >= 1 ? 'bg-warning' : 'bg-danger') }}"
                         style="width: {{ ($used/4)*100 }}%"></div>
                </div>
                <small class="text-muted">Sisa {{ $remaining }} sesi &bull; Rp {{ number_format($memberSchedule->monthly_price / 4, 0, ',', '.') }}/sesi</small>
            </div>
        </div>

        @if($memberSchedule->is_active)
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Aksi
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.member-schedules.generate', $memberSchedule) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Generate Sesi Berikutnya
                    </button>
                </form>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-outline-primary w-50" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Sesi
                    </button>
                    <button type="button" class="btn btn-outline-danger w-50" data-bs-toggle="modal" data-bs-target="#removeSessionModal"
                        @if($bookings->where('status', 'pending')->isEmpty()) disabled @endif>
                        <i class="bi bi-dash-circle me-1"></i> Kurangi Sesi
                    </button>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <small><strong>Sistem Kuota:</strong><br>
                    • Maks 4 sesi per bulan kalender<br>
                    • Kuota reset setiap awal bulan<br>
                    • Generate otomatis saat halaman member dibuka</small>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Booking Mendatang
                </h6>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentMonth = ''; @endphp
                                @foreach($bookings as $booking)
                                    @php $bookingMonth = $booking->booking_date->format('F Y'); @endphp
                                    @if($bookingMonth !== $currentMonth)
                                        @php $currentMonth = $bookingMonth; @endphp
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-bold text-primary">
                                                <i class="bi bi-calendar3 me-1"></i> {{ $bookingMonth }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{ $booking->booking_date->format('d F Y') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $booking->booking_date->locale('id')->dayName }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-warning fw-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td>{!! $booking->status_badge !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-3">Belum ada booking</h6>
                        @if($memberSchedule->start_date->isFuture())
                            <p class="text-muted">Booking akan di-generate otomatis mulai {{ $memberSchedule->start_date->format('d F Y') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- History Adjustment -->
        @if($adjustments->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Riwayat Perubahan Sesi
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Tanggal Sesi</th>
                                <th>Alasan</th>
                                <th>Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adjustments as $adj)
                            <tr>
                                <td><small>{{ $adj->created_at->format('d M Y H:i') }}</small></td>
                                <td>
                                    @if($adj->type === 'add')
                                        <span class="badge bg-success"><i class="bi bi-plus"></i> Tambah</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-dash"></i> Kurang</span>
                                    @endif
                                </td>
                                <td>
                                    @if($adj->booking)
                                        {{ $adj->booking->booking_date->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $adj->reason }}</td>
                                <td><small>{{ $adj->adjustedByUser->name ?? '-' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Sesi -->
<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.member-schedules.add-session', $memberSchedule) }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Sesi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penambahan Sesi <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required
                            placeholder="Contoh: Ganti sesi yang batal karena hujan, Bonus sesi dari promo, dll"></textarea>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <small>Sesi baru akan ditambahkan di minggu berikutnya setelah sesi terakhir.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Tambah Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kurangi Sesi -->
<div class="modal fade" id="removeSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.member-schedules.remove-session', $memberSchedule) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-dash-circle me-2"></i>Kurangi Sesi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Sesi yang Dibatalkan <span class="text-danger">*</span></label>
                        <select name="booking_id" class="form-select" required>
                            <option value="">-- Pilih sesi --</option>
                            @foreach($bookings->where('status', 'pending') as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->booking_date->format('d F Y') }} ({{ $b->booking_date->locale('id')->dayName }}) — {{ $b->start_time }} - {{ $b->end_time }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required
                            placeholder="Contoh: Member izin tidak hadir, Lapangan maintenance, dll"></textarea>
                    </div>
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <small>Sesi yang dibatalkan akan berubah status menjadi "Dibatalkan" dan tidak bisa dikembalikan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-dash-circle me-1"></i> Batalkan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
