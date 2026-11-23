<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * DFD 5.0: Kelola Staf
     */
    public function index()
    {
        $staffMembers = Staff::orderBy('created_at', 'desc')->get();
        return view('admin.staff.index', compact('staffMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email',
            'password' => 'required|min:6',
            'role' => 'required|in:ADMIN,CASHIER,KITCHEN',
            'is_active' => 'nullable|boolean',
        ]);

        Staff::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return back()->with('success', "Staf '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email,' . $id . ',staff_id',
            'password' => 'nullable|min:6',
            'role' => 'required|in:ADMIN,CASHIER,KITCHEN',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password_hash'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        return back()->with('success', "Data staf '{$staff->name}' berhasil diperbarui.");
    }

    public function toggleActive($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->is_active = !$staff->is_active;
        $staff->save();

        $statusStr = $staff->is_active ? 'Aktif' : 'Non-Aktif';
        return back()->with('success', "Status staf '{$staff->name}' diubah menjadi: {$statusStr}.");
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        
        if ($staff->staff_id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $staff->name;
        $staff->delete();

        return back()->with('success', "Staf '{$name}' telah dihapus.");
    }
}

