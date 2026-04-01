<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use App\Models\Field;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    public function index()
    {
        $rules = PricingRule::with('field')->latest()->paginate(10);
        return view('admin.pricing-rules.index', compact('rules'));
    }

    public function create()
    {
        $fields = Field::where('is_active', true)->get();
        return view('admin.pricing-rules.create', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'rule_name' => 'required|string|max:255',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:10'
        ]);

        PricingRule::create($request->all());

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Aturan harga berhasil ditambahkan.');
    }

    public function edit(PricingRule $pricingRule)
    {
        $fields = Field::where('is_active', true)->get();
        return view('admin.pricing-rules.edit', compact('pricingRule', 'fields'));
    }

    public function update(Request $request, PricingRule $pricingRule)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'rule_name' => 'required|string|max:255',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $pricingRule->update($request->all());

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Aturan harga berhasil diupdate.');
    }

    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Aturan harga berhasil dihapus.');
    }

    public function getPrice(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        $dayOfWeek = \Carbon\Carbon::parse($request->date)->dayOfWeek;
        $price = PricingRule::getPriceForDateTime(
            $request->field_id,
            $dayOfWeek,
            $request->time
        );

        return response()->json([
            'price_per_hour' => $price,
            'formatted_price' => 'Rp ' . number_format($price, 0, ',', '.')
        ]);
    }
}