<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Order::with('items.product')->latest()->get();
        $tables = Table::where('status', 'available')->orderBy('number')->get();
        return view('admin.transaction', compact('transactions', 'tables'));
    }

    public function create()
    {
        return view('admin.transactions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_type' => 'required|in:cash,qris',
            'table_number' => 'nullable|integer'
        ]);

        Order::create($request->all());
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Order $transaction)
    {
        return view('admin.transactions.edit', compact('transaction'));
    }

    public function update(Request $request, Order $transaction)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_type' => 'required|in:cash,qris',
            'table_number' => 'nullable|integer'
        ]);

        $transaction->update($request->all());
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diupdate.');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'nullable|exists:orders,id',
            'customer_name' => 'required|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_type' => 'required|in:cash,qris',
            'table_number' => 'nullable|integer',
        ]);

        if ($request->filled('transaction_id')) {
            $transaction = Order::find($request->transaction_id);
            $transaction->update($validated);
        } else {
            Order::create($validated);
        }

        return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    }


    public function destroy(Order $transaction)
    {

    $transaction->items()->delete();

    // Lalu hapus order-nya
    $transaction->delete();

        $transaction->delete();
            return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new TransactionsExport, 'laporan_transaksi.xlsx');
    }
}
