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

            <div class="flex justify-end mb-8">
                <button type="submit" class="btn-gradient px-8 py-4 rounded-xl text-lg">
                    Simpan Batas Persentase & Biaya Admin
                </button>
            </div>
        </form>

        <!-- Tarif Ujrah Ranges Section -->
        <div class="glass-card p-8 mb-6">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                    Tarif Biaya Penitipan per Range
                </div>
            </h3>
            <p class="text-sm text-slate-500 mb-6">Pengaturan biaya penitipan (Ujrah) berdasarkan range nilai taksiran dan kategori. Jika transaksi tidak masuk dalam range ini, akan menggunakan biaya penitipan default di atas.</p>

            <!-- Table of Ranges -->
            <div class="overflow-x-auto mb-8">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5 border-b border-white/10">
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Min. Taksiran</th>
                            <th class="px-4 py-3">Max. Taksiran</th>
                            <th class="px-4 py-3">Tarif (Rp)</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($tarifUjrahs as $tarif)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-white capitalize">
                                <span class="inline-flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        {{ $tarif->kategori_barang == 'emas' ? 'bg-yellow-400' : ($tarif->kategori_barang == 'elektronik' ? 'bg-blue-400' : 'bg-green-400') }}"></span>
                                    {{ $tarif->kategori_barang }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-sky-400">Rp {{ number_format($tarif->min_taksiran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-sky-400">Rp {{ number_format($tarif->max_taksiran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-emerald-400 font-medium">Rp {{ number_format($tarif->tarif, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('tarif-ujrah.destroy', $tarif) }}" method="POST" class="inline" onsubmit="return confirm('Hapus range ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada pengaturan range tarif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Form Add New Range -->
            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                <h4 class="text-sm font-semibold text-white mb-4">Tambah Range Baru</h4>
                <form action="{{ route('tarif-ujrah.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div class="lg:col-span-1">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Kategori</label>
                            <select name="kategori_barang" class="w-full glass bg-white/5 border-white/10 rounded-xl px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                <option value="emas" class="text-dark">Emas</option>
                                <option value="elektronik" class="text-dark">Elektronik</option>
                                <option value="kendaraan" class="text-dark">Kendaraan</option>
                            </select>
                        </div>
                        <div class="lg:col-span-1">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Min. Taksiran</label>
                            <input type="text" name="min_taksiran" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="0" required>
                        </div>
                        <div class="lg:col-span-1">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Max. Taksiran</label>
                            <input type="text" name="max_taksiran" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="1000000" required>
                        </div>
                        <div class="lg:col-span-1">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Tarif Ujrah</label>
                            <input type="text" name="tarif" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="30000" required>
                        </div>
                        <div class="lg:col-span-1">
                            <button type="submit" class="w-full btn-gradient px-4 py-2.5 rounded-xl text-sm h-full flex items-center justify-center">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
</x-app-layout>
