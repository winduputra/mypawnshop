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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Biaya Admin per Kategori
                </h3>
                <p class="text-xs text-slate-500 mb-4">Biaya administrasi flat berdasarkan jenis barang jaminan.</p>
                <div class="space-y-4">
                    @php $ba = $settings['biaya_admin'] ?? collect(); @endphp
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block mr-1"></span>Elektronik (Rp)</label>
                        <input type="text" name="biaya_admin_elektronik" value="{{ $ba->firstWhere('key','biaya_admin_elektronik')->value ?? 35000 }}" class="currency-input w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block mr-1"></span>Emas (Rp)</label>
                        <input type="text" name="biaya_admin_emas" value="{{ $ba->firstWhere('key','biaya_admin_emas')->value ?? 25000 }}" class="currency-input w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-green-400 inline-block mr-1"></span>Kendaraan (Rp)</label>
                        <input type="text" name="biaya_admin_kendaraan" value="{{ $ba->firstWhere('key','biaya_admin_kendaraan')->value ?? 50000 }}" class="currency-input w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                    </div>
                </div>
            </div>

            {{-- Ijarah + Persentase Plafon --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Biaya Ijarah (Penitipan)
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Persentase dari nilai taksiran, ditagihkan setiap tenor 30 hari.</p>
                    @php $ij = $settings['ijarah'] ?? collect(); @endphp
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Persentase Ijarah (%)</label>
                        <input type="number" step="0.1" name="ijarah_persen" value="{{ $ij->firstWhere('key','ijarah_persen')->value ?? 2 }}" min="0" max="100" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                        <p class="text-[10px] text-slate-500 mt-1">Contoh: 2% dari taksiran 10.000.000 = Rp 200.000 /30 hari</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        Plafon Pinjaman (QARD)
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Persentase maksimal pinjaman dari nilai taksiran per kategori.</p>
                    @php $ps = $settings['persentase'] ?? collect(); @endphp
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block mr-1"></span>Emas (%)</label>
                            <input type="number" name="persentase_emas" value="{{ $ps->firstWhere('key','persentase_emas')->value ?? 85 }}" min="1" max="100" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block mr-1"></span>Elektronik (%)</label>
                            <input type="number" name="persentase_elektronik" value="{{ $ps->firstWhere('key','persentase_elektronik')->value ?? 70 }}" min="1" max="100" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1"><span class="w-2 h-2 rounded-full bg-green-400 inline-block mr-1"></span>Kendaraan (%)</label>
                            <input type="number" name="persentase_kendaraan" value="{{ $ps->firstWhere('key','persentase_kendaraan')->value ?? 75 }}" min="1" max="100" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- No Telepon CS --}}
        <div class="mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    No. Telepon CS (WhatsApp)
                </h3>
                <p class="text-xs text-slate-500 mb-4">Nomor telepon yang muncul di pesan pengingat WhatsApp ke nasabah. Gunakan format internasional (contoh: 6281234567890).</p>
                @php $umum = $settings['umum'] ?? collect(); @endphp
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">No. Telepon CS (format 62xxx)</label>
                    <input type="text" name="no_telepon_cs" value="{{ $umum->firstWhere('key','no_telepon_cs')->value ?? '6281234567890' }}" class="w-full bg-white border border-slate-200 border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required placeholder="6281234567890">
                </div>
            </div>
        </div>

        <div class="flex justify-end mb-8">
            <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-8 py-3 rounded-xl text-sm font-semibold">Simpan Pengaturan</button>
        </div>
    </form>

</div>
@endsection
</x-app-layout>
