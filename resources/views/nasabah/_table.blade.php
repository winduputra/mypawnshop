@forelse($nasabahs as $nasabah)
<tr class="hover:bg-white/5 transition-colors">
    <td class="px-6 py-4 font-mono text-sky-400">{{ $nasabah->nik }}</td>
    <td class="px-6 py-4 text-white font-medium">{{ $nasabah->nama }}</td>
    <td class="px-6 py-4 text-slate-400">{{ $nasabah->telepon }}</td>
    <td class="px-6 py-4 text-slate-400 max-w-xs truncate">{{ $nasabah->alamat }}</td>
    <td class="px-6 py-4 text-right space-x-2">
        <a href="{{ route('nasabah.show', $nasabah) }}" class="text-white/60 hover:text-white transition inline-flex items-center" title="Detail">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </a>
        <a href="{{ route('nasabah.edit', $nasabah) }}" class="text-sky-400 hover:text-sky-300 transition inline-flex items-center" title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </a>
        @if(auth()->user()->role === 'admin')
        <form action="{{ route('nasabah.destroy', $nasabah) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="text-rose-400 hover:text-rose-300 transition inline-flex items-center" title="Hapus" onclick="return confirm('Hapus nasabah ini?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada data nasabah</td>
</tr>
@endforelse

<!-- Update pagination via script or placement -->
@if($nasabahs->hasPages())
<tr>
    <td colspan="5" class="p-0">
        <div id="pagination-ajax" class="p-6 border-t border-white/5">
            {{ $nasabahs->links() }}
        </div>
    </td>
</tr>
@endif
