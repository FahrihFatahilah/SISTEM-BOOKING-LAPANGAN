<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserMembership;
use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserMembershipController extends Controller
{
    public function index()
    {
        $memberships = UserMembership::with(['user', 'membershipPackage'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);
        return view('admin.user-memberships.index', compact('memberships'));
    }

    public function create()
    {
        $users = User::whereDoesntHave('roles', function($query) {
            $query->whereIn('name', ['owner', 'admin', 'staff']);
        })->get();
        $packages = MembershipPackage::active()->get();
        
        return view('admin.user-memberships.create', compact('users', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_package_id' => 'required|exists:membership_packages,id',
            'start_date' => 'required|date'
        ]);

        $package = MembershipPackage::findOrFail($request->membership_package_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays($package->duration_days);

        UserMembership::create([
            'user_id' => $request->user_id,
            'membership_package_id' => $request->membership_package_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'remaining_quota' => $package->booking_quota,
            'status' => 'active'
        ]);

        return redirect()->route('admin.user-memberships.index')
                        ->with('success', 'Membership user berhasil dibuat');
    }

    public function show(UserMembership $userMembership)
    {
        return view('admin.user-memberships.show', compact('userMembership'));
    }

    public function destroy(UserMembership $userMembership)
    {
        $userMembership->delete();

        return redirect()->route('admin.user-memberships.index')
                        ->with('success', 'Membership user berhasil dihapus');
    }
}