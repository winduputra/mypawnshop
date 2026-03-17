<x-app-layout>
    @section('header_title', 'Hasil Eksekusi Lelang')

    @section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Success Banner --}}
        <div class="rounded-2xl p-6 flex items-center space-x-4"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(5,150,105,0.1) 100%); border: 1px solid rgba(16,185,129,0.3);">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-emerald-400">Eksekusi Lelang Berhasil!</h2>
                <p class="text-sm text-emerald-300/70 mt-0.5">
                    Barang jaminan <strong>{{ $lelang->transaksiRahn->no_transaksi }}</strong> telah berhasil dilelang pada
                    {{ \Carbon\Carbon::parse($lelang->tanggal_lelang)->translatedFormat('d F Y') }}.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Ringkasan Nasabah & Barang --}}
            <div class="glass-card p-6 space-y-5">
                <h3 class="text-base font-semibold text-white border-b border-white/10 pb-3">Informasi Pinjaman</h3>

                <div>
                    <p class="text-xs text-slate-500 mb-1">Nasabah (Rahin)</p>
                    <p class="text-white font-semibold text-sm">{{ $lelang->transaksiRahn->nasabah->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $lelang->transaksiRahn->nasabah->telepon }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-2">Barang Jaminan (Marhun)</p>
                    <ul class="space-y-1">
                        @foreach($lelang->transaksiRahn->detailTransaksi as $dt)
                        <li class="flex justify-between text-sm">
                            <span class="text-slate-300">{{ $dt->barang->nama_barang }}</span>
                            <span class="text-sky-400 font-mono">Rp {{ number_format($dt->taksiran_item, 0, ',', '.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="pt-3 border-t border-white/10 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Pinjaman Awal</span>
                        <span class="text-white font-mono">Rp {{ number_format($lelang->transaksiRahn->total_pinjaman, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. Transaksi</span>
                        <span class="text-sky-400 font-mono font-bold">{{ $lelang->transaksiRahn->no_transaksi }}</span>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Lelang --}}
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-white/10 pb-3">Detail Hasil Lelang</h3>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Pembeli</span>
                        <span class="text-white font-medium">{{ $lelang->pembeli }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal Lelang</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($lelang->tanggal_lelang)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Harga Terjual</span>
                        <span class="text-white font-mono font-semibold">Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Biaya Lelang</span>
                        <span class="text-white font-mono">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-white/10 space-y-3">
                    @if($lelang->sisa_untuk_nasabah > 0)
                    <div class="flex justify-between items-center rounded-xl px-4 py-3"
                         style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                        <div>
                            <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide">Dana Turun ke Nasabah</p>
                            <p class="text-xs text-emerald-300/60 mt-0.5">Hak nasabah atas kelebihan lelang</p>
                        </div>
                        <span class="text-emerald-400 font-bold text-lg font-mono">
                            Rp {{ number_format($lelang->sisa_untuk_nasabah, 0, ',', '.') }}
                        </span>
                    </div>
                    @elseif($lelang->kerugian > 0)
                    <div class="flex justify-between items-center rounded-xl px-4 py-3"
                         style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                        <div>
                            <p class="text-xs text-rose-400 font-semibold uppercase tracking-wide">Kerugian / Kekurangan</p>
                            <p class="text-xs text-rose-300/60 mt-0.5">Sisa kewajiban nasabah</p>
                        </div>
                        <span class="text-rose-400 font-bold text-lg font-mono">
                            Rp {{ number_format($lelang->kerugian, 0, ',', '.') }}
                        </span>
                    </div>
                    @else
                    <div class="flex justify-between items-center rounded-xl px-4 py-3"
                         style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2);">
                        <p class="text-indigo-400 font-semibold text-sm">Impas — Tidak ada kelebihan atau kekurangan</p>
                    </div>
                    @endif

                    <div class="flex justify-between text-xs text-slate-500 pt-1">
                        <span>Dieksekusi oleh: {{ $lelang->user->name ?? '-' }}</span>
                        <span>{{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('lelang.cetak-pdf', $lelang) }}"
               class="flex-1 btn-gradient py-4 rounded-xl text-base text-center flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Berita Acara Lelang (PDF)</span>
            </a>
            <a href="{{ route('lelang.index') }}"
               class="flex-1 py-4 rounded-xl text-base text-center text-slate-400 hover:text-white border border-white/10 hover:border-white/20 transition-colors flex items-center justify-center">
                Kembali ke Daftar Lelang
            </a>
        </div>

    </div>
    @endsection
</x-app-layout>
