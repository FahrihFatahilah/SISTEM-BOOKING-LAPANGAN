@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Detail Penjualan - {{ $sale->invoice_number }}</h5>
            <a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Invoice:</strong> {{ $sale->invoice_number }}</p>
                    <p><strong>Tanggal:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Kasir:</strong> {{ $sale->user->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Cabang:</strong> {{ $sale->branch->name }}</p>
                    <p><strong>Pembayaran:</strong> {{ ucfirst($sale->payment_method) }}</p>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                        <td>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->discount > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>Diskon:</strong></td>
                        <td>Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($sale->tax > 0)
                    <tr>
                        <td colspan="3" class="text-end"><strong>Pajak:</strong></td>
                        <td>Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                        <td><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Bayar:</strong></td>
                        <td>Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Kembali:</strong></td>
                        <td>Rp {{ number_format($sale->change, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
