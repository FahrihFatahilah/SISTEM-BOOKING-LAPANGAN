<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $branches = null;
        $selectedBranch = null;

        if ($user->hasRole(['owner', 'admin'])) {
            $branches = Branch::active()->get();
            $selectedBranch = $request->branch_id ?? $branches->first()?->id;
        } else {
            $selectedBranch = $user->branch_id;
        }

        $products = Product::active()
            ->where('branch_id', $selectedBranch)
            ->where('display_stock', '>', 0)
            ->get();
            
        return view('admin.pos.index', compact('products', 'branches', 'selectedBranch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id ?? Product::find($validated['items'][0]['product_id'])->branch_id;

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'total' => $validated['total'],
                'paid' => $validated['paid'],
                'change' => $validated['paid'] - $validated['total'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->display_stock < $item['quantity']) {
                    throw new \Exception("Stok display {$product->name} tidak mencukupi (tersedia: {$product->display_stock})");
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->selling_price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->selling_price * $item['quantity'],
                ]);

                $product->reduceStock($item['quantity']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Transaksi gagal. Silakan coba lagi.'], 422);
        }
    }

    public function print(Sale $sale)
    {
        $sale->load('items', 'branch', 'user');
        return view('admin.pos.print', compact('sale'));
    }

    public function sales()
    {
        $sales = Sale::with('user', 'branch')
            ->latest()
            ->paginate(20);
        return view('admin.pos.sales', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load('items', 'branch', 'user');
        return view('admin.pos.show', compact('sale'));
    }
}
