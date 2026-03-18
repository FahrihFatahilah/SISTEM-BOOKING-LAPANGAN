@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Laporan Pendapatan</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-2">
                    <label>Tipe</label>
                    <select name="type" class="form-control" required>
                        <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                </div>
            </form>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Total Pendapatan</h6>
                            <h3>Rp {{ number_format($revenues->sum('revenue') ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Total Booking</h6>
                            <h3>{{ $revenues->sum('bookings') ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mt-4">Detail Pendapatan {{ request('type') == 'daily' ? 'Harian' : 'Bulanan' }}</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ request('type') == 'daily' ? 'Tanggal' : 'Bulan' }}</th>
                        <th>Jumlah Booking</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues ?? [] as $revenue)
                    <tr>
                        <td>
                            @if(request('type') == 'daily')
                                {{ \Carbon\Carbon::parse($revenue->date)->format('d/m/Y') }}
                            @else
                                {{ \Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}
                            @endif
                        </td>
                        <td>{{ $revenue->bookings }}</td>
                        <td>Rp {{ number_format($revenue->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
