<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CafeTable;

class TableController extends Controller
{
    public function index()
    {
        $tables = CafeTable::withCount(['orders' => function ($q) {
            $q->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'READY']);
        }])->orderBy('table_number', 'asc')->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:cafe_tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:AVAILABLE,OCCUPIED',
        ]);

        CafeTable::create($validated);

        return back()->with('success', "Meja '{$validated['table_number']}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $table = CafeTable::findOrFail($id);

        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:cafe_tables,table_number,' . $id . ',table_id',
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:AVAILABLE,OCCUPIED',
        ]);

        $table->update($validated);

        return back()->with('success', "Meja '{$table->table_number}' berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $table = CafeTable::findOrFail($id);
        $table->status = ($table->status === 'AVAILABLE') ? 'OCCUPIED' : 'AVAILABLE';
        $table->save();

        return back()->with('success', "Status meja '{$table->table_number}' diubah ke {$table->status}.");
    }

    public function destroy($id)
    {
        $table = CafeTable::findOrFail($id);
        $number = $table->table_number;
        $table->delete();

        return back()->with('success', "Meja '{$number}' telah dihapus.");
    }
}

