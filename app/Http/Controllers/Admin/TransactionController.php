<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Branch;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Transaction::with(['branch', 'user', 'booking']);
        
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        if ($request->filled('branch_id') && $user->isOwner()) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);
        
        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);

        // Summary
        $summary = [
            'total_income' => $query->clone()->income()->sum('amount'),
            'total_expense' => $query->clone()->expense()->sum('amount'),
            'net_profit' => $query->clone()->income()->sum('amount') - $query->clone()->expense()->sum('amount')
        ];

        return view('admin.transactions.index', compact('transactions', 'branches', 'summary'));
    }

    public function create()
    {
        $user = auth()->user();
        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);
        
        return view('admin.transactions.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        Transaction::create([
            'branch_id' => $request->branch_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['branch', 'user', 'booking']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $user = auth()->user();
        $branches = $user->isOwner() ? Branch::all() : collect([$user->branch]);
        
        return view('admin.transactions.edit', compact('transaction', 'branches'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $transaction->update($request->all());

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil diupdate.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}