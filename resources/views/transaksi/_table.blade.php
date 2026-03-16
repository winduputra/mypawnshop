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
            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                @php
                    $waMessage = "Yth. Bpk/Ibu *" . $trx->nasabah->nama . "*,\n" .
                               "Kami informasikan bahwa kontrak gadai dengan nomor *" . $trx->no_transaksi . "* akan jatuh tempo pada tanggal *" . $trx->tanggal_jatuh_tempo . "*.\n\n" .
                               "Mohon melakukan pembayaran pokok pinjaman atau perpanjangan tenor melalui kasir sebelum tanggal tersebut.\n\n" .
                               "Apabila tidak ada konfirmasi hingga tanggal *" . $trx->tanggal_jatuh_tempo . "*, barang jaminan *" . $trx->detailTransaksi->first()->barang->nama_barang . "* akan diproses sesuai ketentuan yang berlaku.";
                    $waUrl = "https://wa.me/" . $trx->nasabah->whatsapp_number . "?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="text-emerald-500 hover:text-emerald-400 p-1" title="Kirim WA Reminder">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <a href="{{ route('transaksi.show', $trx) }}" class="text-sky-400 hover:text-sky-300 p-1" title="Lihat Detail">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
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
<div class="p-6 border-t border-white/5">
    {{ $transactions->links() }}
</div>
@endif
