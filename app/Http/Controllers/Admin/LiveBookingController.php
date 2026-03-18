<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiveBookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Booking::with(['field.branch', 'user'])
            ->whereDate('booking_date', today())
            ->orderBy('start_time');
        
        // Filter berdasarkan role
        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $todayBookings = $query->get();
        
        return view('admin.live-booking.index', compact('todayBookings'));
    }

    public function getData()
    {
        try {
            $user = auth()->user();
            
            $query = Booking::with(['field.branch', 'user'])
                ->whereDate('booking_date', today())
                ->orderBy('start_time');
            
            // Filter berdasarkan role
            if (!$user->isOwner()) {
                $query->whereHas('field', function($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            }

            $bookings = $query->get()->map(function ($booking) {
                $now = now();
                $bookingStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
                $bookingEnd = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);
                
                return [
                    'id' => $booking->id,
                    'field_name' => $booking->field->name,
                    'branch_name' => $booking->field->branch->name,
                    'customer_name' => $booking->customer_name,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'status' => $booking->status,
                    'status_badge' => $this->getStatusBadge($booking->status),
                    'time_remaining' => $this->getTimeRemaining($booking, $now),
                ];
            });

            return response()->json([
                'bookings' => $bookings,
                'last_updated' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            \Log::error('Live booking error: ' . $e->getMessage());
            return response()->json([
                'bookings' => [],
                'last_updated' => now()->format('H:i:s'),
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'pending' => '<span class="badge bg-warning text-dark">🔵 Akan Datang</span>',
            'ongoing' => '<span class="badge bg-success">🟢 Berjalan</span>',
            'completed' => '<span class="badge bg-secondary">🔴 Selesai</span>',
            'cancelled' => '<span class="badge bg-danger">❌ Dibatalkan</span>',
            default => '<span class="badge bg-light text-dark">Unknown</span>'
        };
    }

    private function getTimeRemaining($booking, $now)
    {
        $bookingEnd = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);
        
        if ($booking->status === 'ongoing') {
            $diff = $now->diffInMinutes($bookingEnd, false);
            if ($diff > 0) {
                return "Sisa {$diff} menit";
            } else {
                return "Selesai";
            }
        } elseif ($booking->status === 'pending') {
            $bookingStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
            $diff = $now->diffInMinutes($bookingStart, false);
            if ($diff > 0) {
                return "Mulai dalam {$diff} menit";
            } else {
                return "Dimulai";
            }
        }
        
        return '-';
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,ongoing,completed,cancelled'
        ]);

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diupdate'
        ]);
    }
}