<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary-item h3 {
            margin: 0;
            color: #333;
        }
        .summary-item small {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-pending { color: #ffc107; }
        .status-ongoing { color: #28a745; }
        .status-completed { color: #6c757d; }
        .status-cancelled { color: #dc3545; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN BOOKING LAPANGAN</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d F Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>{{ $summary['total_bookings'] }}</h3>
            <small>Total Booking</small>
        </div>
        <div class="summary-item">
            <h3>{{ $summary['completed_bookings'] }}</h3>
            <small>Selesai</small>
        </div>
        <div class="summary-item">
            <h3>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
            <small>Total Pendapatan</small>
        </div>
    </div>

    @if($bookings->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Lapangan</th>
                <th>Cabang</th>
                <th>Waktu</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $index => $booking)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                <td>
                    {{ $booking->customer_name }}<br>
                    <small>{{ $booking->customer_phone }}</small>
                </td>
                <td>{{ $booking->field->name }}</td>
                <td>{{ $booking->field->branch->name }}</td>
                <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                <td class="status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; margin: 50px 0;">Tidak ada data booking pada periode yang dipilih.</p>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh Sistem Booking Lapangan</p>
    </div>
</body>
</html>