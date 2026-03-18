<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Branch;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $fields = Field::with('branch')->paginate(10);
        } else {
            $fields = Field::where('branch_id', $user->branch_id)->with('branch')->paginate(10);
        }
        
        return view('admin.fields.index', compact('fields'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $branches = Branch::active()->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->active()->get();
        }
        
        return view('admin.fields.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        Field::create($request->all());

        return redirect()->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function show(Field $field)
    {
        $field->load(['branch', 'bookings' => function($q) {
            $q->latest()->take(10);
        }]);
        
        return view('admin.fields.show', compact('field'));
    }

    public function edit(Field $field)
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $branches = Branch::active()->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->active()->get();
        }
        
        return view('admin.fields.edit', compact('field', 'branches'));
    }

    public function update(Request $request, Field $field)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $field->update($request->all());

        return redirect()->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil diupdate.');
    }

    public function destroy(Field $field)
    {
        // Check if field has active bookings
        $hasActiveBookings = $field->bookings()->whereIn('status', ['pending', 'ongoing'])->exists();
        
        if ($hasActiveBookings) {
            return back()->withErrors(['delete' => 'Tidak dapat menghapus lapangan yang memiliki booking aktif.']);
        }

        $field->delete();

        return redirect()->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}