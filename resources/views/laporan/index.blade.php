<x-app-layout>
    @section('header_title', 'Laporan')

    @section('content')
    <div class="max-w-7xl mx-auto">

        {{-- Cabang Filter (admin only) --}}
        @if(auth()->user()->role === 'admin')
        <form method="GET" action="{{ route('laporan.index') }}" class="mb-6 flex flex-wrap items-end gap-4">
            <input type="hidden" name="period" value="{{ $period }}">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-slate-500 mb-1 uppercase tracking-widest">Filter Cabang</label>
                <select name="cabang_id" onchange="this.form.submit()"
                    class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}" {{ $filterCabangId == $c->id ? 'selected' : '' }}>
                            {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
        @endif

        @if($selectedCabang)
        <div class="mb-4 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold text-sky-300 bg-sky-500/10 border border-sky-500/20">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Menampilkan data Cabang: <strong class="ml-1">{{ $selectedCabang->nama_cabang }}</strong>
        </div>
        @endif

        <!-- Period Tabs -->
        <div class="mb-8 flex space-x-3">
            <a href="{{ route('laporan.index', ['period' => 'harian', 'cabang_id' => $filterCabangId]) }}"
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'harian' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Harian
            </a>
            <a href="{{ route('laporan.index', ['period' => 'mingguan', 'cabang_id' => $filterCabangId]) }}"
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'mingguan' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Mingguan
            </a>
            <a href="{{ route('laporan.index', ['period' => 'bulanan', 'cabang_id' => $filterCabangId]) }}"
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'bulanan' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Bulanan
            </a>
        </div>

        <p class="text-slate-400 text-sm mb-6">Periode: <span class="text-white font-semibold">{{ $periodLabel }}</span></p>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-fuchsia-500/10 rounded-xl">
                        <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-500 uppercase">Pend. Angsuran</span>
                </div>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($laporanAngsuran->total_angsuran ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $laporanAngsuran->jumlah ?? 0 }} pembayaran</p>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-sky-500/10 rounded-xl">
                        <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-500 uppercase">Biaya Admin</span>
                </div>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($laporanAdmin->total_admin ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $laporanAdmin->jumlah ?? 0 }} transaksi</p>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-500/10 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-500 uppercase">Ujrah/Penitipan</span>
                </div>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($laporanUjrah->total_ujrah ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $laporanUjrah->jumlah ?? 0 }} perpanjangan</p>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-500/10 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-500 uppercase">Pinjaman</span>
                </div>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($laporanPinjaman->total_pinjaman ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $laporanPinjaman->jumlah ?? 0 }} transaksi baru</p>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-rose-500/10 rounded-xl">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-500 uppercase">Jatuh Tempo</span>
                </div>
                <h3 class="text-2xl font-bold text-white">{{ $laporanJatuhTempo->count() }}</h3>
                <p class="text-xs text-slate-400 mt-1">Transaksi belum lunas</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Laporan Barang Jaminan - ENHANCED -->
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-lg font-semibold text-white">Barang Jaminan Aktif</h3>
                    <p class="text-xs text-slate-500 mt-1">Semua barang yang sedang digadaikan (status aktif/diperpanjang)</p>
                </div>
                <div class="p-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="text-center p-3 bg-white/5 rounded-xl">
                            <p class="text-2xl font-bold text-white">{{ $totalBarangAktif }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">Total Item</p>
                        </div>
                        <div class="text-center p-3 bg-white/5 rounded-xl">
                            <p class="text-sm font-bold text-sky-400 font-mono">Rp {{ number_format($totalNilaiTaksiran, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">Nilai Taksiran</p>
                        </div>
                        <div class="text-center p-3 bg-white/5 rounded-xl">
                            <p class="text-sm font-bold text-emerald-400 font-mono">Rp {{ number_format($totalNilaiPinjaman, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">Nilai Pinjaman</p>
                        </div>
                    </div>

                    @forelse($laporanBarang as $item)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-3 {{ $item->kategori == 'emas' ? 'bg-yellow-400' : ($item->kategori == 'elektronik' ? 'bg-blue-400' : 'bg-green-400') }}"></span>
                            <div>
                                <span class="text-sm text-white font-medium capitalize">{{ $item->kategori }}</span>
                                <p class="text-[10px] text-slate-500">Pinjaman: Rp {{ number_format($item->total_pinjaman, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-white font-mono font-bold">{{ $item->jumlah }} item</p>
                            <p class="text-xs text-slate-500">Taksiran: Rp {{ number_format($item->total_taksiran, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500 text-center py-4">Tidak ada barang jaminan aktif</p>
                    @endforelse
                </div>
            </div>

            <!-- Transaksi Period -->
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Transaksi Periode Ini</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $transaksiPeriod->total() }} transaksi</p>
                    </div>
                    <form method="GET" action="{{ route('laporan.index') }}" id="form-per-page-trx">
                        @foreach(request()->except('per_page', 'page_trx') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="per_page" onchange="document.getElementById('form-per-page-trx').submit()"
                            class="bg-slate-800 border border-white/10 rounded-lg px-2 py-1 text-white text-xs">
                            @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} baris</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                                <th class="px-6 py-3">No. Transaksi</th>
                                <th class="px-6 py-3">Nasabah</th>
                                <th class="px-6 py-3 text-right">Pinjaman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            @forelse($transaksiPeriod as $trx)
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-3 font-mono text-sky-400 text-xs">{{ $trx->no_transaksi }}</td>
                                <td class="px-6 py-3 text-white">{{ $trx->nasabah->nama }}</td>
                                <td class="px-6 py-3 text-white text-right font-mono">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-slate-500">Tidak ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transaksiPeriod->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $transaksiPeriod->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Laporan Jatuh Tempo - with search/filter -->
        <div class="glass-card overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Laporan Jatuh Tempo</h3>
                    <p class="text-xs text-slate-500 mt-1">Semua transaksi aktif diurutkan berdasarkan jatuh tempo terdekat</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="cabang_id" value="{{ $filterCabangId }}">
                        <input type="text" name="search_jt" value="{{ $search ?? '' }}" placeholder="Cari nama / no. kontrak..." 
                               class="glass bg-white/5 border-white/10 rounded-xl px-4 py-2 text-white text-sm focus:border-sky-500 focus:ring-sky-500 w-52">
                        <button type="submit" class="glass px-3 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10">Cari</button>
                        @if($search)
                        <a href="{{ route('laporan.index', ['period' => $period, 'cabang_id' => $filterCabangId]) }}" class="text-xs text-slate-500 hover:text-white">Reset</a>
                        @endif
                    </form>
                    <form method="GET" action="{{ route('laporan.index') }}" id="form-per-page-jt">
                        @foreach(request()->except('per_page', 'page_jt') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="per_page" onchange="document.getElementById('form-per-page-jt').submit()"
                            class="bg-slate-800 border border-white/10 rounded-lg px-2 py-1 text-white text-xs">
                            @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} baris</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                            <th class="px-6 py-4">No. Transaksi</th>
                            <th class="px-6 py-4">Nasabah</th>
                            <th class="px-6 py-4">Pinjaman</th>
                            <th class="px-6 py-4">Sisa</th>
                            <th class="px-6 py-4">Jatuh Tempo</th>
                            <th class="px-6 py-4">Status Hari</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        @forelse($laporanJatuhTempo as $trx)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-sky-400 text-xs">{{ $trx->no_transaksi }}</td>
                            <td class="px-6 py-4 text-white font-medium">{{ $trx->nasabah->nama }}</td>
                            <td class="px-6 py-4 text-white font-mono">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-amber-400 font-mono">Rp {{ number_format($trx->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-white">{{ $trx->tanggal_jatuh_tempo }}</td>
                            <td class="px-6 py-4">
                                @if($trx->sisa_hari > 0)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400">
                                        Lewat {{ $trx->sisa_hari }} hari
                                    </span>
                                @elseif($trx->sisa_hari >= -7)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400">
                                        {{ abs($trx->sisa_hari) }} hari lagi
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400">
                                        {{ abs($trx->sisa_hari) }} hari lagi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if($trx->status == 'aktif') bg-sky-500/10 text-sky-400
                                    @else bg-indigo-500/10 text-indigo-400 @endif">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                @if($search)
                                    Tidak ditemukan transaksi dengan kata kunci "{{ $search }}"
                                @else
                                    Tidak ada transaksi aktif
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laporanJatuhTempo->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $laporanJatuhTempo->links() }}
            </div>
            @endif
        </div>
    </div>
    @endsection
</x-app-layout>
