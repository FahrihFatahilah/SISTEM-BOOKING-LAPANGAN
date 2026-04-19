<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Sale;
use App\Models\Branch;
use Illuminate\Http\Request;
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
            'status' => 'nullable|in:pending,ongoing,completed,cancelled',
            'booking_type' => 'nullable|in:regular,member',
        ]);

        $user = auth()->user();

        $query = Booking::with(['field.branch', 'user'])
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        if (!$user->isOwner()) {
            $query->whereHas('field', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('field', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('booking_type')) {
            $query->where('is_membership', $request->booking_type === 'member');
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $summary = [
            'total_bookings' => $bookings->count(),
            'member_bookings' => $bookings->where('is_membership', true)->count(),
            'regular_bookings' => $bookings->where('is_membership', false)->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'ongoing_bookings' => $bookings->where('status', 'ongoing')->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'regular_revenue' => $bookings->where('is_membership', false)->whereIn('status', ['pending', 'ongoing', 'completed'])->sum('total_price'),
            'member_revenue' => $bookings->where('is_membership', true)->whereIn('status', ['pending', 'ongoing', 'completed'])->sum('total_price'),
            'total_revenue' => $bookings->whereIn('status', ['pending', 'ongoing', 'completed'])->sum('total_price'),
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

        // === BOOKING REVENUE ===
        $bookingQuery = Booking::with(['field.branch'])
            ->whereIn('status', ['pending', 'ongoing', 'completed'])
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        if (!$user->isOwner()) {
            $bookingQuery->whereHas('field', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('branch_id')) {
            $bookingQuery->whereHas('field', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->type === 'daily') {
            $bookingRevenues = $bookingQuery->selectRaw('DATE(booking_date) as date,
                SUM(CASE WHEN is_membership = 0 THEN total_price ELSE 0 END) as regular_revenue,
                SUM(CASE WHEN is_membership = 1 THEN total_price ELSE 0 END) as member_revenue,
                SUM(total_price) as total_revenue,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN is_membership = 0 THEN 1 ELSE 0 END) as regular_bookings,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_bookings')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $bookingRevenues = $bookingQuery->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month,
                SUM(CASE WHEN is_membership = 0 THEN total_price ELSE 0 END) as regular_revenue,
                SUM(CASE WHEN is_membership = 1 THEN total_price ELSE 0 END) as member_revenue,
                SUM(total_price) as total_revenue,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN is_membership = 0 THEN 1 ELSE 0 END) as regular_bookings,
                SUM(CASE WHEN is_membership = 1 THEN 1 ELSE 0 END) as member_bookings')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        // === SALES/POS REVENUE ===
        $salesQuery = Sale::whereBetween('created_at', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ]);

        if (!$user->isOwner()) {
            $salesQuery->where('branch_id', $user->branch_id);
        }

        if ($request->filled('branch_id')) {
            $salesQuery->where('branch_id', $request->branch_id);
        }

        if ($request->type === 'daily') {
            $salesRevenues = $salesQuery->selectRaw('DATE(created_at) as date,
                SUM(total) as sales_revenue,
                COUNT(*) as total_sales')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
        } else {
            $salesRevenues = $salesQuery->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month,
                SUM(total) as sales_revenue,
                COUNT(*) as total_sales')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);

        return view('admin.reports.revenue-report', compact('bookingRevenues', 'salesRevenues', 'branches', 'request'));
    }

    public function exportBookingPdf(Request $request)
    {
        $user = auth()->user();

        $query = Booking::with(['field.branch', 'user'])
            ->whereBetween('booking_date', [$request->start_date, $request->end_date]);

        if (!$user->isOwner()) {
            $query->whereHas('field', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('field', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $summary = [
            'total_bookings' => $bookings->count(),
            'member_bookings' => $bookings->where('is_membership', true)->count(),
            'regular_bookings' => $bookings->where('is_membership', false)->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'total_revenue' => $bookings->where('is_membership', false)->whereIn('status', ['pending', 'ongoing', 'completed'])->sum('total_price'),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf.booking-report', compact('bookings', 'summary', 'request'));

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }

    public function exportBookingExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new BookingExport($request), 'laporan-booking-' . date('Y-m-d') . '.xlsx');
    }
}

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
            $query->whereHas('field', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        return $query->orderBy('booking_date', 'desc')->get()->map(function ($booking) {
            return [
                'Tanggal' => $booking->booking_date->format('d/m/Y'),
                'Tipe' => $booking->is_membership ? 'Member' : 'Regular',
                'Cabang' => $booking->field->branch->name,
                'Lapangan' => $booking->field->name,
                'Pelanggan' => $booking->customer_name,
                'Telepon' => $booking->customer_phone ?? '-',
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
        return ['Tanggal', 'Tipe', 'Cabang', 'Lapangan', 'Pelanggan', 'Telepon', 'Jam Mulai', 'Jam Selesai', 'Total Harga', 'Status', 'Dibuat Oleh'];
    }
}
