<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function ensureOwner(): void
    {
        if (!in_array(auth()->user()->role, ['owner', 'superadmin'])) {
            abort(403, 'Hanya Owner yang dapat mengelola admin.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureOwner();

        $search = $request->query('search');
        $admins = User::where('role', 'admin')
            ->with('cabang')
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhereHas('cabang', fn($q2) => $q2->where('nama_cabang', 'like', "%{$s}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin._table', compact('admins'))->render();
        }

        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        $this->ensureOwner();

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('admin.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $this->ensureOwner();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'cabang_id' => 'nullable|exists:cabangs,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'cabang_id' => $request->cabang_id,
        ]);

        return redirect()->route('admin.index')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $admin)
    {
        $this->ensureOwner();

        if ($admin->role !== 'admin') {
            return redirect()->route('admin.index')->with('error', 'Akun ini bukan admin.');
        }

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('admin.edit', compact('admin', 'cabangs'));
    }

    public function update(Request $request, User $admin)
    {
        $this->ensureOwner();

        if ($admin->role !== 'admin') {
            return redirect()->route('admin.index')->with('error', 'Tidak dapat mengubah akun non-admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
            'cabang_id' => 'nullable|exists:cabangs,id',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->cabang_id = $request->cabang_id;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admin.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        $this->ensureOwner();

        if ($admin->role !== 'admin') {
            return back()->with('error', 'Tidak bisa menghapus akun non-admin.');
        }

        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
