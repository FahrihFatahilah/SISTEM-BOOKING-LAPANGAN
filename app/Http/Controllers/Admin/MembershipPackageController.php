<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;

class MembershipPackageController extends Controller
{
    public function index()
    {
        $packages = MembershipPackage::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.membership-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.membership-packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'booking_quota' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        MembershipPackage::create($request->all());

        return redirect()->route('admin.membership-packages.index')
                        ->with('success', 'Paket membership berhasil dibuat');
    }

    public function show(MembershipPackage $membershipPackage)
    {
        return view('admin.membership-packages.show', compact('membershipPackage'));
    }

    public function edit(MembershipPackage $membershipPackage)
    {
        return view('admin.membership-packages.edit', compact('membershipPackage'));
    }

    public function update(Request $request, MembershipPackage $membershipPackage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'booking_quota' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $membershipPackage->update($request->all());

        return redirect()->route('admin.membership-packages.index')
                        ->with('success', 'Paket membership berhasil diupdate');
    }

    public function destroy(MembershipPackage $membershipPackage)
    {
        $membershipPackage->delete();

        return redirect()->route('admin.membership-packages.index')
                        ->with('success', 'Paket membership berhasil dihapus');
    }
}