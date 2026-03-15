<x-app-layout>
    @section('header_title', 'Transaksi Rahn')

    @section('content')
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-white">Daftar Gadai Aktif</h3>
        <a href="{{ route('transaksi.create') }}" class="btn-gradient">
            Transaksi Baru
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Pinjaman</th>
                        <th class="px-6 py-4">Jatuh Tempo</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-sky-400 font-bold">{{ $trx->no_transaksi }}</td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $trx->nasabah->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $trx->nasabah->nik }}</div>
                        </td>
                        <td class="px-6 py-4 text-white">
                            <span class="block">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-500">Ujrah: {{ number_format($trx->ujrah_per_30hari, 0, ',', '.') }}/30hr</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="block text-white">{{ $trx->tanggal_jatuh_tempo }}</span>
                            <span class="text-xs text-rose-400">Lelang: {{ $trx->tanggal_batas_lelang }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                @if($trx->status == 'aktif') bg-sky-500/10 text-sky-400 
                                @elseif($trx->status == 'lunas') bg-emerald-500/10 text-emerald-400 
                                @elseif($trx->status == 'lelang') bg-rose-500/10 text-rose-400
                                @else bg-indigo-500/10 text-indigo-400 @endif">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('transaksi.show', $trx) }}" class="text-sky-400 hover:text-sky-300 font-medium">Buka</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada transaksi rahn</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
    @endsection
</x-app-layout>
