@extends('admin.layouts.app')

@section('title', 'Opname Stok')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Opname Stok</h2>
                <p class="text-muted">Perbandingan stok Gudang dan Display - {{ now()->format('d F Y') }}</p>
            </div>
            <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-box-seam fs-1 mb-2"></i>
                <h4 class="mb-0">{{ $products->sum('warehouse_stock') }}</h4>
                <small>Total Stok Gudang</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-shop fs-1 mb-2"></i>
                <h4 class="mb-0">{{ $products->sum('display_stock') }}</h4>
                <small>Total Stok Display</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-archive fs-1 mb-2"></i>
                <h4 class="mb-0">{{ $products->sum('stock') }}</h4>
                <small>Total Keseluruhan</small>
            </div>
        </div>
    </div>
</div>

<!-- Opname Table -->
<div class="card">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="bi bi-clipboard-check me-2"></i>
            Detail Stok Per Produk
        </h5>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Produk</th>
                            <th>Cabang</th>
                            <th class="text-center">Stok Gudang</th>
                            <th class="text-center">Stok Display</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Min. Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ $product->code }}</span></td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td><small class="text-muted">{{ $product->branch->name }}</small></td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $product->warehouse_stock }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $product->display_stock }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ $product->stock }}</span>
                            </td>
                            <td class="text-center">{{ $product->min_stock }}</td>
                            <td>
                                @if($product->stock <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($product->stock <= $product->min_stock)
                                    <span class="badge bg-warning">Stok Rendah</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif

                                @if($product->warehouse_stock > 0 && $product->display_stock <= 0)
                                    <span class="badge bg-info">Perlu Pindah</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada produk</h5>
            </div>
        @endif
    </div>
</div>
@endsection
