<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    private array $roles = ['kasir', 'admin', 'owner', 'superadmin', 'superuser'];

    private function ensureSuperuser(): void
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'superuser'])) {
            abort(403, 'Hanya Superuser yang dapat mengelola semua user.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureSuperuser();
        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::with('cabang')
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($search, fn($q, $s) => $q->where(fn($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', ['users' => $users, 'roles' => $this->roles]);
    }

    public function create()
    {
        $this->ensureSuperuser();
        return view('users.create', ['roles' => $this->roles, 'cabangs' => Cabang::orderBy('nama_cabang')->get()]);
    }

    public function store(Request $request)
    {
        $this->ensureSuperuser();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:' . implode(',', $this->roles),
            'cabang_id' => 'nullable|exists:cabangs,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->ensureSuperuser();
        return view('users.edit', ['user' => $user, 'roles' => $this->roles, 'cabangs' => Cabang::orderBy('nama_cabang')->get()]);
    }

    public function update(Request $request, User $user)
    {
        $this->ensureSuperuser();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:' . implode(',', $this->roles),
            'cabang_id' => 'nullable|exists:cabangs,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->ensureSuperuser();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
