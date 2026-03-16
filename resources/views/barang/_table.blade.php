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
    <td class="px-6 py-4 text-white font-semibold flex flex-col">
        <span class="text-xs text-slate-500">Rp</span> 
        <span>{{ number_format($barang->taksiran, 0, ',', '.') }}</span>
    </td>
    <td class="px-6 py-4">
        @if($barang->isSedangDigadai())
            <span class="px-2 py-1 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                Sedang Digadai
            </span>
        @else
            <span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                Bisa Digadai
            </span>
        @endif
    </td>
    <td class="px-6 py-4 text-right space-x-2">
        <a href="{{ route('barang.show', $barang) }}" class="text-white/60 hover:text-white transition inline-flex items-center" title="Detail">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </a>
        
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('barang.edit', $barang) }}" class="text-sky-400 hover:text-sky-300 transition inline-flex items-center" title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </a>
            
            <form action="{{ route('barang.destroy', $barang) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="text-rose-400 hover:text-rose-300 transition inline-flex items-center" title="Hapus" onclick="return confirm('Hapus barang ini?')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data barang jaminan</td>
</tr>
@endforelse

@if($barangs->hasPages())
<tr>
    <td colspan="6" class="p-0">
        <div class="p-6 border-t border-white/5">
            {{ $barangs->links() }}
        </div>
    </td>
</tr>
@endif
