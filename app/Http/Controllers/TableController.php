<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables');
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|unique:tables,number',
        ]);

        Table::create([
            'number' => $request->number,
            'status' => 'available',
        ]);

        return redirect()->route('admin.tables')->with('success', 'Table added');
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'number' => 'required|integer|min:0|unique:tables,number,' . $table->id,
            'status' => 'required|in:available,unavailable'
        ]);

        $table->update($request->all());

        return redirect()->route('admin.tables.index')->with('success', 'Table updated');
    }

   public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:tables,id',
            'number' => 'required|integer|min:0|unique:tables,number,' . $request->id,
            'status' => 'required|in:available,unavailable',
        ]);

        if ($request->id) {
            $table = Table::find($request->id);
            $table->update($validated);
        } else {
            Table::create($validated);
        }

        return redirect()->route('admin.tables.index')->with('success', 'Data meja berhasil disimpan.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Data meja berhasil dihapus.');
    }

    public function qrcode(Table $table)
    {
        $url = url('/product') . '?table=' . $table->number; // ini akan di-scan user
        return view('admin.qrcode', compact('url', 'table'));
    }

    public function setSelectedAvailable(Request $request)
    {
        $ids = $request->input('table_ids');

        if ($ids && is_array($ids)) {
            Table::whereIn('id', $ids)->update(['status' => 'available']);
            return redirect()->route('admin.tables.index')->with('success', 'Meja terpilih berhasil diubah menjadi tersedia.');
        }

        return redirect()->route('admin.tables.index')->with('success', 'Tidak ada meja yang dipilih.');
    }


}


