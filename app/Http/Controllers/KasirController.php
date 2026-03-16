<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    public function index()
    {
        $kasirs = User::where('role', 'kasir')->latest()->paginate(10);
        return view('kasir.index', compact('kasirs'));
    }

    public function create()
    {
        return view('kasir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir',
        ]);

        return redirect()->route('kasir.index')->with('success', 'Akun kasir berhasil dibuat.');
    }

    public function edit(User $kasir)
    {
        if ($kasir->role !== 'kasir') {
            return redirect()->route('kasir.index')->with('error', 'Akun ini bukan kasir.');
        }
        return view('kasir.edit', compact('kasir'));
    }

    public function update(Request $request, User $kasir)
    {
        if ($kasir->role !== 'kasir') {
            return redirect()->route('kasir.index')->with('error', 'Tidak dapat mengubah akun non-kasir.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $kasir->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $kasir->name = $request->name;
        $kasir->email = $request->email;

        if ($request->filled('password')) {
            $kasir->password = Hash::make($request->password);
        }

        $kasir->save();

        return redirect()->route('kasir.index')->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function destroy(User $kasir)
    {
        if ($kasir->role !== 'kasir') {
            return back()->with('error', 'Tidak bisa menghapus akun admin.');
        }

        $kasir->delete();
        return redirect()->route('kasir.index')->with('success', 'Akun kasir berhasil dihapus.');
    }
}
