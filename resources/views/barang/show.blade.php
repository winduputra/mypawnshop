<x-app-layout>
@section('header_title', 'Detail Barang Jaminan')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
        <a href="{{ route('barang.edit', $barang) }}" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Barang
        </a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- LEFT --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Identitas Barang</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">ID Barang</p>
                            <p class="text-sm font-mono text-sky-400">BRG-{{ str_pad($barang->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Jenis Barang</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold mt-1
                                @if($barang->kategori == 'emas') bg-amber-500/10 text-amber-500
                                @elseif($barang->kategori == 'elektronik') bg-sky-500/10 text-sky-400
                                @else bg-indigo-500/10 text-indigo-400 @endif uppercase">{{ $barang->kategori }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Nasabah</p>
                        <p class="text-sm text-slate-800 font-medium">{{ $barang->nasabah->nama }}</p>
                        <p class="text-xs text-sky-400 font-mono">{{ $barang->nasabah->nik }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Merk / Type</p>
                        <p class="text-sm text-slate-800">{{ $barang->nama_barang }}</p>
                        @if($barang->merk_type)<p class="text-xs text-slate-500">{{ $barang->merk_type }}</p>@endif
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Nomor Seri / Rangka / Polisi</p>
                        <p class="text-sm text-slate-800 font-mono">{{ $barang->nomor_seri ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Spesifikasi</p>
                        <p class="text-sm text-slate-800">{{ $barang->spesifikasi ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Perkiraan Nilai Taksiran</p>
                        <p class="text-xl font-bold text-emerald-400">Rp {{ number_format($barang->taksiran, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- RIGHT --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Kelengkapan & Kondisi</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Kelengkapan</p>
                        @if($barang->kelengkapan && count($barang->kelengkapan))
                            <div class="flex flex-wrap gap-2">
                                @foreach($barang->kelengkapan as $item)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/10 text-sky-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ $item }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500">Tidak ada data kelengkapan.</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Kondisi Fisik</p>
                        <p class="text-sm text-slate-800 leading-relaxed mt-1">{{ $barang->kondisi_fisik ?: ($barang->deskripsi ?: 'Tidak ada deskripsi.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Galeri Foto</h3>
                <div class="grid grid-cols-3 gap-3">
                    @forelse($barang->fotoBarang as $foto)
                    <a href="{{ asset('storage/'.$foto->foto_path) }}" target="_blank" class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-white">
                        <img src="{{ asset('storage/'.$foto->foto_path) }}" class="w-full h-full object-cover group-hover:opacity-80 transition">
                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 p-1 text-center text-[10px] text-slate-800 backdrop-blur-sm">{{ $foto->keterangan }}</div>
                    </a>
                    @empty
                    <p class="text-slate-500 text-sm col-span-3">Belum ada foto.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</x-app-layout>
