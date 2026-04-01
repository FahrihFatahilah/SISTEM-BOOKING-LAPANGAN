<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);
        
        return view('admin.reports.index', compact('branches'));
    }

    public function bookingReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'field_id' => 'nullable|exists:fields,id',
        ]);

        $user = auth()->user();
        
        $query = Booking::with(['field.branch', 'user'])
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        // Filter berdasarkan role
        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Filter berdasarkan cabang
        if ($request->filled('branch_id')) {
            $query->whereHas('field', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Filter berdasarkan lapangan
        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        $bookings = $query->get();
        
        $summary = [
            'total_bookings' => $bookings->count(),
            'member_bookings' => $bookings->where('is_membership', true)->count(),
            'regular_bookings' => $bookings->where('is_membership', false)->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $bookings->where('status', 'completed')->where('is_membership', false)->sum('total_price'),
            'member_revenue' => $bookings->where('status', 'completed')->where('is_membership', true)->count() * 0, // Member sudah bayar bulanan
        ];

        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);

        return view('admin.reports.booking-report', compact('bookings', 'summary', 'branches', 'request'));
    }

    public function revenueReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:daily,monthly',
        ]);

        $user = auth()->user();
        
        $query = Booking::with(['field.branch'])
            ->where('status', 'completed')
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        // Filter berdasarkan role
        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Filter berdasarkan cabang
        if ($request->filled('branch_id')) {
            $query->whereHas('field', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->type === 'daily') {
            $revenues = $query->selectRaw('DATE(booking_date) as date, 
                SUM(CASE WHEN is_membership = 0 THEN total_price ELSE 0 END) as regular_revenue,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_sessions,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN is_membership = 0 THEN 1 ELSE 0 END) as regular_bookings,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_bookings')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $revenues = $query->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, 
                SUM(CASE WHEN is_membership = 0 THEN total_price ELSE 0 END) as regular_revenue,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_sessions,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN is_membership = 0 THEN 1 ELSE 0 END) as regular_bookings,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_bookings')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);

        return view('admin.reports.revenue-report', compact('revenues', 'branches', 'request'));
    }

    public function exportBookingPdf(Request $request)
    {
        $user = auth()->user();
        
        $query = Booking::with(['field.branch', 'user'])
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('field', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $bookings = $query->get();
        
        $summary = [
            'total_bookings' => $bookings->count(),
            'member_bookings' => $bookings->where('is_membership', true)->count(),
            'regular_bookings' => $bookings->where('is_membership', false)->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'total_revenue' => $bookings->where('status', 'completed')->where('is_membership', false)->sum('total_price'),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf.booking-report', compact('bookings', 'summary', 'request'));
        
        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }

    public function exportBookingExcel(Request $request)
    {
        return Excel::download(new BookingExport($request), 'laporan-booking-' . date('Y-m-d') . '.xlsx');
    }
}

// Export class untuk Excel
class BookingExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $user = auth()->user();
        
        $query = Booking::with(['field.branch', 'user'])
            ->whereBetween('booking_date', [$this->request->start_date, $this->request->end_date]);

        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        return $query->get()->map(function ($booking) {
            return [
                'Tanggal' => $booking->booking_date->format('d/m/Y'),
                'Tipe' => $booking->is_membership ? 'Member' : 'Regular',
                'Cabang' => $booking->field->branch->name,
                'Lapangan' => $booking->field->name,
                'Pelanggan' => $booking->customer_name,
                'Telepon' => $booking->customer_phone,
                'Jam Mulai' => $booking->start_time,
                'Jam Selesai' => $booking->end_time,
                'Total Harga' => $booking->is_membership ? 'Bulanan' : $booking->total_price,
                'Status' => ucfirst($booking->status),
                'Dibuat Oleh' => $booking->user->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Cabang',
            'Lapangan',
            'Pelanggan',
            'Telepon',
            'Jam Mulai',
            'Jam Selesai',
            'Total Harga',
            'Status',
            'Dibuat Oleh',
        ];
    }
}