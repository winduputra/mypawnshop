<x-app-layout>
@section('header_title', 'Pengaturan Sistem')
@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pengaturan.update') }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Biaya Admin per Kategori --}}
            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Biaya Admin per Kategori
                </h3>
                <p class="text-xs text-slate-500 mb-4">Biaya administrasi flat berdasarkan jenis barang jaminan.</p>
                <div class="space-y-4">
                    @php $ba = $settings['biaya_admin'] ?? collect(); @endphp
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block mr-1"></span>Elektronik (Rp)</label>
                        <input type="text" name="biaya_admin_elektronik" value="{{ $ba->firstWhere('key','biaya_admin_elektronik')->value ?? 35000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block mr-1"></span>Emas (Rp)</label>
                        <input type="text" name="biaya_admin_emas" value="{{ $ba->firstWhere('key','biaya_admin_emas')->value ?? 25000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-green-400 inline-block mr-1"></span>Kendaraan (Rp)</label>
                        <input type="text" name="biaya_admin_kendaraan" value="{{ $ba->firstWhere('key','biaya_admin_kendaraan')->value ?? 50000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                    </div>
                </div>
            </div>

            {{-- Ijarah + Persentase Plafon --}}
            <div class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Biaya Ijarah (Penitipan)
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Persentase dari nilai taksiran, ditagihkan setiap tenor 30 hari.</p>
                    @php $ij = $settings['ijarah'] ?? collect(); @endphp
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Persentase Ijarah (%)</label>
                        <input type="number" step="0.1" name="ijarah_persen" value="{{ $ij->firstWhere('key','ijarah_persen')->value ?? 2 }}" min="0" max="100" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                        <p class="text-[10px] text-slate-500 mt-1">Contoh: 2% dari taksiran 10.000.000 = Rp 200.000 /30 hari</p>
                    </div>
                </div>

                <div class="glass-card p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        Plafon Pinjaman (QARD)
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Persentase maksimal pinjaman dari nilai taksiran per kategori.</p>
                    @php $ps = $settings['persentase'] ?? collect(); @endphp
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block mr-1"></span>Emas (%)</label>
                            <input type="number" name="persentase_emas" value="{{ $ps->firstWhere('key','persentase_emas')->value ?? 85 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block mr-1"></span>Elektronik (%)</label>
                            <input type="number" name="persentase_elektronik" value="{{ $ps->firstWhere('key','persentase_elektronik')->value ?? 70 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1"><span class="w-2 h-2 rounded-full bg-green-400 inline-block mr-1"></span>Kendaraan (%)</label>
                            <input type="number" name="persentase_kendaraan" value="{{ $ps->firstWhere('key','persentase_kendaraan')->value ?? 75 }}" min="1" max="100" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mb-8">
            <button type="submit" class="btn-gradient px-8 py-3 rounded-xl text-sm font-semibold">Simpan Pengaturan</button>
        </div>
    </form>

    {{-- Tarif Ujrah Ranges --}}
    <div class="glass-card p-6 mb-6">
        <h3 class="text-base font-semibold text-amber-400 mb-5">Tarif Biaya Penitipan per Range</h3>
        <p class="text-xs text-slate-500 mb-4">Override biaya penitipan berdasarkan range nilai taksiran. Jika tidak masuk range, menggunakan persentase Ijarah di atas.</p>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5 border-b border-white/10">
                        <th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Min. Taksiran</th><th class="px-4 py-3">Max. Taksiran</th><th class="px-4 py-3">Tarif (Rp)</th><th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($tarifUjrahs as $tarif)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3 text-white capitalize"><span class="w-2 h-2 rounded-full mr-2 inline-block {{ $tarif->kategori_barang == 'emas' ? 'bg-yellow-400' : ($tarif->kategori_barang == 'elektronik' ? 'bg-blue-400' : 'bg-green-400') }}"></span>{{ $tarif->kategori_barang }}</td>
                        <td class="px-4 py-3 font-mono text-sky-400">Rp {{ number_format($tarif->min_taksiran, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-mono text-sky-400">Rp {{ number_format($tarif->max_taksiran, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-mono text-emerald-400 font-medium">Rp {{ number_format($tarif->tarif, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('tarif-ujrah.destroy', $tarif) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:text-rose-300"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada pengaturan range tarif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white/5 rounded-xl p-5 border border-white/10">
            <h4 class="text-xs font-semibold text-white mb-3">Tambah Range Baru</h4>
            <form action="{{ route('tarif-ujrah.store') }}" method="POST">@csrf
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Kategori</label>
                        <select name="kategori_barang" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm" required>
                            <option value="emas" class="bg-slate-800">Emas</option>
                            <option value="elektronik" class="bg-slate-800">Elektronik</option>
                            <option value="kendaraan" class="bg-slate-800">Kendaraan</option>
                        </select>
                    </div>
                    <div><label class="block text-xs text-slate-400 mb-1">Min. Taksiran</label><input type="text" name="min_taksiran" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm" required></div>
                    <div><label class="block text-xs text-slate-400 mb-1">Max. Taksiran</label><input type="text" name="max_taksiran" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm" required></div>
                    <div><label class="block text-xs text-slate-400 mb-1">Tarif Ujrah</label><input type="text" name="tarif" class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm" required></div>
                    <div><button type="submit" class="w-full btn-gradient px-4 py-2 rounded-lg text-sm">Tambah</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
</x-app-layout>
