<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSchedule;
use App\Models\MemberSessionAdjustment;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberScheduleController extends Controller
{
    public function index()
    {
        // Auto-generate booking untuk member aktif yang start_date sudah tiba
        $this->autoGenerateBookings();

        $schedules = MemberSchedule::with('field.branch')->latest()->paginate(15);
        return view('admin.member-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $fields = Field::with('branch')->active()->get();
        return view('admin.member-schedules.create', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_name' => 'required|string|max:255',
            'member_phone' => 'nullable|string|max:20',
            'field_id' => 'required|exists:fields,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_limit' => 'nullable|integer|min:1|max:12',
            'start_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        $schedule = MemberSchedule::create($request->all());
        
        // Auto generate booking mulai dari start_date
        $bookings = $schedule->generateBookingsFor30Days($request->start_date);
        
        $count = count($bookings);
        $startFormatted = \Carbon\Carbon::parse($request->start_date)->format('d F Y');
        
        return redirect()->route('admin.member-schedules.index')
            ->with('success', "Jadwal member berhasil dibuat. {$count} sesi di-generate mulai {$startFormatted}.");
    }

    public function show(MemberSchedule $memberSchedule)
    {
        $memberSchedule->load('field.branch');
        
        // Auto-complete sesi yang sudah lewat
        $memberSchedule->autoCompletePastSessions();
        
        $bookings = Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', $memberSchedule->start_date)
            ->orderBy('booking_date')
            ->get();

        $adjustments = MemberSessionAdjustment::where('member_schedule_id', $memberSchedule->id)
            ->with('adjustedByUser', 'booking')
            ->latest()
            ->get();
            
        return view('admin.member-schedules.show', compact('memberSchedule', 'bookings', 'adjustments'));
    }

    public function edit(MemberSchedule $memberSchedule)
    {
        $fields = Field::with('branch')->active()->get();
        return view('admin.member-schedules.edit', compact('memberSchedule', 'fields'));
    }

    public function update(Request $request, MemberSchedule $memberSchedule)
    {
        $request->validate([
            'member_name' => 'required|string|max:255',
            'member_phone' => 'nullable|string|max:20',
            'field_id' => 'required|exists:fields,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_limit' => 'nullable|integer|min:1|max:12',
            'start_date' => 'required|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $memberSchedule->update($request->all());

        return redirect()->route('admin.member-schedules.index')
            ->with('success', 'Jadwal member berhasil diupdate.');
    }

    public function destroy(MemberSchedule $memberSchedule)
    {
        // Batalkan booking pending yang belum berlangsung
        Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', now())
            ->where('status', 'pending')
            ->delete();

        // Hapus adjustment history
        MemberSessionAdjustment::where('member_schedule_id', $memberSchedule->id)->delete();

        // Hapus member schedule
        $name = $memberSchedule->member_name;
        $memberSchedule->delete();

        return redirect()->route('admin.member-schedules.index')
            ->with('success', "Membership {$name} berhasil dihapus.");
    }

    public function generateNext30Days(MemberSchedule $memberSchedule)
    {
        $limit = $memberSchedule->monthly_limit ?? 4;
        
        if (!$memberSchedule->canBookThisMonth()) {
            return back()->with('error', "Kuota member sudah habis ({$limit}/{$limit} sesi terpakai).");
        }
        
        $from = now()->gt($memberSchedule->start_date) ? now() : $memberSchedule->start_date;
        $bookings = $memberSchedule->generateBookingsFor30Days($from);
        $generated = count($bookings);
        $pending = $memberSchedule->getRemainingQuota();
        
        if ($generated > 0) {
            return back()->with('success', "{$generated} sesi booking berhasil di-generate. Sesi pending: {$pending}/{$limit}");
        } else {
            return back()->with('info', 'Tidak ada sesi baru yang di-generate. Semua sesi sudah ada.');
        }
    }

    public function addSession(Request $request, MemberSchedule $memberSchedule)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Cari tanggal sesi berikutnya setelah sesi terakhir
        $lastBooking = Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', $memberSchedule->start_date)
            ->orderBy('booking_date', 'desc')
            ->first();

        $nextDate = $lastBooking
            ? Carbon::parse($lastBooking->booking_date)->addWeek()
            : Carbon::parse($memberSchedule->start_date);

        if ($nextDate->dayOfWeek != $memberSchedule->day_of_week) {
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $nextDate->next($dayNames[$memberSchedule->day_of_week]);
        }

        $totalSessions = Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', $memberSchedule->start_date)
            ->count();

        $field = Field::find($memberSchedule->field_id);
        $duration = abs(Carbon::parse($memberSchedule->end_time)->diffInHours(Carbon::parse($memberSchedule->start_time)));
        if ($duration == 0) $duration = 1;
        $limit = $memberSchedule->monthly_limit ?? 4;
        $pricePerSession = $field
            ? $field->getPriceForDate($nextDate->format('Y-m-d')) * $duration
            : $memberSchedule->monthly_price / $limit;

        $booking = Booking::create([
            'field_id' => $memberSchedule->field_id,
            'user_id' => auth()->id(),
            'customer_name' => $memberSchedule->member_name,
            'customer_phone' => $memberSchedule->member_phone,
            'booking_date' => $nextDate->format('Y-m-d'),
            'start_time' => $memberSchedule->start_time,
            'end_time' => $memberSchedule->end_time,
            'total_price' => $pricePerSession,
            'status' => 'pending',
            'is_membership' => true,
            'booking_type' => 'member',
            'notes' => 'Sesi tambahan - ' . $request->reason,
        ]);

        MemberSessionAdjustment::create([
            'member_schedule_id' => $memberSchedule->id,
            'type' => 'add',
            'booking_id' => $booking->id,
            'reason' => $request->reason,
            'adjusted_by' => auth()->id(),
        ]);

        return back()->with('success', "Sesi tambahan berhasil dibuat untuk tanggal {$nextDate->format('d F Y')}.");
    }

    public function removeSession(Request $request, MemberSchedule $memberSchedule)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'required|string|max:500',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('is_membership', true)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled', 'notes' => 'Dibatalkan - ' . $request->reason]);

        MemberSessionAdjustment::create([
            'member_schedule_id' => $memberSchedule->id,
            'type' => 'remove',
            'booking_id' => $booking->id,
            'reason' => $request->reason,
            'adjusted_by' => auth()->id(),
        ]);

        return back()->with('success', "Sesi tanggal {$booking->booking_date->format('d F Y')} berhasil dibatalkan.");
    }

    private function autoGenerateBookings()
    {
        $schedules = MemberSchedule::active()
            ->where('start_date', '<=', now())
            ->get();

        foreach ($schedules as $schedule) {
            $schedule->autoCompletePastSessions();
            $schedule->generateBookingsFor30Days();
        }
    }
}