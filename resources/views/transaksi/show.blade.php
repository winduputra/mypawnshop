<x-app-layout>
@section('header_title', 'Detail Akad Pinjaman')
@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))<div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-sm">{{ session('error') }}</div>@endif

    <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
        <a href="{{ route('transaksi.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Kembali
        </a>
        <div class="flex flex-wrap gap-2">
            {{-- Kasir: Kirim ke Admin --}}
            @if(in_array($transaksi->status_approval, ['draft','pending']) && auth()->user()->role === 'kasir')
            <a href="{{ route('transaksi.edit', $transaksi) }}" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10">Edit Akad</a>
            <form action="{{ route('transaksi.kirim', $transaksi) }}" method="POST" onsubmit="return confirm('Kirim akad ke admin untuk verifikasi?');">@csrf
                <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-5 py-2 rounded-xl text-sm font-semibold">Kirim ke Admin</button>
            </form>
            @endif
            @if($transaksi->status_approval === 'menunggu_persetujuan_nasabah' && auth()->user()->role === 'kasir')
            <form action="{{ route('transaksi.nasabah-setuju', $transaksi) }}" method="POST" onsubmit="return confirm('Nasabah setuju dengan nilai taksiran final? ID akad akan diterbitkan.');">@csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Nasabah Setuju</button>
            </form>
            <form action="{{ route('transaksi.nasabah-tidak-setuju', $transaksi) }}" method="POST" onsubmit="return confirm('Nasabah tidak setuju? Transaksi akan ditutup tanpa ID akad.');">@csrf
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Nasabah Tidak Setuju</button>
            </form>
            @endif
            {{-- Admin: Review --}}
            @if($transaksi->status_approval === 'dikirim' && in_array(auth()->user()->role, ['admin','owner']))
            <a href="{{ route('transaksi.review', $transaksi) }}" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-5 py-2 rounded-xl text-sm font-semibold">Review Akad</a>
            @endif
            {{-- PDF only if approved --}}
            @if($transaksi->status_approval === 'disetujui')
            <a href="{{ route('transaksi.kontrak-pdf', $transaksi) }}" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-emerald-400 hover:bg-white/10" target="_blank">Cetak Kontrak</a>
            @if($transaksi->status == 'lunas')
            <a href="{{ route('transaksi.nota-lunas', $transaksi) }}" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10" target="_blank">Cetak Nota Lunas</a>
            @endif
            @if(in_array($transaksi->status, ['aktif','diperpanjang']))
            <button onclick="toggleModal('modalAngsuran')" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-amber-400 hover:bg-white/10">Bayar Angsuran</button>
            <button onclick="toggleModal('modalPerpanjang')" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-indigo-400 hover:bg-white/10">Perpanjang</button>
            <button onclick="toggleModal('modalPelunasan')" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-5 py-2 rounded-xl text-sm">Lunasi</button>
            @endif
            @endif
        </div>
    </div>

    {{-- Catatan Admin --}}
    @if($transaksi->catatan_admin)
    <div class="mb-6 p-4 rounded-xl border {{ in_array($transaksi->status_approval, ['pending','menunggu_persetujuan_nasabah']) ? 'bg-amber-500/10 border-amber-500/20' : ($transaksi->status_approval === 'ditolak' ? 'bg-rose-500/10 border-rose-500/20' : 'bg-emerald-500/10 border-emerald-500/20') }}">
        <p class="text-xs font-semibold uppercase {{ in_array($transaksi->status_approval, ['pending','menunggu_persetujuan_nasabah']) ? 'text-amber-400' : ($transaksi->status_approval === 'ditolak' ? 'text-rose-400' : 'text-emerald-400') }} mb-1">Catatan Admin</p>
        <p class="text-sm text-slate-800">{{ $transaksi->catatan_admin }}</p>
    </div>
    @endif

    @if($transaksi->status_approval === 'menunggu_persetujuan_nasabah')
    <div class="mb-6 p-4 rounded-xl border bg-amber-500/10 border-amber-500/20">
        <p class="text-xs font-semibold uppercase text-amber-400 mb-1">Menunggu Persetujuan Nasabah</p>
        <p class="text-sm text-slate-800">Admin mengubah nilai taksiran final menjadi <strong>Rp {{ number_format($transaksi->taksiran_final ?? $transaksi->total_taksiran, 0, ',', '.') }}</strong>. Kasir perlu konfirmasi ke nasabah sebelum ID akad diterbitkan.</p>
    </div>
    @endif

    {{-- Modals --}}
    @if($transaksi->status_approval === 'disetujui' && in_array($transaksi->status, ['aktif','diperpanjang']))
    <div id="modalAngsuran" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalAngsuran')" class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-xl">&times;</button>
            <h3 class="text-xl font-bold text-slate-800 mb-6">Bayar Angsuran</h3>
            <form action="{{ route('transaksi.angsuran', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-sky-500/10 rounded-xl border border-sky-500/20">
                    <p class="text-xs text-sky-400 uppercase font-semibold">Sisa Pinjaman</p>
                    <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                </div>
                <div><label class="block text-sm text-slate-500 mb-2">Jumlah Bayar (Rp)</label><input type="text" name="jumlah_bayar" class="currency-input w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-xl px-4 py-3 text-slate-800" required></div>
                <div><label class="block text-sm text-slate-500 mb-2">Catatan</label><input type="text" name="catatan" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-xl px-4 py-3 text-slate-800 text-sm"></div>
                <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all w-full py-4 rounded-xl">Bayar Angsuran</button>
            </form>
        </div>
    </div>
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalPerpanjang')" class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-xl">&times;</button>
            <h3 class="text-xl font-bold text-slate-800 mb-6">Perpanjang Tenor</h3>
            @php
                $extendCount = $transaksi->perpanjangan->count();
                $jatuhTempoDate = \Carbon\Carbon::parse($transaksi->tanggal_jatuh_tempo);
                $todayDate = \Carbon\Carbon::today();
                $selisihHari = $jatuhTempoDate->diffInDays($todayDate, false);
                $isOverdue = $selisihHari > 0 && $selisihHari <= 7;
                $isBlocked = $selisihHari > 7;
                $biayaPerpanjangan = $transaksi->biaya_penitipan;
            @endphp

            {{-- Extension history count (no limit) --}}
            <div class="mb-4 p-3 bg-sky-500/10 border border-sky-500/20 rounded-xl">
                <p class="text-xs text-sky-400">Perpanjangan sebelumnya: <strong>{{ $extendCount }}x</strong> (tidak ada batasan)</p>
            </div>

            @if($isBlocked)
            {{-- Overdue > 7 hari: BLOCKED --}}
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                <p class="text-sm text-rose-500 font-semibold mb-1">⚠ Perpanjangan Tidak Dapat Dilakukan</p>
                <p class="text-xs text-rose-400">Sudah melewati {{ $selisihHari }} hari dari jatuh tempo (maks 7 hari). Barang masuk periode lelang.</p>
            </div>
            @elseif($isOverdue)
            {{-- Overdue 1-7 hari: Bayar 1x penitipan, dapat +20 hari --}}
            <div class="mb-4 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <p class="text-sm text-amber-600 font-semibold mb-1">⚠ Jatuh Tempo Telah Lewat {{ $selisihHari }} Hari</p>
                <p class="text-xs text-amber-500">Bayar <strong>1x biaya penitipan</strong> tanpa biaya admin. Tenor bertambah <strong>20 hari</strong> dari tanggal jatuh tempo lama.</p>
            </div>
            <form action="{{ route('transaksi.perpanjang', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-rose-500/10 rounded-xl border border-rose-500/20 text-center">
                    <p class="text-xs text-rose-400 uppercase font-semibold mb-1">Biaya Perpanjangan (1x Penitipan)</p>
                    <p class="text-3xl font-bold text-rose-600 font-mono">Rp {{ number_format($biayaPerpanjangan, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Rp {{ number_format($transaksi->biaya_penitipan, 0, ',', '.') }} · Tenor +20 hari</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 space-y-1">
                    <div class="flex justify-between"><span>Jatuh Tempo Lama</span><span class="font-semibold text-slate-800">{{ $jatuhTempoDate->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span>Jatuh Tempo Baru</span><span class="font-semibold text-emerald-600">{{ $jatuhTempoDate->copy()->addDays(20)->format('d/m/Y') }}</span></div>
                </div>
                <label class="flex items-start cursor-pointer"><input type="checkbox" required class="w-5 h-5 border-slate-300 rounded bg-white text-amber-500 mr-3 mt-0.5"><span class="text-sm text-slate-600">Nasabah setuju membayar 1x biaya penitipan tanpa biaya admin serta memperpanjang tenor 20 hari.</span></label>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 px-4 rounded-xl shadow-sm transition-all w-full">Bayar Penitipan & Perpanjang 20 Hari</button>
            </form>
            @else
            {{-- Normal: belum jatuh tempo --}}
            <form action="{{ route('transaksi.perpanjang', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-indigo-500/10 rounded-xl border border-indigo-500/20 text-center">
                    <p class="text-xs text-indigo-400 uppercase font-semibold mb-1">Biaya Perpanjangan (1x Penitipan)</p>
                    <p class="text-3xl font-bold text-slate-800 font-mono">Rp {{ number_format($biayaPerpanjangan, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Rp {{ number_format($transaksi->biaya_penitipan, 0, ',', '.') }} · Tenor +30 hari dari jatuh tempo</p>
                    <p class="text-[10px] text-slate-500 mt-1">Biaya admin tidak dikenakan lagi karena hanya dibayar 1x selama transaksi Rahn.</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 space-y-1">
                    <div class="flex justify-between"><span>Jatuh Tempo Lama</span><span class="font-semibold text-slate-800">{{ $jatuhTempoDate->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span>Jatuh Tempo Baru</span><span class="font-semibold text-emerald-600">{{ $jatuhTempoDate->copy()->addDays(30)->format('d/m/Y') }}</span></div>
                </div>
                <label class="flex items-start cursor-pointer"><input type="checkbox" required class="w-5 h-5 border-slate-300 rounded bg-white text-indigo-500 mr-3 mt-0.5"><span class="text-sm text-slate-600">Nasabah setuju memperpanjang tenor.</span></label>
                <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-4 px-4 rounded-xl shadow-sm transition-all w-full">Bayar & Perpanjang 30 Hari</button>
            </form>
            @endif
        </div>
    </div>
    <div id="modalPelunasan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalPelunasan')" class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-xl">&times;</button>
            <h3 class="text-xl font-bold text-slate-800 mb-6">Pelunasan</h3>
            <form action="{{ route('transaksi.pelunasan', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20"><p class="text-xs text-emerald-400 uppercase font-semibold">Sisa Pinjaman</p><p class="text-2xl font-bold text-slate-800">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p></div>
                <div><label class="block text-sm text-slate-500 mb-2">Jumlah Bayar</label><input type="text" value="{{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}" class="w-full bg-slate-100 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 cursor-not-allowed" readonly><p class="text-xs text-slate-500 mt-1">Nominal pelunasan otomatis sesuai sisa pokok pinjaman.</p></div>
                <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all w-full py-4 rounded-xl">Proses Pelunasan</button>
            </form>
        </div>
    </div>
    @endif
    <script>function toggleModal(id){document.getElementById(id).classList.toggle('hidden');}</script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-wrap items-center justify-between gap-4 border-l-4 
                @if($transaksi->status_approval === 'disetujui' && $transaksi->status === 'aktif') border-sky-500 
                @elseif($transaksi->status === 'lunas') border-emerald-500 
                @elseif(in_array($transaksi->status_approval, ['pending','menunggu_persetujuan_nasabah'])) border-amber-500 
                @elseif($transaksi->status_approval === 'ditolak') border-rose-500 
                @else border-slate-500 @endif">
                <div>
                    <p class="text-xs text-slate-500 uppercase font-semibold">Status Approval</p>
                    @php
                        $apColors=['draft'=>'text-slate-500','dikirim'=>'text-blue-400','pending'=>'text-amber-400','menunggu_persetujuan_nasabah'=>'text-amber-400','disetujui'=>'text-emerald-400','ditolak'=>'text-rose-400'];
                        $apLabels=['menunggu_persetujuan_nasabah'=>'MENUNGGU PERSETUJUAN NASABAH'];
                    @endphp
                    <h3 class="text-2xl font-bold {{ $apColors[$transaksi->status_approval] ?? 'text-slate-800' }}">{{ $apLabels[$transaksi->status_approval] ?? strtoupper($transaksi->status_approval) }}</h3>
                    @if($transaksi->status !== 'draft' && $transaksi->status !== 'ditolak')
                    <p class="text-xs text-slate-500 mt-1">Status Transaksi: <span class="font-semibold text-slate-800">{{ strtoupper($transaksi->status) }}</span></p>
                    @endif
                </div>
                <div class="text-right">
                    @if($transaksi->no_register_akad)
                    <p class="text-xs text-slate-500 uppercase font-semibold">No. Register Akad</p>
                    <p class="text-lg font-mono text-emerald-400 font-bold">{{ $transaksi->no_register_akad }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">Ref: {{ $transaksi->no_transaksi }}</p>
                </div>
            </div>

            {{-- Barang Jaminan --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Barang Jaminan (Marhun)</h3>
                @foreach($transaksi->detailTransaksi as $detail)
                <div class="flex items-center p-4 bg-white rounded-xl border border-slate-200">
                    <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-800 font-medium">{{ $detail->barang->nama_barang }}</p>
                        <p class="text-xs text-slate-500 uppercase">{{ $detail->barang->kategori }} · Taksiran: Rp {{ number_format($detail->taksiran_item, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right"><p class="text-sm text-slate-800 font-bold">Rp {{ number_format($detail->pinjaman_item, 0, ',', '.') }}</p><p class="text-[10px] text-slate-500">Pinjaman</p></div>
                </div>
                @endforeach
            </div>

            {{-- Angsuran History --}}
            @if($transaksi->angsuran->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Riwayat Angsuran</h3>
                <table class="w-full text-left text-sm">
                    <thead><tr class="text-xs text-slate-500 uppercase bg-white"><th class="px-3 py-2">Ke-</th><th class="px-3 py-2">Tanggal</th><th class="px-3 py-2 text-right">Dibayar</th><th class="px-3 py-2 text-right">Sisa</th><th class="px-3 py-2">Kasir</th><th class="px-3 py-2 text-right">Nota</th></tr></thead>
                    <tbody class="divide-y divide-slate-200">
                    @foreach($transaksi->angsuran as $idx => $ans)
                    <tr class="hover:bg-slate-50"><td class="px-3 py-2 text-slate-800 font-semibold">{{ $idx+1 }}</td><td class="px-3 py-2 text-slate-800">{{ $ans->tanggal_bayar }}</td><td class="px-3 py-2 text-emerald-400 font-mono text-right">Rp {{ number_format($ans->jumlah_bayar,0,',','.') }}</td><td class="px-3 py-2 text-slate-800 font-mono text-right">Rp {{ number_format($ans->sisa_pinjaman,0,',','.') }}</td><td class="px-3 py-2 text-slate-500">{{ $ans->user->name }}</td><td class="px-3 py-2 text-right"><a href="{{ route('transaksi.angsuran.cetak', [$transaksi, $ans]) }}" class="text-[10px] bg-amber-50 text-amber-500 px-2 py-1 rounded-full hover:bg-amber-100" target="_blank">📄 Download</a></td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Riwayat</h3>
                <div class="relative pl-8 border-l border-slate-300 space-y-6">
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-slate-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">Draft Dibuat</p><p class="text-xs text-slate-500">{{ $transaksi->tanggal_transaksi }} · {{ $transaksi->user->name }}</p></div>
                    @php
                        $historyLabels = [
                            'sent' => ['title' => 'Dikirim ke Admin', 'color' => 'bg-blue-500'],
                            'resubmitted' => ['title' => 'Diajukan Ulang ke Admin', 'color' => 'bg-blue-500'],
                            'updated' => ['title' => 'Akad Diperbaiki Kasir', 'color' => 'bg-amber-500'],
                            'pending' => ['title' => 'Pending oleh Admin', 'color' => 'bg-amber-500'],
                            'customer_confirmation_requested' => ['title' => 'Menunggu Persetujuan Nasabah', 'color' => 'bg-amber-500'],
                            'customer_approved' => ['title' => 'Disetujui Nasabah', 'color' => 'bg-emerald-500'],
                            'customer_rejected' => ['title' => 'Ditolak Nasabah', 'color' => 'bg-rose-500'],
                            'approved' => ['title' => 'Disetujui Admin', 'color' => 'bg-emerald-500'],
                            'rejected' => ['title' => 'Ditolak Admin', 'color' => 'bg-rose-500'],
                        ];
                    @endphp
                    @foreach($transaksi->histories->sortBy('created_at') as $history)
                        @php $meta = $historyLabels[$history->action] ?? ['title' => ucfirst($history->action), 'color' => 'bg-slate-500']; @endphp
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full {{ $meta['color'] }} border-4 border-slate-900"></span>
                            <p class="text-sm font-bold text-slate-800">{{ $meta['title'] }} oleh {{ $history->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $history->created_at->format('Y-m-d H:i') }} · Status: {{ ucfirst($history->status_approval ?? '-') }}</p>
                            @if($history->note)
                            <p class="text-xs text-slate-600 mt-1 bg-slate-50 border border-slate-200 rounded-lg p-2">{{ $history->note }}</p>
                            @endif
                        </div>
                    @endforeach
                    @if($transaksi->histories->isEmpty() && $transaksi->status_approval === 'pending' && $transaksi->catatan_admin)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-amber-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">Pending oleh Admin</p><p class="text-xs text-slate-500">{{ $transaksi->updated_at->format('Y-m-d H:i') }} · Alasan: {{ $transaksi->catatan_admin }}</p></div>
                    @endif
                    @if($transaksi->histories->where('action', 'approved')->isEmpty() && $transaksi->approved_at)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">Disetujui oleh {{ $transaksi->approvedByUser->name ?? '-' }}</p><p class="text-xs text-slate-500">{{ $transaksi->approved_at->format('Y-m-d H:i') }} · {{ $transaksi->no_register_akad }}</p></div>
                    @endif
                    @foreach($transaksi->perpanjangan as $ext)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full {{ $ext->is_overdue_extension ? 'bg-amber-500' : 'bg-indigo-500' }} border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">Perpanjangan (+{{ $ext->tambahan_tenor_hari }} hari) @if($ext->is_overdue_extension)<span class="text-[10px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full ml-1">OVERDUE</span>@endif <a href="{{ route('transaksi.perpanjangan.cetak', [$transaksi, $ext]) }}" target="_blank" class="ml-2 text-[10px] bg-indigo-50 text-indigo-500 px-2 py-0.5 rounded-full hover:bg-indigo-100">📄 Download Nota</a></p><p class="text-xs text-slate-500">{{ $ext->tanggal_perpanjangan }} · Nota: {{ $ext->no_nota ?? '-' }} · JT Baru: {{ $ext->tanggal_jatuh_tempo_baru }} · Rp {{ number_format($ext->ujrah_dibayar, 0, ',', '.') }}</p></div>
                    @endforeach
                    @foreach($transaksi->angsuran as $ans)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-amber-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">Angsuran Rp {{ number_format($ans->jumlah_bayar,0,',','.') }}</p><p class="text-xs text-slate-500">{{ $ans->tanggal_bayar }} · Sisa: Rp {{ number_format($ans->sisa_pinjaman,0,',','.') }}</p></div>
                    @endforeach
                    @if($transaksi->pelunasan)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-slate-800">LUNAS</p><p class="text-xs text-slate-500">{{ $transaksi->pelunasan->tanggal_pelunasan }}</p></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">
            @if($transaksi->status_approval === 'disetujui')
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 {{ $transaksi->status == 'lunas' ? 'border-emerald-500' : 'border-amber-500' }}">
                <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Sisa Pinjaman</p>
                <p class="text-3xl font-bold {{ $transaksi->sisa_pinjaman <= 0 ? 'text-emerald-400' : 'text-slate-800' }}">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                @if($transaksi->sisa_pinjaman > 0 && $transaksi->total_pinjaman > 0)
                <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-sky-500 to-emerald-500 rounded-full" style="width:{{ 100-(($transaksi->sisa_pinjaman/$transaksi->total_pinjaman)*100) }}%"></div></div>
                <p class="text-[10px] text-slate-500 mt-1">{{ number_format(100-(($transaksi->sisa_pinjaman/$transaksi->total_pinjaman)*100),0) }}% terbayar</p>
                @endif
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-800 mb-4">Rincian Keuangan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Total Taksiran</span><span class="text-slate-800 font-mono">Rp {{ number_format($transaksi->total_taksiran,0,',','.') }}</span></div>
                    @if($transaksi->taksiran_final)<div class="flex justify-between"><span class="text-slate-500">Taksiran Final</span><span class="text-emerald-400 font-mono font-bold">Rp {{ number_format($transaksi->taksiran_final,0,',','.') }}</span></div>@endif
                    <div class="flex justify-between"><span class="text-slate-500">Pinjaman (QARD)</span><span class="text-sky-400 font-mono font-bold">Rp {{ number_format($transaksi->total_pinjaman,0,',','.') }}</span></div>
                    <div class="flex justify-between border-t border-slate-200 pt-3"><span class="text-slate-500">Biaya Admin</span><span class="text-slate-800 font-mono">Rp {{ number_format($transaksi->biaya_admin,0,',','.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ijarah/30hr</span><span class="text-indigo-400 font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari,0,',','.') }}</span></div>
                    <div class="flex justify-between border-t border-slate-200 pt-3"><span class="text-slate-500">Metode Biaya</span><span class="text-xs px-2 py-1 rounded-full {{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">{{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'Bayar di Awal' : 'Potong Pinjaman' }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-800 mb-4">Info Tenor</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Tenor</span><span class="text-slate-800">{{ $transaksi->tenor_hari }} Hari</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Jatuh Tempo</span><span class="text-slate-800 font-semibold">{{ $transaksi->tanggal_jatuh_tempo }}</span></div>
                    <div class="flex justify-between"><span class="text-rose-400">Batas Lelang</span><span class="text-rose-400 font-semibold">{{ $transaksi->tanggal_batas_lelang }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-white mr-3 border border-slate-300 flex items-center justify-center text-slate-500 font-bold text-sm">{{ substr($transaksi->nasabah->nama,0,1) }}</div>
                <div><p class="text-sm font-bold text-slate-800">{{ $transaksi->nasabah->nama }}</p><p class="text-xs text-sky-400 font-mono">{{ $transaksi->nasabah->telepon }}</p></div>
            </div>

            {{-- WhatsApp Reminder --}}
            @if($transaksi->status_approval === 'disetujui' && in_array($transaksi->status, ['aktif','diperpanjang']))
            @php
                $jt = \Carbon\Carbon::parse($transaksi->tanggal_jatuh_tempo);
                $now = \Carbon\Carbon::today();
                $diffHari = (int) $now->diffInDays($jt, false); // positive = belum JT, negative = sudah lewat
                $hariLewat = $diffHari < 0 ? abs($diffHari) : 0;
                $hariBefore = $diffHari > 0 ? $diffHari : 0;

                // Get barang jaminan name
                $detailFirst = $transaksi->detailTransaksi->first();
                $namaBarang = $detailFirst ? $detailFirst->barang->nama_barang : 'barang jaminan';
                $namaCustomer = $transaksi->nasabah->nama;
                $sisaUtang = 'Rp' . number_format($transaksi->sisa_pinjaman, 0, ',', '.');
                $ujrah = 'Rp' . number_format($transaksi->ujrah_per_30hari, 0, ',', '.');
                $tglJT = $jt->format('d/m/Y');
                $noAkad = $transaksi->no_register_akad ?? $transaksi->no_transaksi;
                $csPhone = $noTeleponCs ?? '6281234567890';

                // Format phone for wa.me (strip leading 0, ensure 62 prefix)
                $custPhone = preg_replace('/[^0-9]/', '', $transaksi->nasabah->telepon);
                if (str_starts_with($custPhone, '0')) {
                    $custPhone = '62' . substr($custPhone, 1);
                } elseif (!str_starts_with($custPhone, '62')) {
                    $custPhone = '62' . $custPhone;
                }

                // Format CS phone for display
                $csPhoneDisplay = $csPhone;

                // Determine message template based on due date proximity
                if ($hariLewat >= 8) {
                    // H+8: Final - Siap Lelang
                    $waLabel = '⚠ Kirim Peringatan Lelang (H+' . $hariLewat . ')';
                    $waColor = 'bg-red-600 hover:bg-red-700';
                    $waBadge = 'SIAP LELANG';
                    $waBadgeColor = 'bg-red-100 text-red-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, sesuai akad rahn no {$noAkad}, karena belum ada pelunasan hingga H+8, barang jaminan {$namaBarang} resmi kami lelang. Sisa dana (jika ada) setelah lelang dan potong hutang akan dikembalikan ke rekening Bapak/Ibu. Terima kasih. HARMANS GADAI SYARIAH";
                } elseif ($hariLewat >= 7) {
                    // H+7: Peringatan Lelang
                    $waLabel = '⚠ Kirim Peringatan Lelang (H+7)';
                    $waColor = 'bg-red-500 hover:bg-red-600';
                    $waBadge = 'PERINGATAN LELANG';
                    $waBadgeColor = 'bg-red-100 text-red-600';
                    $biayaOverdue = 'Rp' . number_format($transaksi->ujrah_per_30hari * 2, 0, ',', '.');
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, perhatian! Jatuh tempo gadai Anda telah lewat 7 hari. Dalam 1x24 jam, barang jaminan {$namaBarang} akan dilelang untuk menutup hutang pokok {$sisaUtang} + biaya penitipan overdue {$biayaOverdue}. Hubungi kami segera di {$csPhoneDisplay} untuk menghindari lelang. HARMANS GADAI SYARIAH";
                } elseif ($hariLewat > 0) {
                    // H+1 to H+6: Sudah lewat tapi belum H+7
                    $waLabel = '⚠ Kirim Peringatan (Lewat ' . $hariLewat . ' Hari)';
                    $waColor = 'bg-orange-500 hover:bg-orange-600';
                    $waBadge = 'SUDAH LEWAT JT';
                    $waBadgeColor = 'bg-orange-100 text-orange-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, barang jaminan {$namaBarang} Bapak/Ibu sudah melewati jatuh tempo {$hariLewat} hari pada tgl {$tglJT}. Sisa utang pokok {$sisaUtang}. Segera lunasi atau perpanjang sebelum masuk periode lelang (H+8). Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                } elseif ($diffHari == 0) {
                    // H-0 / H-1 (hari ini jatuh tempo)
                    $waLabel = '🔴 Kirim Peringatan Penting (HARI INI JT)';
                    $waColor = 'bg-rose-500 hover:bg-rose-600';
                    $waBadge = 'HARI INI JATUH TEMPO';
                    $waBadgeColor = 'bg-rose-100 text-rose-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, HARI INI adalah batas akhir jatuh tempo gadai Bapak/Ibu. Jika belum melunasi pokok pinjaman {$sisaUtang} atau perpanjang biaya penitipan {$ujrah}, barang jaminan {$namaBarang} otomatis masuk tahap pelelangan tanpa pemberitahuan lebih lanjut. Segera lunasi. Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                } elseif ($hariBefore == 1) {
                    // H-1
                    $waLabel = '🔴 Kirim Peringatan Penting (H-1)';
                    $waColor = 'bg-rose-500 hover:bg-rose-600';
                    $waBadge = 'BESOK JATUH TEMPO';
                    $waBadgeColor = 'bg-rose-100 text-rose-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, BESOK adalah batas akhir jatuh tempo gadai Bapak/Ibu pada tgl {$tglJT}. Jika belum melunasi pokok pinjaman {$sisaUtang} atau perpanjang biaya penitipan {$ujrah}, barang jaminan {$namaBarang} otomatis masuk tahap pelelangan tanpa pemberitahuan lebih lanjut. Segera lunasi. Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                } elseif ($hariBefore <= 3) {
                    // H-3
                    $waLabel = '🟠 Kirim Peringatan H-' . $hariBefore;
                    $waColor = 'bg-amber-500 hover:bg-amber-600';
                    $waBadge = 'H-' . $hariBefore;
                    $waBadgeColor = 'bg-amber-100 text-amber-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, barang jaminan {$namaBarang} Bapak/Ibu akan jatuh tempo dalam {$hariBefore} hari lagi pada tgl {$tglJT}. Sisa utang pokok {$sisaUtang}. Segera lunasi atau perpanjang sebelum jatuh tempo. Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                } elseif ($hariBefore <= 7) {
                    // H-7
                    $waLabel = '🟡 Kirim Peringatan Dini (H-' . $hariBefore . ')';
                    $waColor = 'bg-yellow-500 hover:bg-yellow-600';
                    $waBadge = 'H-' . $hariBefore;
                    $waBadgeColor = 'bg-yellow-100 text-yellow-700';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, barang jaminan {$namaBarang} Bapak/Ibu akan jatuh tempo dalam {$hariBefore} hari lagi pada tgl {$tglJT}. Sisa utang pokok {$sisaUtang}. Segera lunasi atau perpanjang sebelum jatuh tempo. Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                } else {
                    // Masih jauh (>7 hari)
                    $waLabel = '💬 Kirim Pengingat (H-' . $hariBefore . ')';
                    $waColor = 'bg-emerald-500 hover:bg-emerald-600';
                    $waBadge = 'H-' . $hariBefore;
                    $waBadgeColor = 'bg-emerald-100 text-emerald-600';
                    $waMessage = "Assalamu'alaikum Bpk/Ibu {$namaCustomer}, ini adalah pengingat bahwa barang jaminan {$namaBarang} Bapak/Ibu memiliki jatuh tempo pada tgl {$tglJT}. Sisa utang pokok {$sisaUtang}. Info hub CS HARMANS GADAI SYARIAH: {$csPhoneDisplay}.";
                }

                $waUrl = 'https://wa.me/' . $custPhone . '?text=' . urlencode($waMessage);
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-slate-800">Ingatkan via WA</h3>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $waBadgeColor }}">{{ $waBadge }}</span>
                </div>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100" style="white-space:pre-line;">{{ $waMessage }}</p>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                   class="{{ $waColor }} text-white font-semibold py-3 px-4 rounded-xl shadow-sm transition-all w-full block text-center text-sm">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    {{ $waLabel }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
</x-app-layout>
