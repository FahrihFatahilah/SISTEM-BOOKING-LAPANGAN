@extends('admin.layouts.app')

@section('title', 'Pembukuan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Pembukuan</h2>
                <p class="text-muted">Kelola pemasukan dan pengeluaran</p>
            </div>
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Transaksi
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-arrow-up-circle fs-1 mb-2"></i>
                <h4 class="mb-0">{{ number_format($summary['total_income'], 0, ',', '.') }}</h4>
                <small>Total Pemasukan</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="bi bi-arrow-down-circle fs-1 mb-2"></i>
                <h4 class="mb-0">{{ number_format($summary['total_expense'], 0, ',', '.') }}</h4>
                <small>Total Pengeluaran</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card {{ $summary['net_profit'] >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
            <div class="card-body text-center">
                <i class="bi bi-calculator fs-1 mb-2"></i>
                <h4 class="mb-0">{{ number_format($summary['net_profit'], 0, ',', '.') }}</h4>
                <small>Laba Bersih</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" name="type">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                </div>
                @if(auth()->user()->isOwner())
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Cabang</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td>{!! $transaction->type_badge !!}</td>
                            <td>{{ $transaction->category }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>{{ $transaction->branch->name }}</td>
                            <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }} fw-semibold">
                                {{ $transaction->type === 'income' ? '+' : '-' }} {{ $transaction->formatted_amount }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.edit', $transaction) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
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
            
            {{ $transactions->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum ada transaksi</h5>
                <p class="text-muted">Mulai catat pemasukan dan pengeluaran</p>
                <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Transaksi Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection