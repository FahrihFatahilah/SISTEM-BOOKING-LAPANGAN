<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateBookingStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = now();
        
        // Update pending bookings to ongoing if current time is between start and end time
        $pendingBookings = Booking::where('status', 'pending')
            ->whereDate('booking_date', $now->toDateString())
            ->get();
            
        foreach ($pendingBookings as $booking) {
            $bookingStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
            $bookingEnd = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);
            
            if ($now->between($bookingStart, $bookingEnd)) {
                $booking->update(['status' => 'ongoing']);
                \Log::info("Booking {$booking->id} status updated to ongoing");
            }
        }
        
        // Update ongoing bookings to completed if current time is past end time
        $ongoingBookings = Booking::where('status', 'ongoing')
            ->whereDate('booking_date', $now->toDateString())
            ->get();
            
        foreach ($ongoingBookings as $booking) {
            $bookingEnd = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);
            
            if ($now->greaterThan($bookingEnd)) {
                $booking->update(['status' => 'completed']);
                \Log::info("Booking {$booking->id} status updated to completed");
            }
        }
    }
}