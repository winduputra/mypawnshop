<x-app-layout>
    @section('header_title', 'Detail Barang Jaminan')

    @section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar
            </a>
            <a href="{{ route('barang.edit', $barang) }}" class="glass px-4 py-2 rounded-xl text-sm text-slate-300 hover:bg-white/10">
                Edit Barang
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="glass-card p-6">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Nasabah</p>
                    <p class="text-white font-bold">{{ $barang->nasabah->nama }}</p>
                    <p class="text-xs text-sky-400 font-mono">{{ $barang->nasabah->nik }}</p>
                </div>

                <div class="glass-card p-6">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Nilai Taksiran</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($barang->taksiran, 0, ',', '.') }}</p>
                </div>

                <div class="glass-card p-6">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Kategori</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                        @if($barang->kategori == 'emas') bg-amber-500/10 text-amber-500 
                        @elseif($barang->kategori == 'elektronik') bg-sky-500/10 text-sky-400 
                        @else bg-indigo-500/10 text-indigo-400 @endif uppercase">
                        {{ $barang->kategori }}
                    </span>
 
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <div class="glass-card p-8">
                    <h3 class="text-xl font-bold text-white mb-4">Informasi Barang</h3>
                    <p class="text-slate-300 leading-relaxed">
                        {{ $barang->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                    </p>
                </div>

                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-white mb-6">Galeri Foto</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @forelse($barang->fotoBarang as $foto)
                            <div class="relative group aspect-video rounded-xl overflow-hidden border border-white/5 bg-slate-800">
                                <img src="{{ asset('storage/' . $foto->foto_path) }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-black/50 p-2 text-center text-xs text-white backdrop-blur-sm">
                                    {{ $foto->keterangan }}
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 text-sm">Belum ada foto yang diunggah.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
