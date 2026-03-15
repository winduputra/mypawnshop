<x-app-layout>
    @section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stat Card 1 -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-sky-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-slate-500 uppercase">Nasabah</span>
            </div>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_nasabah'] }}</h3>
            <p class="text-xs text-slate-400 mt-1">Sobat Gadai Terdaftar</p>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-slate-500 uppercase">Gadai Aktif</span>
            </div>
            <h3 class="text-2xl font-bold text-white">{{ $stats['active_rahn'] }}</h3>
            <p class="text-xs text-slate-400 mt-1">Transaksi Berjalan</p>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-slate-500 uppercase">Total Pinjaman</span>
            </div>
            <h3 class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_pinjaman'], 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Marhun Bih Beredar</p>
        </div>

        <!-- Stat Card 4 -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-rose-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <span class="text-xs font-medium text-slate-500 uppercase">Barang Lelang</span>
            </div>
            <h3 class="text-2xl font-bold text-white">{{ $stats['siap_lelang'] }}</h3>
            <p class="text-xs text-slate-400 mt-1">Jatuh Tempo & Grace Period</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 glass-card overflow-hidden">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="text-sm text-sky-400 hover:text-sky-300">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                            <th class="px-6 py-4">No. Transaksi</th>
                            <th class="px-6 py-4">Nasabah</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Pinjaman</th>
                            <th class="px-6 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        @forelse($recent_transactions as $trx)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-sky-400">{{ $trx->no_transaksi }}</td>
                            <td class="px-6 py-4 text-white font-medium">{{ $trx->nasabah->nama }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $trx->tanggal_transaksi }}</td>
                            <td class="px-6 py-4 text-white">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if($trx->status == 'aktif') bg-sky-500/10 text-sky-400 
                                    @elseif($trx->status == 'lunas') bg-emerald-500/10 text-emerald-400 
                                    @else bg-rose-500/10 text-rose-400 @endif">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Summary -->
        <div class="glass-card p-6 flex flex-col">
            <h3 class="text-lg font-semibold text-white mb-6">Ringkasan Hari Ini</h3>
            <div class="flex-1 space-y-6">
                <div class="p-4 bg-sky-500/5 rounded-2xl border border-sky-500/10">
                    <p class="text-xs text-slate-400 mb-1 uppercase tracking-wider">Pemasukan Ujrah/Pelunasan</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['pelunasan_hari_ini'], 0, ',', '.') }}</p>
                </div>
                
                <div class="flex flex-col gap-3">
                    <p class="text-sm font-medium text-slate-300">Quick Action</p>
                    <a href="{{ route('transaksi.create') }}" class="btn-gradient w-full flex items-center justify-center py-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buat Gadai Baru
                    </a>
                    <a href="{{ route('nasabah.create') }}" class="w-full inline-block glass py-3 rounded-xl text-slate-300 font-semibold hover:bg-white/10 transition-all text-center">
                        Register Nasabah
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
