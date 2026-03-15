<x-app-layout>
    @section('header_title', 'Barang Jaminan')

    @section('content')
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-white">Daftar Barang Jaminan</h3>
        <a href="{{ route('barang.create') }}" class="btn-gradient">
            Tambah Barang
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Pemilik (Nasabah)</th>
                        <th class="px-6 py-4">Taksiran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($barangs as $barang)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">{{ $barang->nama_barang }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                @if($barang->kategori == 'emas') bg-amber-500/10 text-amber-500 
                                @elseif($barang->kategori == 'elektronik') bg-sky-500/10 text-sky-400 
                                @else bg-indigo-500/10 text-indigo-400 @endif">
                                {{ ucfirst($barang->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400">{{ $barang->nasabah->nama }}</td>
                        <td class="px-6 py-4 text-white font-semibold">Rp {{ number_format($barang->taksiran, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('barang.show', $barang) }}" class="text-sky-400 hover:text-sky-300">Detail</a>
                            <a href="{{ route('barang.edit', $barang) }}" class="text-slate-400 hover:text-slate-300">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada data barang jaminan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangs->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $barangs->links() }}
        </div>
        @endif
    </div>
    @endsection
</x-app-layout>
