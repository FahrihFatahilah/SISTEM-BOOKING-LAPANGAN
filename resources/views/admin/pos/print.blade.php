<!DOCTYPE html>
<html>
<head>
    <title>Print Struk - {{ $sale->invoice_number }}</title>
    <style>
        @media print {
            @page { margin: 0; size: 80mm auto; }
            body { margin: 0; }
        }
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            font-size: 12px;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="center bold">
        {{ $sale->branch->name }}<br>
        {{ $sale->branch->address }}<br>
        Telp: {{ $sale->branch->phone }}
    </div>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>Invoice</td>
            <td class="right">{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="right">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="right">{{ $sale->user->name }}</td>
        </tr>
    </table>
    
    <div class="line"></div>
    
    <table>
        @foreach($sale->items as $item)
        <tr>
            <td colspan="2">{{ $item->product_name }}</td>
        </tr>
        <tr>
            <td>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($sale->discount > 0)
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($sale->tax > 0)
        <tr>
            <td>Pajak</td>
            <td class="right">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td>TOTAL</td>
            <td class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ ucfirst($sale->payment_method) }})</td>
            <td class="right">Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="right">Rp {{ number_format($sale->change, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <div class="line"></div>
    
    <div class="center">
        Terima Kasih<br>
        Selamat Datang Kembali
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
