@extends('admin.layouts.app')

@section('title', 'Pemindahan Stok')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Pemindahan Stok</h2>
                <p class="text-muted">Riwayat pemindahan barang dari Gudang ke Display</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.stock-transfers.opname') }}" class="btn btn-outline-info">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Opname Stok
                </a>
                <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Pindahkan Stok
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.stock-transfers.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                </div>
                @if(auth()->user()->isOwner())
                <div class="col-md-4">
                    <label class="form-label">Cabang</label>
                    <select class="form-select" name="branch_id">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transfer Table -->
<div class="card">
    <div class="card-body">
        @if($transfers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Cabang</th>
                            <th>Jumlah</th>
                            <th>Oleh</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->transfer_date->format('d F Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transfer->product->name }}</div>
                                <small class="text-muted">{{ $transfer->product->code }}</small>
                            </td>
                            <td><small class="text-muted">{{ $transfer->branch->name }}</small></td>
                            <td>
                                <span class="badge bg-primary">{{ $transfer->quantity }} pcs</span>
                            </td>
                            <td>{{ $transfer->user->name }}</td>
                            <td><small class="text-muted">{{ $transfer->notes ?? '-' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $transfers->firstItem() }} - {{ $transfers->lastItem() }} dari {{ $transfers->total() }}
                </div>
                {{ $transfers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada pemindahan stok</h5>
                <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-box-arrow-right me-2"></i> Pindahkan Stok
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
