@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan Produk')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Laporan Penjualan Produk</h2>
                <p class="text-muted">
                    Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d F Y') }} -
                    {{ \Carbon\Carbon::parse($request->end_date)->format('d F Y') }}
                </p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.product-sales') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $request->start_date }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $request->end_date }}" required>
                </div>
                @if(auth()->user()->isOwner())
                <div class="col-md-3">
                    <label class="form-label">Cabang</label>
                    <select class="form-select" name="branch_id">
                        <option value="">Semua</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $request->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ $summary['total_products'] }}</h3>
                <small>Jenis Produk Terjual</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">{{ number_format($summary['total_items_sold']) }}</h3>
                <small>Total Item Terjual</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                <small>Total Pendapatan</small>
            </div>
        </div>
    </div>
</div>

<!-- Product Sales Table -->
<div class="card">
    <div class="card-body">
        @if($productSales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th class="text-center">Jumlah Terjual</th>
                            <th class="text-end">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productSales as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $item->product_name }}</td>
                            <td class="text-center">{{ number_format($item->total_qty) }}</td>
                            <td class="text-end">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2">Total</td>
                            <td class="text-center">{{ number_format($summary['total_items_sold']) }}</td>
                            <td class="text-end">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data penjualan produk</h5>
                <p class="text-muted">Coba ubah filter atau periode tanggal</p>
            </div>
        @endif
    </div>
</div>
@endsection
