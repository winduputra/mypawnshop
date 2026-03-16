<x-app-layout>
    @section('header_title', 'Tambah Kasir')

    @section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('kasir.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-white mb-6">Buat Akun Kasir Baru</h3>

            <form action="{{ route('kasir.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Password</label>
                    <input type="password" name="password" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-gradient w-full py-4 text-lg">
                        Buat Akun Kasir
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection
</x-app-layout>
