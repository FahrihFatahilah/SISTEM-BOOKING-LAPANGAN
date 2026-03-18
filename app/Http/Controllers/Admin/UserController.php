<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $users = User::with(['branch', 'roles'])->paginate(10);
        } else {
            $users = User::where('branch_id', $user->branch_id)->with(['branch', 'roles'])->paginate(10);
        }
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $branches = Branch::active()->get();
            $roles = Role::all();
        } else {
            $branches = Branch::where('id', $user->branch_id)->active()->get();
            $roles = Role::whereIn('name', ['admin', 'staff'])->get();
        }
        
        return view('admin.users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['branch', 'roles', 'bookings' => function($q) {
            $q->latest()->take(10);
        }]);
        
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->isOwner()) {
            $branches = Branch::active()->get();
            $roles = Role::all();
        } else {
            $branches = Branch::where('id', $currentUser->branch_id)->active()->get();
            $roles = Role::whereIn('name', ['admin', 'staff'])->get();
        }
        
        return view('admin.users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Tidak dapat menghapus akun sendiri.']);
        }

        // Check if user has bookings
        if ($user->bookings()->exists()) {
            return back()->withErrors(['delete' => 'Tidak dapat menghapus user yang memiliki booking.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}