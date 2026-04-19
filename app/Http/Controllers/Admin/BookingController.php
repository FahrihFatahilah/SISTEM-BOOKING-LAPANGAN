<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Booking::with(['field.branch', 'user']);
        
        // Filter berdasarkan role
        if (!$user->isOwner()) {
            $query->whereHas('field', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Filter berdasarkan parameter
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('booking_type')) {
            if ($request->booking_type === 'member') {
                $query->where('is_membership', true);
            } elseif ($request->booking_type === 'regular') {
                $query->where('is_membership', false);
            }
        }

        if ($request->filled('branch_id') && $user->isOwner()) {
            $query->whereHas('field', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $bookings = $query->latest()->paginate(15);
        
        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);

        return view('admin.bookings.index', compact('bookings', 'branches'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $fields = Field::with('branch')->active()->get();
        } else {
            $fields = Field::where('branch_id', $user->branch_id)->active()->get();
        }

        return view('admin.bookings.create', compact('fields'));
    }

    public function store(Request $request)
    {
        \Log::info('Booking store request:', $request->all());
        
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'booking_type' => 'required|in:regular,member',
        ]);

        $field = Field::findOrFail($request->field_id);
        
        // Validasi ketersediaan lapangan
        if (!$field->isAvailable($request->booking_date, $request->start_time, $request->end_time)) {
            return back()->withErrors(['availability' => 'Lapangan tidak tersedia pada waktu tersebut. Mungkin sudah ada booking reguler atau jadwal membership.'])->withInput();
        }
        
        // Khusus untuk booking reguler, cek konflik dengan jadwal member
        if ($request->booking_type === 'regular') {
            $dayOfWeek = \Carbon\Carbon::parse($request->booking_date)->dayOfWeek;
            $memberConflict = \App\Models\MemberSchedule::where('field_id', $request->field_id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->where(function($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q2) use ($request) {
                          $q2->where('start_time', '<=', $request->start_time)
                             ->where('end_time', '>=', $request->end_time);
                      });
                })
                ->first();
                
            if ($memberConflict) {
                return back()->withErrors(['availability' => "Waktu tersebut bentrok dengan jadwal member tetap: {$memberConflict->member_name} setiap {$memberConflict->day_name} ({$memberConflict->start_time} - {$memberConflict->end_time})"])->withInput();
            }
        }
        
        // Hitung total harga berdasarkan weekday/weekend
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = abs($endTime->diffInHours($startTime));
        if ($duration == 0) $duration = 1;
        $totalPrice = $duration * $field->getPriceForDate($request->booking_date);

        $booking = Booking::create([
            'field_id' => $request->field_id,
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'notes' => $request->notes,
            'is_membership' => $request->booking_type === 'member',
            'booking_type' => $request->booking_type,
        ]);

        \Log::info('Booking created:', $booking->toArray());

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dibuat.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['field.branch', 'user']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $fields = Field::with('branch')->active()->get();
        } else {
            $fields = Field::where('branch_id', $user->branch_id)->active()->get();
        }

        return view('admin.bookings.edit', compact('booking', 'fields'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:pending,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
            'booking_type' => 'required|in:regular,member',
        ]);

        $field = Field::findOrFail($request->field_id);
        
        // Validasi ketersediaan lapangan (exclude booking saat ini)
        if (!$field->isAvailable($request->booking_date, $request->start_time, $request->end_time, $booking->id)) {
            return back()->withErrors(['availability' => 'Lapangan tidak tersedia pada waktu tersebut. Mungkin sudah ada booking reguler atau jadwal membership.'])->withInput();
        }

        // Hitung ulang total harga berdasarkan weekday/weekend
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = abs($endTime->diffInHours($startTime));
        if ($duration == 0) $duration = 1;
        $totalPrice = $duration * $field->getPriceForDate($request->booking_date);

        $booking->update([
            'field_id' => $request->field_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => $totalPrice,
            'status' => $request->status,
            'notes' => $request->notes,
            'is_membership' => $request->booking_type === 'member',
            'booking_type' => $request->booking_type,
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diupdate.');
    }

    public function destroy(Booking $booking)
    {
        $user = auth()->user();
        
        // Staff tidak bisa menghapus booking
        if ($user->isStaff()) {
            return back()->withErrors(['permission' => 'Anda tidak memiliki izin untuk menghapus booking.']);
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dihapus.');
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_booking_id' => 'nullable|exists:bookings,id'
        ]);

        $field = Field::findOrFail($request->field_id);
        $isAvailable = $field->isAvailable(
            $request->booking_date,
            $request->start_time,
            $request->end_time,
            $request->exclude_booking_id
        );

        $conflicts = [];
        if (!$isAvailable) {
            $conflicts = $field->getConflictingSchedules(
                $request->booking_date,
                $request->start_time,
                $request->end_time,
                $request->exclude_booking_id
            );
        }

        return response()->json([
            'available' => $isAvailable,
            'conflicts' => $conflicts,
            'message' => $isAvailable 
                ? 'Lapangan tersedia' 
                : 'Lapangan tidak tersedia pada waktu tersebut'
        ]);
    }
}