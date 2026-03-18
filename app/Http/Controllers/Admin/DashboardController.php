<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Field;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Base query berdasarkan role
        if ($user->isOwner()) {
            $bookingsQuery = Booking::query();
            $branchesQuery = Branch::query();
            $fieldsQuery = Field::query();
        } else {
            // Admin dan Staff hanya melihat data cabang mereka
            $bookingsQuery = Booking::whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
            $branchesQuery = Branch::where('id', $user->branch_id);
            $fieldsQuery = Field::where('branch_id', $user->branch_id);
        }

        $stats = [
            'total_bookings_today' => $bookingsQuery->clone()->today()->count(),
            'ongoing_bookings' => $bookingsQuery->clone()->ongoing()->count(),
            'completed_bookings_today' => $bookingsQuery->clone()->today()->completed()->count(),
            'total_revenue_today' => $bookingsQuery->clone()->today()->completed()->sum('total_price'),
            'total_branches' => $branchesQuery->count(),
            'total_fields' => $fieldsQuery->count(),
        ];

        $recentBookings = $bookingsQuery->clone()
            ->with(['field.branch', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}