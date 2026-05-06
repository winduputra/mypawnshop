<x-app-layout>
@section('header_title', 'Review Akad Pinjaman')
@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('transaksi.show', $transaksi) }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Kembali ke Detail
    </a>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Review Akad Pinjaman</h2>
        <p class="text-sm text-slate-500 mt-1">Ref: <span class="font-mono text-sky-400">{{ $transaksi->no_transaksi }}</span> · Kasir: {{ $transaksi->user->name }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: Detail --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Nasabah --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Data Nasabah</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-xs text-slate-500">Nama</p><p class="text-slate-800 font-medium">{{ $transaksi->nasabah->nama }}</p></div>
                    <div><p class="text-xs text-slate-500">NIK</p><p class="text-slate-800 font-mono">{{ $transaksi->nasabah->nik }}</p></div>
                    <div><p class="text-xs text-slate-500">Telepon</p><p class="text-slate-800">{{ $transaksi->nasabah->telepon }}</p></div>
                    <div><p class="text-xs text-slate-500">Email</p><p class="text-slate-800">{{ $transaksi->nasabah->email ?? '-' }}</p></div>
                </div>
            </div>

            {{-- Barang --}}
            @foreach($transaksi->detailTransaksi as $detail)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Barang Jaminan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-xs text-slate-500">Nama Barang</p><p class="text-slate-800 font-medium">{{ $detail->barang->nama_barang }}</p></div>
                    <div><p class="text-xs text-slate-500">Kategori</p><span class="inline-block px-2 py-1 rounded-full text-xs font-bold @if($detail->barang->kategori=='emas') bg-amber-500/10 text-amber-500 @elseif($detail->barang->kategori=='elektronik') bg-sky-500/10 text-sky-400 @else bg-indigo-500/10 text-indigo-400 @endif uppercase">{{ $detail->barang->kategori }}</span></div>
                    <div><p class="text-xs text-slate-500">Merk/Type</p><p class="text-slate-800">{{ $detail->barang->merk_type ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">No Seri/Rangka</p><p class="text-slate-800 font-mono">{{ $detail->barang->nomor_seri ?? '-' }}</p></div>
                    @if($detail->barang->spesifikasi)<div class="col-span-2"><p class="text-xs text-slate-500">Spesifikasi</p><p class="text-slate-800">{{ $detail->barang->spesifikasi }}</p></div>@endif
                    @if($detail->barang->kondisi_fisik)<div class="col-span-2"><p class="text-xs text-slate-500">Kondisi Fisik</p><p class="text-slate-800">{{ $detail->barang->kondisi_fisik }}</p></div>@endif
                    @if($detail->barang->kelengkapan && count($detail->barang->kelengkapan))
                    <div class="col-span-2"><p class="text-xs text-slate-500 mb-2">Kelengkapan</p><div class="flex flex-wrap gap-2">@foreach($detail->barang->kelengkapan as $k)<span class="px-2 py-1 rounded-full text-xs bg-sky-500/10 text-sky-400">✓ {{ $k }}</span>@endforeach</div></div>
                    @endif
                </div>
                {{-- Foto --}}
                @if($detail->barang->fotoBarang->count())
                <div class="mt-4 grid grid-cols-3 gap-3">
                    @foreach($detail->barang->fotoBarang as $foto)
                    <a href="{{ asset('storage/'.$foto->foto_path) }}" target="_blank" class="aspect-square rounded-lg overflow-hidden border border-slate-300">
                        <img src="{{ asset('storage/'.$foto->foto_path) }}" class="w-full h-full object-cover hover:opacity-80 transition">
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            {{-- Rincian Keuangan --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-4">Rincian Keuangan (Kasir)</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-xs text-slate-500">Total Taksiran</p><p class="text-xl font-bold text-slate-800">Rp {{ number_format($transaksi->total_taksiran,0,',','.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Pinjaman (QARD)</p><p class="text-xl font-bold text-sky-400">Rp {{ number_format($transaksi->total_pinjaman,0,',','.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Biaya Admin</p><p class="text-slate-800 font-mono">Rp {{ number_format($transaksi->biaya_admin,0,',','.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Ijarah/30hr</p><p class="text-indigo-400 font-mono">Rp {{ number_format($transaksi->ujrah_per_30hari,0,',','.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Tenor</p><p class="text-slate-800">{{ $transaksi->tenor_hari }} Hari</p></div>
                    <div><p class="text-xs text-slate-500">Metode Biaya</p><p class="text-slate-800">{{ $transaksi->metode_pembayaran == 'bayar_dimuka' ? 'Bayar di Awal' : 'Potong Pinjaman' }}</p></div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Action Panel --}}
        <div class="space-y-6">
            {{-- SETUJU --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-emerald-500">
                <h3 class="text-base font-semibold text-emerald-400 mb-4">✓ Setujui Akad</h3>
                <form action="{{ route('transaksi.approve', $transaksi) }}" method="POST" onsubmit="return confirm('Setujui akad ini? Nomor register akan digenerate otomatis.');">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs text-slate-500 mb-1">Nilai Taksiran Final (Rp) <span class="text-rose-400">*</span></label>
                        <input type="text" name="taksiran_final" id="inp_taksiran_final" value="{{ number_format($transaksi->total_taksiran,0,'','') }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm font-bold" required>
                        <p class="text-[10px] text-slate-500 mt-1">Taksiran kasir: Rp {{ number_format($transaksi->total_taksiran,0,',','.') }}. Sesuaikan jika perlu.</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs text-slate-500 mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan_admin" rows="2" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" placeholder="Alasan perubahan taksiran atau pesan untuk kasir..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-slate-800 font-semibold text-sm transition">SETUJU</button>
                </form>
            </div>

            {{-- PENDING --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-amber-500">
                <h3 class="text-base font-semibold text-amber-400 mb-4">⏳ Pending (Kembalikan ke Kasir)</h3>
                <form action="{{ route('transaksi.pending', $transaksi) }}" method="POST" onsubmit="return confirm('Kembalikan akad ke kasir?');">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs text-slate-500 mb-1">Catatan untuk Kasir <span class="text-rose-400">*</span></label>
                        <textarea name="catatan_admin" rows="3" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required placeholder="Jelaskan data yang perlu dilengkapi atau dicek ulang..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-slate-800 font-semibold text-sm transition">PENDING</button>
                </form>
            </div>

            {{-- TOLAK --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-rose-500">
                <h3 class="text-base font-semibold text-rose-400 mb-4">✕ Tolak Akad</h3>
                <form action="{{ route('transaksi.reject', $transaksi) }}" method="POST" onsubmit="return confirm('TOLAK akad ini? Akad tidak bisa diubah lagi oleh kasir.');">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs text-slate-500 mb-1">Alasan Penolakan <span class="text-rose-400">*</span></label>
                        <textarea name="catatan_admin" rows="3" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-slate-800 font-semibold text-sm transition">TIDAK SETUJU</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const inp=document.getElementById('inp_taksiran_final');
    let v=inp.value.replace(/\D/g,'');
    if(v)inp.value=parseInt(v).toLocaleString('id-ID');
    inp.addEventListener('input',function(){let v=this.value.replace(/\D/g,'');this.value=v?parseInt(v).toLocaleString('id-ID'):'';});
    inp.closest('form').addEventListener('submit',function(){inp.value=inp.value.replace(/\D/g,'');});
});
</script>
@endsection
</x-app-layout>
