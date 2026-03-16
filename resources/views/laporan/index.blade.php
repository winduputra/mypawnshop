<x-app-layout>
    @section('header_title', 'Laporan')

    @section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Period Tabs -->
        <div class="mb-8 flex space-x-3">
            <a href="{{ route('laporan.index', ['period' => 'harian']) }}" 
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'harian' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Harian
            </a>
            <a href="{{ route('laporan.index', ['period' => 'mingguan']) }}" 
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'mingguan' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Mingguan
            </a>
            <a href="{{ route('laporan.index', ['period' => 'bulanan']) }}" 
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all {{ $period === 'bulanan' ? 'btn-gradient' : 'glass text-slate-400 hover:text-white hover:bg-white/10' }}">
                Bulanan
            </a>
        </div>

        <p class="text-slate-400 text-sm mb-6">Periode: <span class="text-white font-semibold">{{ $periodLabel }}</span></p>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-lg font-semibold text-white">Transaksi Periode Ini</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $transaksiPeriod->count() }} transaksi</p>
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
            </div>
        </div>

        <!-- Laporan Jatuh Tempo - with search/filter -->
        <div class="glass-card overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Laporan Jatuh Tempo</h3>
                    <p class="text-xs text-slate-500 mt-1">Semua transaksi aktif diurutkan berdasarkan jatuh tempo terdekat</p>
                </div>
                <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-3">
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="text" name="search_jt" value="{{ $search ?? '' }}" placeholder="Cari nama / no. kontrak..." 
                           class="glass bg-white/5 border-white/10 rounded-xl px-4 py-2 text-white text-sm focus:border-sky-500 focus:ring-sky-500 w-64">
                    <button type="submit" class="glass px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10">Cari</button>
                    @if($search)
                    <a href="{{ route('laporan.index', ['period' => $period]) }}" class="text-xs text-slate-500 hover:text-white">Reset</a>
                    @endif
                </form>
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
        </div>
    </div>
    @endsection
</x-app-layout>
