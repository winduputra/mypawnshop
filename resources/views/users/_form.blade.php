<div>
    <label class="block text-sm text-slate-500 mb-1">Nama</label>
    <input name="name" value="{{ old('name', $user->name ?? '') }}" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800" required>
    @error('name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm text-slate-500 mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800" required>
    @error('email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm text-slate-500 mb-1">Role</label>
    <select name="role" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800" required>
        @foreach($roles as $role)<option value="{{ $role }}" {{ old('role', $user->role ?? '') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>@endforeach
    </select>
    @error('role')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm text-slate-500 mb-1">Cabang</label>
    <select name="cabang_id" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800">
        <option value="">Tanpa/Semua Cabang</option>
        @foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" {{ (string) old('cabang_id', $user->cabang_id ?? '') === (string) $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>@endforeach
    </select>
    @error('cabang_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm text-slate-500 mb-1">Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }}</label>
    <input type="password" name="password" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800" {{ $user ? '' : 'required' }}>
    @error('password')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm text-slate-500 mb-1">Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800" {{ $user ? '' : 'required' }}>
</div>
