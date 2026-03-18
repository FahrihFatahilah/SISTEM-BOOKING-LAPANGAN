@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Riwayat Penjualan</h5>
            <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">Kembali ke POS</a>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($sale->payment_method) }}</span></td>
                        <td>
                            <a href="{{ route('admin.pos.show', $sale) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ route('admin.pos.print', $sale) }}" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
