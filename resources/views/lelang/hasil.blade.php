<x-app-layout>
    @section('header_title', 'Nota Lelang')

    @section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Success Banner --}}
        <div class="rounded-2xl p-6 flex items-center space-x-4 bg-emerald-50 border border-emerald-200">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-emerald-700">Lelang Terjual!</h2>
                <p class="text-sm text-emerald-600 mt-0.5">
                    Barang jaminan <strong>{{ $lelang->transaksiRahn->no_transaksi }}</strong> telah terjual melalui lelang.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Info Pinjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
                <h3 class="text-base font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Pinjaman</h3>
                <div>
                    <p class="text-xs text-slate-500 mb-1">ID Lelang</p>
                    <p class="text-[#084C35] font-mono font-bold text-lg">{{ $lelang->no_lelang }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Nasabah (Rahin)</p>
                    <p class="text-slate-800 font-semibold text-sm">{{ $lelang->transaksiRahn->nasabah->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $lelang->transaksiRahn->nasabah->telepon }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Cabang Asal Barang</p>
                    <p class="text-slate-800 font-semibold text-sm">{{ $lelang->transaksiRahn->nasabah->cabang->nama_cabang ?? $lelang->transaksiRahn->nasabah->cabang->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-2">Barang Jaminan (Marhun)</p>
                    <ul class="space-y-1">
                        @foreach($lelang->transaksiRahn->detailTransaksi as $dt)
                        <li class="flex justify-between text-sm">
                            <span class="text-slate-600">{{ $dt->barang->nama_barang }}</span>
                            <span class="text-sky-600 font-mono">Rp {{ number_format($dt->taksiran_item, 0, ',', '.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Transaksi</span>
                        <span class="text-sky-600 font-mono font-bold">{{ $lelang->transaksiRahn->no_transaksi }}</span>
                    </div>
                </div>
            </div>

            {{-- Detail Nota Lelang --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <h3 class="text-base font-semibold text-slate-800 border-b border-slate-200 pb-3">Nota Lelang</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">ID Lelang</span>
                        <span class="text-[#084C35] font-mono font-bold">{{ $lelang->no_lelang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Terjual</span>
                        <span class="text-slate-800">{{ $lelang->tanggal_terjual ? \Carbon\Carbon::parse($lelang->tanggal_terjual)->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Harga Jual Lelang</span>
                        <span class="text-slate-800 font-mono font-semibold">Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Biaya Admin Lelang</span>
                        <span class="text-slate-800 font-mono">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</span>
                    </div>
                    @if($lelang->pembeli)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pembeli</span>
                        <span class="text-slate-800">{{ $lelang->pembeli }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alamat Pembeli</span>
                        <span class="text-slate-800 text-right max-w-[55%]">{{ $lelang->alamat_pembeli ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Telpon</span>
                        <span class="text-slate-800">{{ $lelang->telepon_pembeli ?? '-' }}</span>
                    </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-200 space-y-3">
                    @if($lelang->sisa_dana_kembali > 0 || $lelang->sisa_untuk_nasabah > 0)
                    <div class="flex justify-between items-center rounded-xl px-4 py-3 bg-emerald-50 border border-emerald-200">
                        <div>
                            <p class="text-xs text-emerald-700 font-semibold uppercase tracking-wide">Sisa Dana Kembali</p>
                            <p class="text-xs text-emerald-500 mt-0.5">Hak nasabah atas kelebihan lelang</p>
                        </div>
                        <span class="text-emerald-700 font-bold text-lg font-mono">
                            Rp {{ number_format(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah), 0, ',', '.') }}
                        </span>
                    </div>
                    @elseif($lelang->kerugian > 0)
                    <div class="flex justify-between items-center rounded-xl px-4 py-3 bg-red-50 border border-red-200">
                        <div>
                            <p class="text-xs text-red-700 font-semibold uppercase tracking-wide">Kerugian / Kekurangan</p>
                            <p class="text-xs text-red-500 mt-0.5">Sisa kewajiban nasabah</p>
                        </div>
                        <span class="text-red-700 font-bold text-lg font-mono">
                            Rp {{ number_format($lelang->kerugian, 0, ',', '.') }}
                        </span>
                    </div>
                    @else
                    <div class="flex justify-between items-center rounded-xl px-4 py-3 bg-blue-50 border border-blue-200">
                        <p class="text-blue-700 font-semibold text-sm">Impas — Tidak ada kelebihan atau kekurangan</p>
                    </div>
                    @endif

                    <div class="flex justify-between text-xs text-slate-400 pt-1">
                        <span>Diproses oleh: {{ $lelang->user->name ?? '-' }}</span>
                        <span>Disetujui oleh: {{ $lelang->approvedByUser->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('lelang.cetak-pdf', $lelang) }}"
               class="flex-1 bg-[#084C35] hover:bg-[#063d2a] text-[#D6A639] font-semibold py-4 rounded-xl text-base text-center flex items-center justify-center space-x-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Nota Lelang (PDF)</span>
            </a>
            <a href="{{ route('lelang.index') }}"
               class="flex-1 py-4 rounded-xl text-base text-center text-slate-500 hover:text-slate-800 border border-slate-300 hover:border-slate-400 transition-colors flex items-center justify-center">
                Kembali ke Daftar Lelang
            </a>
        </div>
    </div>
    @endsection
</x-app-layout>
