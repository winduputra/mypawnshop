<x-app-layout>
    @section('header_title', 'Proses Eksekusi Lelang')

    @section('content')
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('lelang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar Lelang
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Informasi Pinjaman</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Kontrak</span> <span class="text-white font-mono">{{ $transaksi->no_transaksi }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Nasabah</span> <span class="text-white">{{ $transaksi->nasabah->nama }}</span></div>
                        <div class="flex justify-between border-t border-white/5 pt-3"><span class="text-slate-500">Total Pinjaman</span> <span class="text-white font-bold">Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="glass-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Daftar Marhun</h3>
                    <ul class="space-y-2 text-sm text-slate-300">
                        @foreach($transaksi->detailTransaksi as $dt)
                            <li class="flex justify-between">
                                <span>{{ $dt->barang->nama_barang }}</span>
                                <span class="text-xs text-slate-500">{{ ucfirst($dt->barang->kategori) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="glass-card p-8">
                <h3 class="text-xl font-bold text-white mb-6">Formulir Hasil Lelang</h3>
                <form action="{{ route('lelang.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="transaksi_rahn_id" value="{{ $transaksi->id }}">
                    
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Tanggal Lelang Berlangsung</label>
                        <input type="date" name="tanggal_lelang" value="{{ date('Y-m-d') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Nama Pembeli</label>
                        <input type="text" name="pembeli" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Harga Terjual Lelang (Rp)</label>
                        <input type="number" name="harga_lelang" id="harga_lelang" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-xl font-bold" required oninput="calculateExplus()">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Biaya Lelang / Operasional (Rp)</label>
                        <input type="number" name="biaya_lelang" id="biaya_lelang" value="0" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white" required oninput="calculateExplus()">
                    </div>

                    <div class="p-4 bg-sky-500/5 rounded-xl border border-sky-500/20">
                        <p class="text-xs text-sky-400 uppercase font-semibold">Estimasi Kelebihan (Nasabah)</p>
                        <p class="text-2xl font-bold text-white" id="kelebihan_label">Rp 0</p>
                    </div>

                    <button type="submit" class="btn-gradient w-full py-4 rounded-xl text-lg">
                        Finalisasi Eksekusi Lelang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calculateExplus() {
            const pinjaman = {{ $transaksi->total_pinjaman }};
            const harga = parseFloat(document.getElementById('harga_lelang').value) || 0;
            const biaya = parseFloat(document.getElementById('biaya_lelang').value) || 0;
            
            const surplus = Math.max(0, harga - (pinjaman + biaya));
            document.getElementById('kelebihan_label').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(surplus);
        }
    </script>
    @endsection
</x-app-layout>
