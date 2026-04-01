<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSchedule;
use App\Models\Field;
use Illuminate\Http\Request;

class MemberScheduleController extends Controller
{
    public function index()
    {
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
            'member_phone' => 'required|string|max:20',
            'field_id' => 'required|exists:fields,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'monthly_price' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        $schedule = MemberSchedule::create($request->all());
        
        // Generate booking untuk 30 hari ke depan
        $bookings = $schedule->generateBookingsFor30Days($request->start_date);
        
        return redirect()->route('admin.member-schedules.index')
            ->with('success', 'Jadwal member berhasil dibuat dan ' . count($bookings) . ' booking telah di-generate untuk 30 hari ke depan.');
    }

    public function show(MemberSchedule $memberSchedule)
    {
        $memberSchedule->load('field.branch');
        
        // Ambil booking yang terkait dengan member ini
        $bookings = \App\Models\Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', now())
            ->orderBy('booking_date')
            ->get();
            
        return view('admin.member-schedules.show', compact('memberSchedule', 'bookings'));
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
            'member_phone' => 'required|string|max:20',
            'field_id' => 'required|exists:fields,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'monthly_price' => 'required|numeric|min:0',
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
        // Nonaktifkan jadwal dan batalkan booking yang belum berlangsung
        $memberSchedule->update(['is_active' => false]);
        
        \App\Models\Booking::where('field_id', $memberSchedule->field_id)
            ->where('customer_name', $memberSchedule->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', now())
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return redirect()->route('admin.member-schedules.index')
            ->with('success', 'Jadwal member berhasil dinonaktifkan dan booking mendatang dibatalkan.');
    }

    public function generateNext30Days(MemberSchedule $memberSchedule)
    {
        $remaining = $memberSchedule->getRemainingQuota();
        
        if ($remaining <= 0) {
            return back()->with('error', 'Kuota member untuk bulan ini sudah habis (4/4 sesi terpakai).');
        }
        
        $bookings = $memberSchedule->generateBookingsFor30Days();
        $generated = count($bookings);
        
        if ($generated > 0) {
            return back()->with('success', "{$generated} sesi booking berhasil di-generate. Sisa kuota: " . ($remaining - $generated) . "/4");
        } else {
            return back()->with('info', 'Tidak ada sesi baru yang di-generate. Mungkin sudah ada booking atau kuota habis.');
        }
    }
}