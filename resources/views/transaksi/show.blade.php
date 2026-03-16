<x-app-layout>
    @section('header_title', 'Detail Transaksi Rahn')

    @section('content')
    <div class="max-w-6xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-sm">
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
            <a href="{{ route('transaksi.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar
            </a>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('transaksi.kontrak-pdf', $transaksi) }}" class="glass px-4 py-2 rounded-xl text-sm text-emerald-400 hover:bg-white/10 flex items-center" target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Kontrak
                </a>
                @if($transaksi->status == 'lunas')
                    <a href="{{ route('transaksi.nota-lunas', $transaksi) }}" class="glass px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10 flex items-center" target="_blank">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Cetak Nota Lunas
                    </a>
                @endif
                @php
                    $waMessage = "Yth. Bpk/Ibu *" . $transaksi->nasabah->nama . "*,\n" .
                               "Kami informasikan bahwa kontrak gadai dengan nomor *" . $transaksi->no_transaksi . "* akan jatuh tempo pada tanggal *" . $transaksi->tanggal_jatuh_tempo . "*.\n\n" .
                               "Mohon melakukan pembayaran pokok pinjaman atau perpanjangan tenor melalui kasir sebelum tanggal tersebut.\n\n" .
                               "Apabila tidak ada konfirmasi hingga tanggal *" . $transaksi->tanggal_jatuh_tempo . "*, barang jaminan *" . $transaksi->detailTransaksi->first()->barang->nama_barang . "* akan diproses sesuai ketentuan yang berlaku.";
                    $waUrl = "https://wa.me/" . $transaksi->nasabah->whatsapp_number . "?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="glass px-4 py-2 rounded-xl text-sm text-emerald-400 hover:bg-white/10 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp Reminder
                </a>
                @if($transaksi->status == 'aktif' || $transaksi->status == 'diperpanjang')
                    <button onclick="toggleModal('modalAngsuran')" class="glass px-4 py-2 rounded-xl text-sm text-amber-400 hover:bg-white/10">Bayar Angsuran</button>
                    <button onclick="toggleModal('modalPerpanjang')" class="glass px-4 py-2 rounded-xl text-sm text-indigo-400 hover:bg-white/10">Perpanjang Tenor</button>
                    <button onclick="toggleModal('modalPelunasan')" class="btn-gradient px-6 py-2 rounded-xl text-sm">Lunasi Semua</button>
                @endif
            </div>
        </div>

        <!-- Modal Angsuran -->
        <div id="modalAngsuran" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
            <div class="glass-card max-w-md w-full p-8 relative">
                <button onclick="toggleModal('modalAngsuran')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
                <h3 class="text-xl font-bold text-white mb-6">Bayar Angsuran</h3>
                <form action="{{ route('transaksi.angsuran', $transaksi) }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="p-4 bg-sky-500/10 rounded-xl border border-sky-500/20">
                        <p class="text-xs text-sky-400 uppercase font-semibold">Sisa Pinjaman</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Jumlah Bayar (Rp)</label>
                        <input type="text" name="jumlah_bayar" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                        <p class="text-[10px] text-slate-500 mt-2">Nasabah dapat mencicil sesuai kemampuan. Pinjaman otomatis lunas jika sisa = 0.</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-sm" placeholder="Cicilan minggu ke-...">
                    </div>
                    <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Bayar Angsuran</button>
                </form>
            </div>
        </div>

        <!-- Modal Perpanjangan -->
        <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
            <div class="glass-card max-w-md w-full p-8 relative">
                <button onclick="toggleModal('modalPerpanjang')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
                <h3 class="text-xl font-bold text-white mb-6">Perpanjang Tenor</h3>
                
                @php
                    $detail = $transaksi->detailTransaksi->first();
                    $kategoriStr = $detail ? $detail->barang->kategori : 'emas';
                    $limits = ['emas' => 11, 'elektronik' => 2, 'kendaraan' => 3];
                    $maxLimit = $limits[$kategoriStr] ?? 1;
                    $extendCount = $transaksi->perpanjangan->count();
                    $canExtend = $extendCount < $maxLimit;
                @endphp
                
                @if($canExtend)
                <div class="mb-4 p-3 bg-sky-500/10 border border-sky-500/20 rounded-xl">
                    <p class="text-xs text-sky-400">Terpakai: <strong>{{ $extendCount }} / {{ $maxLimit }}x</strong> Perpanjangan ({{ ucfirst($kategoriStr) }})</p>
                </div>
                <form action="{{ route('transaksi.perpanjang', $transaksi) }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="p-4 bg-indigo-500/10 rounded-xl border border-indigo-500/20 text-center mb-6">
                        <p class="text-xs text-indigo-400 uppercase font-semibold mb-1">Biaya Perpanjangan</p>
                        <p class="text-3xl font-bold text-white font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-slate-400 mt-2">Biaya penitipan ini akan ditagihkan untuk tambahan tenor 30 hari ke depan.</p>
                    </div>

                    <label class="flex items-start cursor-pointer group">
                        <div class="flex items-center h-5">
                            <input type="checkbox" required class="w-5 h-5 border-white/10 rounded bg-slate-800 text-indigo-500 focus:ring-indigo-500 cursor-pointer">
                        </div>
                        <div class="ml-3 text-sm">
                            <span class="font-medium text-slate-300 group-hover:text-white transition-colors">Nasabah setuju untuk memperpanjang tenor.</span>
                            <p class="text-slate-500 text-xs mt-1">Nota perpanjangan akan secara otomatis diterbitkan jika disetujui.</p>
                        </div>
                    </label>

                    <button type="submit" class="btn-gradient w-full py-4 rounded-xl mt-4">Bayar & Perpanjang Tenor</button>
                </form>
                @else
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                    <p class="text-sm text-rose-400 font-semibold mb-1">Batas Perpanjangan Tercapai</p>
                    <p class="text-xs text-slate-400">Transaksi dengan jaminan {{ ucfirst($kategoriStr) }} hanya dapat diperpanjang maksimal {{ $maxLimit }} kali.</p>
                </div>
                <button onclick="toggleModal('modalPerpanjang')" class="w-full py-4 rounded-xl glass text-slate-400 hover:text-white hover:bg-white/10 transition-colors">Tutup</button>
                @endif
            </div>
        </div>

        <!-- Modal Pelunasan -->
        <div id="modalPelunasan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
            <div class="glass-card max-w-md w-full p-8 relative">
                <button onclick="toggleModal('modalPelunasan')" class="absolute top-4 right-4 text-slate-500 hover:text-white text-xl">&times;</button>
                <h3 class="text-xl font-bold text-white mb-6">Pelunasan Gadai</h3>
                <form action="{{ route('transaksi.pelunasan', $transaksi) }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                        <p class="text-xs text-emerald-400 uppercase font-semibold">Sisa Pinjaman</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Jumlah Bayar (Rp)</label>
                        <input type="text" name="total_bayar" value="{{ $transaksi->sisa_pinjaman }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                    </div>
                    <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Proses Pelunasan</button>
                </form>
            </div>
        </div>

        <script>
            function toggleModal(id) {
                document.getElementById(id).classList.toggle('hidden');
            }
        </script>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Transaction Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Header -->
                <div class="glass-card p-6 flex items-center justify-between border-l-4 
                    @if($transaksi->status == 'aktif') border-sky-500 
                    @elseif($transaksi->status == 'lunas') border-emerald-500 
                    @else border-rose-500 @endif">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Status</p>
                        <h3 class="text-2xl font-bold @if($transaksi->status == 'aktif') text-sky-400 @elseif($transaksi->status == 'lunas') text-emerald-400 @else text-rose-400 @endif">
                            {{ strtoupper($transaksi->status) }}
                        </h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500 uppercase font-semibold">No. Kontrak</p>
                        <p class="text-lg font-mono text-white font-bold">{{ $transaksi->no_transaksi }}</p>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-white mb-6">Barang Jaminan (Marhun)</h3>
                    <div class="space-y-4">
                        @foreach($transaksi->detailTransaksi as $detail)
                            <div class="flex items-center p-4 bg-white/5 rounded-2xl border border-white/5">
                                <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">{{ $detail->barang->nama_barang }}</p>
                                    <p class="text-xs text-slate-500 uppercase">{{ $detail->barang->kategori }} | Taksiran: Rp {{ number_format($detail->taksiran_item, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-white font-bold">Rp {{ number_format($detail->pinjaman_item, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-slate-500">Pinjaman</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Angsuran History -->
                @if($transaksi->angsuran->count() > 0)
                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-white mb-6">Riwayat Angsuran</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3 text-right">Dibayar</th>
                                    <th class="px-4 py-3 text-right">Sisa</th>
                                    <th class="px-4 py-3">Kasir</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm">
                                @foreach($transaksi->angsuran as $idx => $ans)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3 text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 text-white">{{ $ans->tanggal_bayar }}</td>
                                    <td class="px-4 py-3 text-emerald-400 font-mono text-right">Rp {{ number_format($ans->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-white font-mono text-right">Rp {{ number_format($ans->sisa_pinjaman, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-slate-400">{{ $ans->user->name }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('transaksi.angsuran.cetak', [$transaksi, $ans]) }}" class="text-sky-400 hover:text-sky-300 text-xs font-medium" target="_blank">Cetak Bukti</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Timeline / History -->
                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-white mb-6">Riwayat Transaksi</h3>
                    <div class="relative pl-8 border-l border-white/10 space-y-8">
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-sky-500 border-4 border-slate-900"></span>
                            <p class="text-sm font-bold text-white">Transaksi Dibuat</p>
                            <p class="text-xs text-slate-400">{{ $transaksi->tanggal_transaksi }} · Kasir: {{ $transaksi->user->name }}</p>
                        </div>

                        @foreach($transaksi->perpanjangan as $extend)
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-indigo-500 border-4 border-slate-900"></span>
                            <p class="text-sm font-bold text-white">Perpanjangan (+{{ $extend->tambahan_tenor_hari }} hari)
                                <a href="{{ route('transaksi.perpanjangan.cetak', [$transaksi, $extend]) }}" target="_blank" class="ml-2 text-[10px] font-normal text-indigo-400 hover:text-indigo-300">Cetak Nota</a>
                            </p>
                            <p class="text-xs text-slate-400">{{ $extend->tanggal_perpanjangan }} · Penitipan: Rp {{ number_format($extend->ujrah_dibayar, 0, ',', '.') }}</p>
                        </div>
                        @endforeach

                        @foreach($transaksi->angsuran as $ans)
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-amber-500 border-4 border-slate-900"></span>
                            <p class="text-sm font-bold text-white">Angsuran Rp {{ number_format($ans->jumlah_bayar, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">{{ $ans->tanggal_bayar }} · Sisa: Rp {{ number_format($ans->sisa_pinjaman, 0, ',', '.') }}{{ $ans->catatan ? ' · ' . $ans->catatan : '' }}</p>
                        </div>
                        @endforeach

                        @if($transaksi->pelunasan)
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900"></span>
                            <p class="text-sm font-bold text-white">LUNAS</p>
                            <p class="text-xs text-slate-400">{{ $transaksi->pelunasan->tanggal_pelunasan }} · Bayar: Rp {{ number_format($transaksi->pelunasan->total_bayar, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Financial Summary -->
            <div class="space-y-6">
                <!-- Sisa Pinjaman Tracker -->
                <div class="glass-card p-6 border-t-4 {{ $transaksi->status == 'lunas' ? 'border-emerald-500' : 'border-amber-500' }}">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Sisa Pinjaman</p>
                    <p class="text-3xl font-bold {{ $transaksi->sisa_pinjaman <= 0 ? 'text-emerald-400' : 'text-white' }}">
                        Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}
                    </p>
                    @if($transaksi->sisa_pinjaman > 0 && $transaksi->total_pinjaman > 0)
                    <div class="mt-4 h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-sky-500 to-emerald-500 rounded-full transition-all" 
                             style="width: {{ 100 - (($transaksi->sisa_pinjaman / $transaksi->total_pinjaman) * 100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">{{ number_format(100 - (($transaksi->sisa_pinjaman / $transaksi->total_pinjaman) * 100), 0) }}% terbayar</p>
                    @endif
                </div>

                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-white mb-6">Rincian Keuangan</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Total Taksiran</span>
                            <span class="text-white font-mono">Rp {{ number_format($transaksi->total_taksiran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pinjaman Pokok</span>
                            <span class="text-sky-400 font-mono font-bold">Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-white/5 pt-4">
                            <span class="text-slate-400">Biaya Admin</span>
                            <span class="text-white font-mono">Rp {{ number_format($transaksi->biaya_admin, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Biaya Penitipan</span>
                            <span class="text-white font-mono">Rp {{ number_format($transaksi->biaya_penitipan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Penitipan/30hr</span>
                            <span class="text-indigo-400 font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-white/5 pt-4">
                            <span class="text-slate-400">Metode Biaya</span>
                            <span class="text-xs px-2 py-1 rounded-full {{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                {{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'Bayar di Awal' : 'Potong Pinjaman' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-white mb-6">Informasi Tenor</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tenor</span>
                            <span class="text-white">{{ $transaksi->tenor_hari }} Hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Jatuh Tempo</span>
                            <span class="text-white font-semibold">{{ $transaksi->tanggal_jatuh_tempo }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-rose-400">Batas Lelang</span>
                            <span class="text-rose-400 font-semibold">{{ $transaksi->tanggal_batas_lelang }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-800 mr-4 border border-white/10 flex items-center justify-center text-slate-500 font-bold">
                        {{ substr($transaksi->nasabah->nama, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">{{ $transaksi->nasabah->nama }}</p>
                        <p class="text-xs text-sky-400 font-mono">{{ $transaksi->nasabah->telepon }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
