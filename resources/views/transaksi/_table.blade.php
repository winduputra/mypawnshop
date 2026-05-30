<table class="w-full text-left">
    <thead>
        <tr class="text-xs font-semibold text-slate-500 uppercase bg-white">
            <th class="px-6 py-4">No. Transaksi</th>
            <th class="px-6 py-4">Nasabah</th>
            <th class="px-6 py-4">Cabang</th>
            <th class="px-6 py-4">Pinjaman</th>
            <th class="px-6 py-4">Jatuh Tempo</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-200 text-sm">
        @forelse($transactions as $trx)
        <tr class="hover:bg-white transition-colors">
            <td class="px-6 py-4 font-mono text-sky-400 font-bold">{{ $trx->no_transaksi }}</td>
            <td class="px-6 py-4">
                <div class="text-slate-800 font-medium">{{ $trx->nasabah->nama }}</div>
                <div class="text-xs text-slate-500">{{ $trx->nasabah->nik }}</div>
            </td>
            <td class="px-6 py-4 text-xs text-slate-600">{{ $trx->nasabah->cabang->nama_cabang ?? $trx->nasabah->cabang->nama ?? '-' }}</td>
            <td class="px-6 py-4 text-slate-800">
                <span class="block">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-500">Ujrah: {{ number_format($trx->ujrah_per_30hari, 0, ',', '.') }}/30hr</span>
            </td>
            <td class="px-6 py-4">
                <span class="block text-slate-800">{{ $trx->tanggal_jatuh_tempo }}</span>
                <span class="text-xs text-rose-400">Lelang: {{ $trx->tanggal_batas_lelang }}</span>
            </td>
            <td class="px-6 py-4">
                @php
                    $apColors=['draft'=>'bg-slate-500/10 text-slate-500','dikirim'=>'bg-blue-500/10 text-blue-400','pending'=>'bg-amber-500/10 text-amber-400','menunggu_persetujuan_nasabah'=>'bg-amber-500/10 text-amber-400','disetujui'=>'bg-emerald-500/10 text-emerald-400','ditolak'=>'bg-rose-500/10 text-rose-400'];
                    $apLabels=['menunggu_persetujuan_nasabah'=>'Menunggu Nasabah'];
                @endphp
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $apColors[$trx->status_approval] ?? 'bg-slate-500/10 text-slate-500' }}">{{ $apLabels[$trx->status_approval] ?? ucfirst($trx->status_approval) }}</span>
                @if($trx->status_approval === 'disetujui')
                <span class="px-2 py-1 rounded-full text-xs font-medium ml-1
                    @if($trx->status == 'aktif') bg-sky-500/10 text-sky-400 
                    @elseif($trx->status == 'lunas') bg-emerald-500/10 text-emerald-400 
                    @elseif($trx->status == 'lelang_terjual') bg-emerald-500/10 text-emerald-400
                    @elseif(in_array($trx->status, ['lelang','lelang_pending','lelang_aktif'])) bg-rose-500/10 text-rose-400
                    @else bg-indigo-500/10 text-indigo-400 @endif">{{ ['lelang_pending'=>'Proses Lelang','lelang_aktif'=>'Aktif Lelang','lelang_terjual'=>'Terjual Lelang'][$trx->status] ?? ucfirst($trx->status) }}</span>
                @endif
            </td>
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                @if(in_array($trx->status_approval, ['draft','pending']) && auth()->user()->role === 'kasir')
                <a href="{{ route('transaksi.edit', $trx) }}" class="text-amber-400 hover:text-amber-300 p-1" title="Edit Akad">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                @endif
                <a href="{{ route('transaksi.show', $trx) }}" class="text-sky-400 hover:text-sky-300 p-1" title="Lihat Detail">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
                @if(auth()->user()->role === 'superadmin')
                <form action="{{ route('transaksi.dummy-destroy', $trx) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dummy transaksi ini? Transaksi, detail, angsuran, perpanjangan, pelunasan, lelang, histori, dan baris laporan terkait akan dihapus.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-400 p-1 transition" title="Hapus Dummy Transaksi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.008v.008H12V16.5zm8.25 2.25L13.5 4.5a1.5 1.5 0 00-3 0L3.75 18.75A1.5 1.5 0 005.25 21h13.5a1.5 1.5 0 001.5-2.25z" /></svg>
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada transaksi rahn</td>
        </tr>
        @endforelse
    </tbody>
</table>
@if($transactions->hasPages())
<div class="p-6 border-t border-slate-200">
    {{ $transactions->links() }}
</div>
@endif
