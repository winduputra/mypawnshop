<x-app-layout>
    @section('header_title', 'Detail Transaksi Rahn')

    @section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('transaksi.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar
            </a>
            <div class="flex space-x-3">
                @if($transaksi->status == 'aktif' || $transaksi->status == 'diperpanjang')
                    <button onclick="toggleModal('modalPerpanjang')" class="glass px-4 py-2 rounded-xl text-sm text-indigo-400 hover:bg-white/10">Perpanjang Tenor</button>
                    <button onclick="toggleModal('modalPelunasan')" class="btn-gradient px-6 py-2 rounded-xl text-sm">Proses Pelunasan</button>
                @endif
            </div>

            <!-- Modal Perpanjangan -->
            <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
                <div class="glass-card max-w-md w-full p-8 relative">
                    <button onclick="toggleModal('modalPerpanjang')" class="absolute top-4 right-4 text-slate-500 hover:text-white">&times;</button>
                    <h3 class="text-xl font-bold text-white mb-6">Perpanjang Tenor</h3>
                    <form action="{{ route('transaksi.perpanjang', $transaksi) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Biaya Ujrah (30 Hari)</label>
                            <input type="number" name="ujrah_dibayar" value="{{ $transaksi->ujrah_per_30hari }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                            <p class="text-[10px] text-slate-500 mt-2 italic">Nasabah membayar ujrah untuk memperpanjang jatuh tempo selama 30 hari.</p>
                        </div>
                        <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Bayar & Perpanjang</button>
                    </form>
                </div>
            </div>

            <!-- Modal Pelunasan -->
            <div id="modalPelunasan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
                <div class="glass-card max-w-md w-full p-8 relative">
                    <button onclick="toggleModal('modalPelunasan')" class="absolute top-4 right-4 text-slate-500 hover:text-white">&times;</button>
                    <h3 class="text-xl font-bold text-white mb-6">Pelunasan Gadai</h3>
                    <form action="{{ route('transaksi.pelunasan', $transaksi) }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="space-y-4">
                            <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                <p class="text-xs text-emerald-400 uppercase font-semibold">Total Wajib Bayar</p>
                                <p class="text-2xl font-bold text-white">Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-slate-400 mb-2">Jumlah Bayar (Rp)</label>
                                <input type="number" name="total_bayar" value="{{ $transaksi->total_pinjaman }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                                <p class="text-[10px] text-slate-500 mt-2">Pastikan nominal sesuai dengan pelunasan pokok marhun bih.</p>
                            </div>
                        </div>
                        <button type="submit" class="btn-gradient w-full py-4 rounded-xl">Proses Pelunasan Lunas</button>
                    </form>
                </div>
            </div>

            <script>
                function toggleModal(id) {
                    const m = document.getElementById(id);
                    m.classList.toggle('hidden');
                }
            </script>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Transaction Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Header -->
                <div class="glass-card p-6 flex items-center justify-between border-l-4 
                    @if($transaksi->status == 'aktif') border-sky-500 
                    @elseif($transaksi->status == 'lunas') border-emerald-500 
                    @else border-rose-500 @endif">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Status Transaksi</p>
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
                                    <p class="text-xs text-slate-500 uppercase font-semibold">{{ $detail->barang->kategori }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-white font-bold">Rp {{ number_format($detail->pinjaman_item, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase">Pinjaman Per Item</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Timeline / History -->
                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-white mb-6">Riwayat Transaksi</h3>
                    <div class="relative pl-8 border-l border-white/10 space-y-8">
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-sky-500 border-4 border-slate-900 shadow-sm"></span>
                            <p class="text-sm font-bold text-white">Transaksi Dibuat</p>
                            <p class="text-xs text-slate-400">{{ $transaksi->tanggal_transaksi }} - Oleh: {{ $transaksi->user->name }}</p>
                        </div>

                        @foreach($transaksi->perpanjangan as $extend)
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-indigo-500 border-4 border-slate-900 shadow-sm"></span>
                            <p class="text-sm font-bold text-white">Perpanjangan Tenor (+{{ $extend->tambahan_tenor_hari }} hari)</p>
                            <p class="text-xs text-slate-400">{{ $extend->tanggal_perpanjangan }} - Ujrah Dibayar: Rp {{ number_format($extend->ujrah_dibayar, 0, ',', '.') }}</p>
                        </div>
                        @endforeach

                        @if($transaksi->pelunasan)
                        <div class="relative">
                            <span class="absolute -left-[41px] top-1 w-5 h-5 rounded-full bg-emerald-500 border-4 border-slate-900 shadow-sm"></span>
                            <p class="text-sm font-bold text-white">Pelunasan Selesai</p>
                            <p class="text-xs text-slate-400">{{ $transaksi->pelunasan->tanggal_pelunasan }} - Total Bayar: Rp {{ number_format($transaksi->pelunasan->total_bayar, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Financial Summary -->
            <div class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-white mb-6">Rincian Keuangan</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Total Taksiran</span>
                            <span class="text-white font-mono">Rp {{ number_format($transaksi->total_taksiran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Pinjaman (Marhun Bih)</span>
                            <span class="text-sky-400 font-mono font-bold">Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Biaya Admin</span>
                            <span class="text-white font-mono">Rp {{ number_format($transaksi->biaya_admin, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-t border-white/5 pt-4">
                            <span class="text-slate-400">Ujrah per 30 Hari</span>
                            <span class="text-indigo-400 font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-white mb-6">Informasi Tenor</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Pilihan Tenor</span>
                            <span class="text-white">{{ $transaksi->tenor_hari }} Hari</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Jatuh Tempo</span>
                            <span class="text-white font-semibold">{{ $transaksi->tanggal_jatuh_tempo }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400 text-rose-400">Batas Lelang</span>
                            <span class="text-rose-400 font-semibold">{{ $transaksi->tanggal_batas_lelang }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Mini Info -->
                <div class="glass-card p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-800 mr-4 border border-white/10">
                        @if($transaksi->nasabah->foto_ktp)
                            <img src="{{ asset('storage/' . $transaksi->nasabah->foto_ktp) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold">
                                {{ substr($transaksi->nasabah->nama, 0, 1) }}
                            </div>
                        @endif
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
