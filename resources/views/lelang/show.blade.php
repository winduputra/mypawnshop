@extends('layouts.app')

@section('header_title', 'Proses Lelang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4 mb-6">
        <a href="{{ route('lelang.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-white">Eksekusi Lelang</h1>
            <p class="text-sm text-gray-400 mt-1">Transaksi: {{ $transaksi->no_transaksi }}</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Transaksi -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Informasi Pinjaman</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Nasabah</p>
                    <p class="text-white font-medium">{{ $transaksi->nasabah->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $transaksi->nasabah->telepon }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-1">Barang Jaminan</p>
                    <ul class="list-disc list-inside text-sm text-slate-300">
                        @foreach($transaksi->detailTransaksi as $dt)
                            <li>{{ $dt->barang->nama_barang }} (Rp {{ number_format($dt->taksiran_item, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                </div>

                <div class="pt-4 border-t border-white/10">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-slate-400">Total Taksiran</span>
                        <span class="text-white font-mono">Rp {{ number_format($transaksi->total_taksiran, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-slate-400">Total Pinjaman Awal</span>
                        <span class="text-white font-mono">Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-white/5">
                        <span class="text-sm font-medium text-white">Sisa Pinjaman (Pokok + Tunggakan)</span>
                        <span class="text-lg font-bold text-rose-400 font-mono">Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Lelang -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Input Data Lelang</h3>
            
            <form action="{{ route('lelang.store') }}" method="POST" class="space-y-5" onsubmit="return confirm('Apakah data lelang sudah benar? Aksi ini tidak dapat dibatalkan.');">
                @csrf
                <input type="hidden" name="transaksi_rahn_id" value="{{ $transaksi->id }}">
                <input type="hidden" id="sisa_pinjaman" value="{{ $transaksi->sisa_pinjaman }}">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal Lelang</label>
                    <input type="date" name="tanggal_lelang" value="{{ date('Y-m-d') }}" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Nama Pembeli</label>
                    <input type="text" name="pembeli" required placeholder="Masukkan nama pembeli"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Harga Terjual (Rp)</label>
                    <input type="text" name="harga_lelang" id="harga_lelang" required placeholder="0" oninput="calculateLelang()"
                        class="currency-input w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white text-lg font-mono focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Biaya Lelang / Admin (Rp)</label>
                    <input type="text" name="biaya_lelang" id="biaya_lelang" required value="0" oninput="calculateLelang()"
                        class="currency-input w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white font-mono focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                </div>

                <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-emerald-400">Dana Turun (Kembali ke Nasabah)</span>
                        <span id="label_kembali" class="text-emerald-400 font-mono font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-white/5">
                        <span class="text-sm font-medium text-rose-400">Kerugian (Kekurangan)</span>
                        <span id="label_rugi" class="text-rose-400 font-mono font-bold">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="w-full btn-gradient py-3 rounded-xl mt-4 text-base">
                    Proses Eksekusi Lelang
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function parseRupiah(val) {
        if(!val) return 0;
        return parseInt(val.replace(/[^0-9]/g, '')) || 0;
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    function calculateLelang() {
        const pinjaman = parseFloat(document.getElementById('sisa_pinjaman').value) || 0;
        const harga = parseRupiah(document.getElementById('harga_lelang').value);
        const biaya = parseRupiah(document.getElementById('biaya_lelang').value);

        const totalKewajiban = pinjaman + biaya;

        let kembali = 0;
        let rugi = 0;

        if (harga > totalKewajiban) {
            kembali = harga - totalKewajiban;
        } else if (harga < totalKewajiban) {
            rugi = totalKewajiban - harga;
        }

        document.getElementById('label_kembali').innerText = formatRupiah(kembali, 'Rp ');
        document.getElementById('label_rugi').innerText = formatRupiah(rugi, 'Rp ');
    }
</script>
@endpush
@endsection
