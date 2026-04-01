<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserMembership;
use App\Models\User;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserMembershipController extends Controller
{
    public function index()
    {
        $memberships = UserMembership::with(['user', 'field'])
                                   ->latest()
                                   ->paginate(10);
        return view('admin.user-memberships.index', compact('memberships'));
    }

    public function create()
    {
        $users = User::role('customer')->get();
        $fields = Field::where('is_active', true)->get();
        
        return view('admin.user-memberships.create', compact('users', 'fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'field_id' => 'required|exists:fields,id',
            'start_date' => 'required|date|after_or_equal:today',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'session_duration_hours' => 'required|numeric|min:0.5|max:8',
            'monthly_price' => 'required|numeric|min:0',
        ]);

        $membership = UserMembership::create([
            'user_id' => $request->user_id,
            'field_id' => $request->field_id,
            'start_date' => $request->start_date,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'session_duration_hours' => $request->session_duration_hours,
            'monthly_price' => $request->monthly_price,
            'status' => 'active'
        ]);

        // Generate booking untuk bulan ini
        $membership->generateMonthlyBookings();

        return redirect()->route('admin.user-memberships.index')
            ->with('success', 'Membership berhasil ditambahkan dan jadwal bulan ini telah dibuat.');
    }

    public function show(UserMembership $userMembership)
    {
        $userMembership->load(['user', 'field', 'membershipBookings']);
        return view('admin.user-memberships.show', compact('userMembership'));
    }

    public function destroy(UserMembership $userMembership)
    {
        $userMembership->update(['status' => 'cancelled']);
        $userMembership->membershipBookings()
                      ->where('status', 'scheduled')
                      ->update(['status' => 'cancelled']);

        return redirect()->route('admin.user-memberships.index')
            ->with('success', 'Membership berhasil dibatalkan.');
    }

    public function generateNextMonth(UserMembership $userMembership)
    {
        $nextMonth = now()->addMonth();
        $count = $userMembership->generateMonthlyBookings($nextMonth->month, $nextMonth->year);
        
        return back()->with('success', "Berhasil membuat {$count} jadwal untuk bulan depan.");
    }
}