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
            <form action="{{ route('transaksi.kirim', $transaksi) }}" method="POST" onsubmit="return confirm('Kirim akad ke admin untuk verifikasi?');">@csrf
                <button type="submit" class="btn-gradient px-5 py-2 rounded-xl text-sm font-semibold">Kirim ke Admin</button>
            </form>
            @endif
            {{-- Admin: Review --}}
            @if($transaksi->status_approval === 'dikirim' && in_array(auth()->user()->role, ['admin','owner']))
            <a href="{{ route('transaksi.review', $transaksi) }}" class="btn-gradient px-5 py-2 rounded-xl text-sm font-semibold">Review Akad</a>
            @endif
            {{-- PDF only if approved --}}
            @if($transaksi->status_approval === 'disetujui')
            <a href="{{ route('transaksi.kontrak-pdf', $transaksi) }}" class="glass px-4 py-2 rounded-xl text-sm text-emerald-400 hover:bg-white/10" target="_blank">Cetak Kontrak</a>
            @if($transaksi->status == 'lunas')
            <a href="{{ route('transaksi.nota-lunas', $transaksi) }}" class="glass px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10" target="_blank">Cetak Nota Lunas</a>
            @endif
            @if(in_array($transaksi->status, ['aktif','diperpanjang']))
            <button onclick="toggleModal('modalAngsuran')" class="glass px-4 py-2 rounded-xl text-sm text-amber-400 hover:bg-white/10">Bayar Angsuran</button>
            <button onclick="toggleModal('modalPerpanjang')" class="glass px-4 py-2 rounded-xl text-sm text-indigo-400 hover:bg-white/10">Perpanjang</button>
            <button onclick="toggleModal('modalPelunasan')" class="btn-gradient px-5 py-2 rounded-xl text-sm">Lunasi</button>
            @endif
            @endif
        </div>
    </div>

    {{-- Catatan Admin --}}
    @if($transaksi->catatan_admin)
    <div class="mb-6 p-4 rounded-xl border {{ $transaksi->status_approval === 'pending' ? 'bg-amber-500/10 border-amber-500/20' : ($transaksi->status_approval === 'ditolak' ? 'bg-rose-500/10 border-rose-500/20' : 'bg-emerald-500/10 border-emerald-500/20') }}">
        <p class="text-xs font-semibold uppercase {{ $transaksi->status_approval === 'pending' ? 'text-amber-400' : ($transaksi->status_approval === 'ditolak' ? 'text-rose-400' : 'text-emerald-400') }} mb-1">Catatan Admin</p>
        <p class="text-sm text-white">{{ $transaksi->catatan_admin }}</p>
    </div>
    @endif

    {{-- Modals --}}
    @if($transaksi->status_approval === 'disetujui' && in_array($transaksi->status, ['aktif','diperpanjang']))
    <div id="modalAngsuran" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="glass-card max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalAngsuran')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
            <h3 class="text-xl font-bold text-white mb-6">Bayar Angsuran</h3>
            <form action="{{ route('transaksi.angsuran', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-sky-500/10 rounded-xl border border-sky-500/20">
                    <p class="text-xs text-sky-400 uppercase font-semibold">Sisa Pinjaman</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">Jumlah Bayar (Rp)</label><input type="text" name="jumlah_bayar" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required></div>
                <div><label class="block text-sm text-slate-400 mb-2">Catatan</label><input type="text" name="catatan" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-sm"></div>
                <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Bayar Angsuran</button>
            </form>
        </div>
    </div>
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="glass-card max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalPerpanjang')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
            <h3 class="text-xl font-bold text-white mb-6">Perpanjang Tenor</h3>
            @php $detail=$transaksi->detailTransaksi->first();$kategoriStr=$detail?$detail->barang->kategori:'emas';$limits=['emas'=>11,'elektronik'=>2,'kendaraan'=>3];$maxLimit=$limits[$kategoriStr]??1;$extendCount=$transaksi->perpanjangan->count();$canExtend=$extendCount<$maxLimit; @endphp
            @if($canExtend)
            <div class="mb-4 p-3 bg-sky-500/10 border border-sky-500/20 rounded-xl"><p class="text-xs text-sky-400">Terpakai: <strong>{{ $extendCount }} / {{ $maxLimit }}x</strong></p></div>
            <form action="{{ route('transaksi.perpanjang', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-indigo-500/10 rounded-xl border border-indigo-500/20 text-center">
                    <p class="text-xs text-indigo-400 uppercase font-semibold mb-1">Biaya Perpanjangan</p>
                    <p class="text-3xl font-bold text-white font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }}</p>
                </div>
                <label class="flex items-start cursor-pointer"><input type="checkbox" required class="w-5 h-5 border-white/10 rounded bg-slate-800 text-indigo-500 mr-3 mt-0.5"><span class="text-sm text-slate-300">Nasabah setuju memperpanjang tenor.</span></label>
                <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Bayar & Perpanjang</button>
            </form>
            @else
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl"><p class="text-sm text-rose-400">Batas perpanjangan tercapai ({{ $maxLimit }}x).</p></div>
            @endif
        </div>
    </div>
    <div id="modalPelunasan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="glass-card max-w-md w-full p-8 relative">
            <button onclick="toggleModal('modalPelunasan')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
            <h3 class="text-xl font-bold text-white mb-6">Pelunasan</h3>
            <form action="{{ route('transaksi.pelunasan', $transaksi) }}" method="POST" class="space-y-6">@csrf
                <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20"><p class="text-xs text-emerald-400 uppercase font-semibold">Sisa Pinjaman</p><p class="text-2xl font-bold text-white">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p></div>
                <div><label class="block text-sm text-slate-400 mb-2">Jumlah Bayar</label><input type="text" name="total_bayar" value="{{ $transaksi->sisa_pinjaman }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required></div>
                <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Proses Pelunasan</button>
            </form>
        </div>
    </div>
    @endif
    <script>function toggleModal(id){document.getElementById(id).classList.toggle('hidden');}</script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Header --}}
            <div class="glass-card p-6 flex flex-wrap items-center justify-between gap-4 border-l-4 
                @if($transaksi->status_approval === 'disetujui' && $transaksi->status === 'aktif') border-sky-500 
                @elseif($transaksi->status === 'lunas') border-emerald-500 
                @elseif($transaksi->status_approval === 'pending') border-amber-500 
                @elseif($transaksi->status_approval === 'ditolak') border-rose-500 
                @else border-slate-500 @endif">
                <div>
                    <p class="text-xs text-slate-500 uppercase font-semibold">Status Approval</p>
                    @php $apColors=['draft'=>'text-slate-400','dikirim'=>'text-blue-400','pending'=>'text-amber-400','disetujui'=>'text-emerald-400','ditolak'=>'text-rose-400']; @endphp
                    <h3 class="text-2xl font-bold {{ $apColors[$transaksi->status_approval] ?? 'text-white' }}">{{ strtoupper($transaksi->status_approval) }}</h3>
                    @if($transaksi->status !== 'draft' && $transaksi->status !== 'ditolak')
                    <p class="text-xs text-slate-500 mt-1">Status Transaksi: <span class="font-semibold text-white">{{ strtoupper($transaksi->status) }}</span></p>
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
            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Barang Jaminan (Marhun)</h3>
                @foreach($transaksi->detailTransaksi as $detail)
                <div class="flex items-center p-4 bg-white/5 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white font-medium">{{ $detail->barang->nama_barang }}</p>
                        <p class="text-xs text-slate-500 uppercase">{{ $detail->barang->kategori }} · Taksiran: Rp {{ number_format($detail->taksiran_item, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right"><p class="text-sm text-white font-bold">Rp {{ number_format($detail->pinjaman_item, 0, ',', '.') }}</p><p class="text-[10px] text-slate-500">Pinjaman</p></div>
                </div>
                @endforeach
            </div>

            {{-- Angsuran History --}}
            @if($transaksi->angsuran->count() > 0)
            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Riwayat Angsuran</h3>
                <table class="w-full text-left text-sm">
                    <thead><tr class="text-xs text-slate-500 uppercase bg-white/5"><th class="px-3 py-2">#</th><th class="px-3 py-2">Tanggal</th><th class="px-3 py-2 text-right">Dibayar</th><th class="px-3 py-2 text-right">Sisa</th><th class="px-3 py-2">Kasir</th><th class="px-3 py-2 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-white/5">
                    @foreach($transaksi->angsuran as $idx => $ans)
                    <tr class="hover:bg-white/5"><td class="px-3 py-2 text-slate-400">{{ $idx+1 }}</td><td class="px-3 py-2 text-white">{{ $ans->tanggal_bayar }}</td><td class="px-3 py-2 text-emerald-400 font-mono text-right">Rp {{ number_format($ans->jumlah_bayar,0,',','.') }}</td><td class="px-3 py-2 text-white font-mono text-right">Rp {{ number_format($ans->sisa_pinjaman,0,',','.') }}</td><td class="px-3 py-2 text-slate-400">{{ $ans->user->name }}</td><td class="px-3 py-2 text-right"><a href="{{ route('transaksi.angsuran.cetak', [$transaksi, $ans]) }}" class="text-sky-400 text-xs" target="_blank">Cetak</a></td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Timeline --}}
            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Riwayat</h3>
                <div class="relative pl-8 border-l border-white/10 space-y-6">
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-slate-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-white">Draft Dibuat</p><p class="text-xs text-slate-400">{{ $transaksi->tanggal_transaksi }} · {{ $transaksi->user->name }}</p></div>
                    @if($transaksi->approved_at)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-white">Disetujui oleh {{ $transaksi->approvedByUser->name ?? '-' }}</p><p class="text-xs text-slate-400">{{ $transaksi->approved_at->format('Y-m-d H:i') }} · {{ $transaksi->no_register_akad }}</p></div>
                    @endif
                    @foreach($transaksi->perpanjangan as $ext)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-indigo-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-white">Perpanjangan (+{{ $ext->tambahan_tenor_hari }} hari) <a href="{{ route('transaksi.perpanjangan.cetak', [$transaksi, $ext]) }}" target="_blank" class="ml-2 text-[10px] text-indigo-400">Cetak</a></p><p class="text-xs text-slate-400">{{ $ext->tanggal_perpanjangan }}</p></div>
                    @endforeach
                    @foreach($transaksi->angsuran as $ans)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-amber-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-white">Angsuran Rp {{ number_format($ans->jumlah_bayar,0,',','.') }}</p><p class="text-xs text-slate-400">{{ $ans->tanggal_bayar }} · Sisa: Rp {{ number_format($ans->sisa_pinjaman,0,',','.') }}</p></div>
                    @endforeach
                    @if($transaksi->pelunasan)
                    <div class="relative"><span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900"></span><p class="text-sm font-bold text-white">LUNAS</p><p class="text-xs text-slate-400">{{ $transaksi->pelunasan->tanggal_pelunasan }}</p></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">
            @if($transaksi->status_approval === 'disetujui')
            <div class="glass-card p-6 border-t-4 {{ $transaksi->status == 'lunas' ? 'border-emerald-500' : 'border-amber-500' }}">
                <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Sisa Pinjaman</p>
                <p class="text-3xl font-bold {{ $transaksi->sisa_pinjaman <= 0 ? 'text-emerald-400' : 'text-white' }}">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                @if($transaksi->sisa_pinjaman > 0 && $transaksi->total_pinjaman > 0)
                <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-sky-500 to-emerald-500 rounded-full" style="width:{{ 100-(($transaksi->sisa_pinjaman/$transaksi->total_pinjaman)*100) }}%"></div></div>
                <p class="text-[10px] text-slate-500 mt-1">{{ number_format(100-(($transaksi->sisa_pinjaman/$transaksi->total_pinjaman)*100),0) }}% terbayar</p>
                @endif
            </div>
            @endif

            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-white mb-4">Rincian Keuangan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-400">Total Taksiran</span><span class="text-white font-mono">Rp {{ number_format($transaksi->total_taksiran,0,',','.') }}</span></div>
                    @if($transaksi->taksiran_final)<div class="flex justify-between"><span class="text-slate-400">Taksiran Final</span><span class="text-emerald-400 font-mono font-bold">Rp {{ number_format($transaksi->taksiran_final,0,',','.') }}</span></div>@endif
                    <div class="flex justify-between"><span class="text-slate-400">Pinjaman (QARD)</span><span class="text-sky-400 font-mono font-bold">Rp {{ number_format($transaksi->total_pinjaman,0,',','.') }}</span></div>
                    <div class="flex justify-between border-t border-white/5 pt-3"><span class="text-slate-400">Biaya Admin</span><span class="text-white font-mono">Rp {{ number_format($transaksi->biaya_admin,0,',','.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Ijarah/30hr</span><span class="text-indigo-400 font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari,0,',','.') }}</span></div>
                    <div class="flex justify-between border-t border-white/5 pt-3"><span class="text-slate-400">Metode Biaya</span><span class="text-xs px-2 py-1 rounded-full {{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">{{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'Bayar di Awal' : 'Potong Pinjaman' }}</span></div>
                </div>
            </div>

            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-white mb-4">Info Tenor</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-400">Tenor</span><span class="text-white">{{ $transaksi->tenor_hari }} Hari</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Jatuh Tempo</span><span class="text-white font-semibold">{{ $transaksi->tanggal_jatuh_tempo }}</span></div>
                    <div class="flex justify-between"><span class="text-rose-400">Batas Lelang</span><span class="text-rose-400 font-semibold">{{ $transaksi->tanggal_batas_lelang }}</span></div>
                </div>
            </div>

            <div class="glass-card p-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-slate-800 mr-3 border border-white/10 flex items-center justify-center text-slate-500 font-bold text-sm">{{ substr($transaksi->nasabah->nama,0,1) }}</div>
                <div><p class="text-sm font-bold text-white">{{ $transaksi->nasabah->nama }}</p><p class="text-xs text-sky-400 font-mono">{{ $transaksi->nasabah->telepon }}</p></div>
            </div>
        </div>
    </div>
</div>
@endsection
</x-app-layout>
