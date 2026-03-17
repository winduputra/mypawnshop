<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $kasirs = User::where('role', 'kasir')
            ->with('cabang')
            ->when($search, fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhereHas('cabang', fn($q2) => $q2->where('nama_cabang', 'like', "%{$s}%"))
            )
            ->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('kasir._table', compact('kasirs'))->render();
        }

        return view('kasir.index', compact('kasirs'));
    }

    public function create()
    {
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('kasir.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'cabang_id'  => 'required|exists:cabangs,id',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'kasir',
            'cabang_id' => $request->cabang_id,
        ]);

        return redirect()->route('kasir.index')->with('success', 'Akun kasir berhasil dibuat.');
    }

    public function edit(User $kasir)
    {
        if ($kasir->role !== 'kasir') {
            return redirect()->route('kasir.index')->with('error', 'Akun ini bukan kasir.');
        }
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('kasir.edit', compact('kasir', 'cabangs'));
    }

    public function update(Request $request, User $kasir)
    {
        if ($kasir->role !== 'kasir') {
            return redirect()->route('kasir.index')->with('error', 'Tidak dapat mengubah akun non-kasir.');
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $kasir->id,
            'password'  => 'nullable|string|min:6|confirmed',
            'cabang_id' => 'required|exists:cabangs,id',
        ]);

        $kasir->name      = $request->name;
        $kasir->email     = $request->email;
        $kasir->cabang_id = $request->cabang_id;

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
