<x-app-layout>
    @section('header_title', 'Edit Admin')

    @section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">Kembali ke Daftar</a>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Akun Admin</h3>
            <form action="{{ route('admin.update', $admin) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:border-sky-500 focus:ring-sky-500" required>
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:border-sky-500 focus:ring-sky-500" required>
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-2">Cabang <span class="text-slate-400">(opsional)</span></label>
                    <select name="cabang_id" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                        <option value="">Semua cabang</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ old('cabang_id', $admin->cabang_id) == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                    @error('cabang_id') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-2">Password Baru <span class="text-slate-600">(Kosongkan jika tidak ingin ubah)</span></label>
                    <input type="password" name="password" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                    @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold rounded-xl shadow-sm transition-all w-full py-4 text-lg">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    @endsection
</x-app-layout>
