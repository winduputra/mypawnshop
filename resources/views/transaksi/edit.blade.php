<x-app-layout>
@section('header_title', 'Edit Akad Pinjaman')
@section('content')
@php
    $detail = $transaksi->detailTransaksi->first();
    $barang = $detail->barang;
    $maxPinjaman = $barang->taksiran * \App\Models\Setting::getLoanPercentage($barang->kategori);
@endphp
<div class="max-w-3xl mx-auto">
    <a href="{{ route('transaksi.show', $transaksi) }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Detail
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Edit Akad Pending</h2>
            <p class="text-sm text-slate-500 mt-1">Perbaiki data sesuai catatan admin, lalu kirim ulang dari halaman detail.</p>
        </div>

        @if($transaksi->catatan_admin)
        <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
            <p class="text-xs font-semibold uppercase text-amber-400 mb-1">Catatan Admin</p>
            <p class="text-sm text-slate-800">{{ $transaksi->catatan_admin }}</p>
        </div>
        @endif

        <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
            <div class="font-semibold text-slate-800">{{ $transaksi->nasabah->nama }}</div>
            <div class="text-slate-500">{{ $barang->nama_barang }} · {{ ucfirst($barang->kategori) }} · Taksiran Rp {{ number_format($barang->taksiran, 0, ',', '.') }}</div>
        </div>

        <form action="{{ route('transaksi.update', $transaksi) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm text-slate-500 mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', $transaksi->tanggal_transaksi) }}" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800" required>
            </div>
            <div>
                <label class="block text-sm text-slate-500 mb-2">Jumlah Pinjaman</label>
                <input type="number" name="total_pinjaman" value="{{ old('total_pinjaman', (int) $transaksi->total_pinjaman) }}" min="1" max="{{ (int) $maxPinjaman }}" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800" required>
                <p class="text-xs text-slate-500 mt-1">Maksimal Rp {{ number_format($maxPinjaman, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="block text-sm text-slate-500 mb-2">Metode Pembayaran Biaya</label>
                <select name="metode_pembayaran" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800" required>
                    <option value="potong_pinjaman" @selected(old('metode_pembayaran', $transaksi->metode_pembayaran) === 'potong_pinjaman')>Dipotong dari Pinjaman</option>
                    <option value="bayar_dimuka" @selected(old('metode_pembayaran', $transaksi->metode_pembayaran) === 'bayar_dimuka')>Dibayar di Awal (Cash)</option>
                    <option value="bayar_pelunasan" @selected(old('metode_pembayaran', $transaksi->metode_pembayaran) === 'bayar_pelunasan')>Dibayar Saat Pelunasan</option>
                </select>
            </div>
            <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-3 px-4 rounded-xl shadow-sm transition-all w-full">Simpan Perbaikan</button>
        </form>
    </div>
</div>
@endsection
</x-app-layout>
