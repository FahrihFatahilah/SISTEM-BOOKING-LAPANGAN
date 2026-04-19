<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Branch;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = StockTransfer::with(['product', 'branch', 'user']);

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('transfer_date', $request->date);
        }

        if ($request->filled('branch_id') && $user->isOwner()) {
            $query->where('branch_id', $request->branch_id);
        }

        $transfers = $query->latest()->paginate(20);
        $branches = $user->isOwner() ? Branch::active()->get() : collect([$user->branch]);

        return view('admin.stock-transfers.index', compact('transfers', 'branches'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->isOwner()) {
            $products = Product::with('branch')->active()->where('warehouse_stock', '>', 0)->get();
        } else {
            $products = Product::where('branch_id', $user->branch_id)->active()->where('warehouse_stock', '>', 0)->get();
        }

        return view('admin.stock-transfers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $errors = [];

        foreach ($request->items as $i => $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->warehouse_stock < $item['quantity']) {
                $errors[] = "Stok gudang {$product->name} tidak cukup (tersedia: {$product->warehouse_stock})";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors(['items' => implode(', ', $errors)])->withInput();
        }

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            StockTransfer::create([
                'product_id' => $product->id,
                'branch_id' => $product->branch_id,
                'user_id' => auth()->id(),
                'quantity' => $item['quantity'],
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes,
            ]);

            $product->transferToDisplay($item['quantity']);
        }

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', 'Pemindahan stok berhasil');
    }

    public function opname(Request $request)
    {
        $user = auth()->user();

        if ($user->isOwner()) {
            $products = Product::with('branch')->active()->get();
        } else {
            $products = Product::where('branch_id', $user->branch_id)->active()->get();
        }

        $branches = $user->isOwner() ? Branch::active()->get() : collect([$user->branch]);

        return view('admin.stock-transfers.opname', compact('products', 'branches'));
    }
}
