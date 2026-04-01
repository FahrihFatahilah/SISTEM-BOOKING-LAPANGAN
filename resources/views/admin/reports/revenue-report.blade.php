@extends('admin.layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Laporan Pendapatan</h2>
                <p class="text-muted">
                    Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                    ({{ $request->type == 'daily' ? 'Harian' : 'Bulanan' }})
                </p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $request->start_date }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $request->end_date }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-select" required>
                    <option value="daily" {{ $request->type == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="monthly" {{ $request->type == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            @if(auth()->user()->isOwner())
            <div class="col-md-2">
                <label class="form-label">Cabang</label>
                <select name="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $request->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $revenues->sum('total_bookings') ?? 0 }}</h3>
                <small>Total Booking</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $revenues->sum('member_bookings') ?? 0 }}</h3>
                <small>Booking Member</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $revenues->sum('regular_bookings') ?? 0 }}</h3>
                <small>Booking Regular</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($revenues->sum('regular_revenue') ?? 0, 0, ',', '.') }}</h3>
                <small>Revenue Regular</small>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Table -->
<div class="card">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="bi bi-graph-up me-2"></i>
            Detail Pendapatan {{ $request->type == 'daily' ? 'Harian' : 'Bulanan' }}
        </h5>
    </div>
    <div class="card-body">
        @if($revenues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $request->type == 'daily' ? 'Tanggal' : 'Bulan' }}</th>
                            <th>Total Booking</th>
                            <th>Member</th>
                            <th>Regular</th>
                            <th>Revenue Regular</th>
                            <th>Sesi Member</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenues as $revenue)
                        <tr>
                            <td>
                                @if($request->type == 'daily')
                                    {{ \Carbon\Carbon::parse($revenue->date)->format('d/m/Y') }}
                                @else
                                    {{ \Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $revenue->total_bookings }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $revenue->member_bookings }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $revenue->regular_bookings }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">Rp {{ number_format($revenue->regular_revenue, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $revenue->member_sessions }} sesi</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th><span class="badge bg-primary">{{ $revenues->sum('total_bookings') }}</span></th>
                            <th><span class="badge bg-warning text-dark">{{ $revenues->sum('member_bookings') }}</span></th>
                            <th><span class="badge bg-info">{{ $revenues->sum('regular_bookings') }}</span></th>
                            <th><span class="fw-bold text-success">Rp {{ number_format($revenues->sum('regular_revenue'), 0, ',', '.') }}</span></th>
                            <th><span class="text-muted">{{ $revenues->sum('member_sessions') }} sesi</span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-graph-down text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data pendapatan</h5>
                <p class="text-muted">Tidak ada data pada periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>

<!-- Info Card -->
<div class="card mt-4">
    <div class="card-body">
        <h6 class="card-title">Keterangan:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><i class="bi bi-info-circle text-info me-2"></i><strong>Revenue Regular:</strong> Pendapatan dari booking harian</li>
                    <li><i class="bi bi-crown text-warning me-2"></i><strong>Sesi Member:</strong> Jumlah sesi yang digunakan member</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><i class="bi bi-calendar-check text-success me-2"></i><strong>Member:</strong> Sudah bayar bulanan, tidak dihitung di revenue</li>
                    <li><i class="bi bi-cash text-primary me-2"></i><strong>Regular:</strong> Bayar per booking, dihitung di revenue</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
