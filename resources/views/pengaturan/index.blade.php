<x-app-layout>
    @section('header_title', 'Pengaturan Sistem')

    @section('content')
    <div class="max-w-3xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('pengaturan.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Biaya Admin Section -->
            <div class="glass-card p-8 mb-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Biaya Administrasi
                </h3>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Biaya Admin (Rp)</label>
                    <input type="text" name="biaya_admin" value="{{ $settings['biaya']->firstWhere('key', 'biaya_admin')->value ?? 10000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('biaya_admin') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Ujrah / Biaya Penitipan Section -->
            <div class="glass-card p-8 mb-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Biaya Penitipan (Ujrah)
                </h3>
                <p class="text-sm text-slate-500 mb-6">Biaya Penitipan Per 30 Hari</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>
                                Emas (Rp/30 hari)
                            </span>
                        </label>
                        <input type="text" name="ujrah_emas" value="{{ $settings['ujrah']->firstWhere('key', 'ujrah_emas')->value ?? 50000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('ujrah_emas') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-blue-400 mr-2"></span>
                                Elektronik (Rp/30 hari)
                            </span>
                        </label>
                        <input type="text" name="ujrah_elektronik" value="{{ $settings['ujrah']->firstWhere('key', 'ujrah_elektronik')->value ?? 75000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('ujrah_elektronik') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-green-400 mr-2"></span>
                                Kendaraan (Rp/30 hari)
                            </span>
                        </label>
                        <input type="text" name="ujrah_kendaraan" value="{{ $settings['ujrah']->firstWhere('key', 'ujrah_kendaraan')->value ?? 100000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('ujrah_kendaraan') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Persentase Section -->
            <div class="glass-card p-8 mb-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    Persentase Maksimal Pinjaman
                </h3>
                <p class="text-sm text-slate-500 mb-6">Persentase maksimal pinjaman dari nilai taksiran untuk setiap kategori barang jaminan.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>
                                Emas (%)
                            </span>
                        </label>
                        <input type="number" name="persentase_emas" value="{{ $settings['persentase']->firstWhere('key', 'persentase_emas')->value ?? 85 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('persentase_emas') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-blue-400 mr-2"></span>
                                Elektronik (%)
                            </span>
                        </label>
                        <input type="number" name="persentase_elektronik" value="{{ $settings['persentase']->firstWhere('key', 'persentase_elektronik')->value ?? 70 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('persentase_elektronik') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full bg-green-400 mr-2"></span>
                                Kendaraan (%)
                            </span>
                        </label>
                        <input type="number" name="persentase_kendaraan" value="{{ $settings['persentase']->firstWhere('key', 'persentase_kendaraan')->value ?? 75 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                        @error('persentase_kendaraan') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-gradient px-8 py-4 rounded-xl text-lg">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
    @endsection
</x-app-layout>
