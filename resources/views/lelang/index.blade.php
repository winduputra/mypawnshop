<x-app-layout>
    @section('header_title', 'Manajemen Lelang')

    @section('content')
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-white">Barang Siap Lelang</h3>
        <p class="text-sm text-slate-500">Daftar marhun yang telah melewati batas tenggang {{ config('app.grace_period', 7) }} hari.</p>
    </div>

    <div class="glass-card overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Pokok Hutang</th>
                        <th class="px-6 py-4">Tgl Batas</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-sky-400 font-bold uppercase">{{ $trx->no_transaksi }}</td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $trx->nasabah->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $trx->nasabah->telepon }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-slate-300">
                                @foreach($trx->detailTransaksi as $dt)
                                    <li>{{ $dt->barang->nama_barang }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-white font-semibold">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-rose-400 font-medium">{{ $trx->tanggal_batas_lelang }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('lelang.show', $trx) }}" class="btn-gradient px-4 py-2 rounded-lg text-xs">Proses Lelang</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-slate-500">Tidak ada barang yang perlu dilelang saat ini.</p>
                            </div>
                        </td>
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
