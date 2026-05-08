<x-app-layout>
    @section('header_title', 'Detail Lelang')

    @section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('lelang.index') }}" class="text-[#084C35] hover:text-[#084C35]/70 text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Lelang
        </a>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>
        @endif

        @php
            // Determine if we're viewing an existing lelang or creating new
            $isExisting = isset($lelang);
            $trx = $isExisting ? $lelang->transaksiRahn : $transaksi;
            $user = auth()->user();
        @endphp

        {{-- Catatan Owner (jika dibatalkan) --}}
        @if($isExisting && $lelang->status_lelang === 'dibatalkan' && $lelang->catatan_owner)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start space-x-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div>
                <p class="text-sm font-semibold text-red-700">Catatan dari Owner:</p>
                <p class="text-sm text-red-600">{{ $lelang->catatan_owner }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Info Pinjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#084C35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Informasi Pinjaman
                </h3>
                <div class="space-y-4">
                    @if($isExisting)
                    <div>
                        <p class="text-xs text-slate-500 mb-1">ID Lelang</p>
                        <p class="text-[#084C35] font-mono font-bold">{{ $lelang->no_lelang }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Nasabah</p>
                        <p class="text-slate-800 font-medium">{{ $trx->nasabah->nama }}</p>
                        <p class="text-xs text-slate-400">{{ $trx->nasabah->telepon }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Barang Jaminan</p>
                        <ul class="list-disc list-inside text-sm text-slate-600">
                            @foreach($trx->detailTransaksi as $dt)
                            <li>{{ $dt->barang->nama_barang }} (Rp {{ number_format($dt->taksiran_item, 0, ',', '.') }})</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-slate-200 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-500">Pokok Pinjaman</span>
                            <span class="text-slate-800 font-mono font-semibold">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-500">Sisa Pokok Pinjaman</span>
                            <span class="text-rose-600 font-mono font-bold">Rp {{ number_format($trx->sisa_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-500">Ijarah (Biaya Penitipan)</span>
                            <span class="text-slate-700 font-mono">Rp {{ number_format($trx->biaya_penitipan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form / Review Panel --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                @if($isExisting && $lelang->status_lelang === 'pending' && in_array($user->role, ['owner','superadmin']))
                {{-- Owner Review Mode --}}
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Review Lelang</h3>
                <div class="space-y-3 text-sm mb-6">
                    <div class="flex justify-between"><span class="text-slate-500">Harga Jual Lelang</span><span class="text-slate-800 font-mono font-bold">Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Biaya Admin Lelang</span><span class="text-slate-800 font-mono">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ijarah</span><span class="text-slate-700 font-mono">Rp {{ number_format($lelang->ijarah, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between pt-3 border-t border-slate-200"><span class="text-sm font-medium text-emerald-600">Est. Sisa Dana Kembali</span><span class="text-emerald-600 font-mono font-bold">Rp {{ number_format($lelang->sisa_dana_kembali, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-sm text-slate-500">Diajukan oleh</span><span class="text-slate-700">{{ $lelang->user->name ?? '-' }}</span></div>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('lelang.approve', $lelang->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Setujui lelang ini?')">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition-all">Approve</button>
                    </form>
                    <button onclick="document.getElementById('reject-inline').classList.toggle('hidden')" class="flex-1 border border-red-300 text-red-600 font-semibold py-3 rounded-xl hover:bg-red-50 transition-all">Tolak / Revisi</button>
                </div>
                <div id="reject-inline" class="hidden mt-4">
                    <form action="{{ route('lelang.reject', $lelang->id) }}" method="POST">
                        @csrf
                        <textarea name="catatan_owner" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800 mb-3 focus:ring-2 focus:ring-red-300 focus:outline-none" placeholder="Catatan revisi..."></textarea>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition-all">Kirim Tolakan</button>
                    </form>
                </div>

                @elseif($isExisting && $lelang->status_lelang === 'dibatalkan' && in_array($user->role, ['admin','kasir','superadmin']))
                {{-- Admin Revisi Mode --}}
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Revisi Harga Lelang</h3>
                <form action="{{ route('lelang.update', $lelang->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="sisa_pinjaman" value="{{ $trx->sisa_pinjaman }}">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Harga Jual Lelang (Rp)</label>
                        <input type="text" name="harga_lelang" id="harga_lelang" value="{{ number_format($lelang->harga_lelang, 0, '', '') }}" required oninput="calculateLelang()"
                            class="currency-input w-full border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 text-lg font-mono focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Biaya Admin Lelang (Rp)</label>
                        <input type="text" name="biaya_lelang" id="biaya_lelang" value="{{ number_format($lelang->biaya_lelang, 0, '', '') }}" required oninput="calculateLelang()"
                            class="currency-input w-full border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 font-mono focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-emerald-700">Sisa Dana Kembali</span>
                            <span id="label_kembali" class="text-emerald-700 font-mono font-bold text-lg">Rp 0</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-3 rounded-xl transition-all">
                        Kirim Revisi ke Owner
                    </button>
                </form>

                @elseif(!$isExisting)
                {{-- New Lelang Form --}}
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Input Data Lelang</h3>
                <form action="{{ route('lelang.store') }}" method="POST" class="space-y-5" onsubmit="return confirm('Kirim data lelang ke Owner untuk approval?')">
                    @csrf
                    <input type="hidden" name="transaksi_rahn_id" value="{{ $trx->id }}">
                    <input type="hidden" id="sisa_pinjaman" value="{{ $trx->sisa_pinjaman }}">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Harga Jual Lelang (Rp)</label>
                        <input type="text" name="harga_lelang" id="harga_lelang" required placeholder="0" oninput="calculateLelang()"
                            class="currency-input w-full border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 text-lg font-mono focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Biaya Admin Lelang (Rp)</label>
                        <input type="text" name="biaya_lelang" id="biaya_lelang" required value="0" oninput="calculateLelang()"
                            class="currency-input w-full border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 font-mono focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-emerald-700">Est. Sisa Dana Kembali</span>
                            <span id="label_kembali" class="text-emerald-700 font-mono font-bold text-lg">Rp 0</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#084C35] hover:bg-[#063d2a] text-[#D6A639] font-semibold py-3 rounded-xl transition-all text-base">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim ke Owner</span>
                        </span>
                    </button>
                </form>

                @elseif($isExisting && in_array($lelang->status_lelang, ['aktif','pending','terjual']))
                {{-- Read-only view --}}
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Detail Lelang</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Status</span>
                        @php $sc = ['pending'=>'bg-amber-100 text-amber-700','aktif'=>'bg-blue-100 text-blue-700','terjual'=>'bg-emerald-100 text-emerald-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc[$lelang->status_lelang] ?? '' }}">{{ ucfirst($lelang->status_lelang) }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Harga Jual Lelang</span><span class="text-slate-800 font-mono font-bold">Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Biaya Admin Lelang</span><span class="text-slate-800 font-mono">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ijarah</span><span class="text-slate-700 font-mono">Rp {{ number_format($lelang->ijarah, 0, ',', '.') }}</span></div>
                    @if($lelang->approved_at)
                    <div class="flex justify-between"><span class="text-slate-500">Disetujui oleh</span><span class="text-slate-700">{{ $lelang->approvedByUser->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Tanggal Approve</span><span class="text-slate-700">{{ $lelang->approved_at->format('d M Y H:i') }}</span></div>
                    @endif
                    @if($lelang->status_lelang === 'terjual')
                    <div class="flex justify-between pt-3 border-t border-slate-200"><span class="text-emerald-600 font-medium">Sisa Dana Kembali</span><span class="text-emerald-600 font-mono font-bold">Rp {{ number_format($lelang->sisa_dana_kembali, 0, ',', '.') }}</span></div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function parseRupiah(val) { return parseInt((val||'').toString().replace(/[^0-9]/g, '')) || 0; }
        function formatRupiah(angka) {
            var s = angka.toString().replace(/[^,\d]/g, ''), sp = s.split(','), sisa = sp[0].length % 3, r = sp[0].substr(0, sisa), rb = sp[0].substr(sisa).match(/\d{3}/gi);
            if(rb){r += (sisa?'.':'') + rb.join('.');}
            return 'Rp ' + r;
        }
        function calculateLelang() {
            var p = parseFloat(document.getElementById('sisa_pinjaman')?.value) || 0;
            var h = parseRupiah(document.getElementById('harga_lelang')?.value);
            var b = parseRupiah(document.getElementById('biaya_lelang')?.value);
            var sisa = Math.max(0, h - (p + b));
            var el = document.getElementById('label_kembali');
            if(el) el.innerText = formatRupiah(sisa);
        }
    </script>
    @endpush
    @endsection
</x-app-layout>
