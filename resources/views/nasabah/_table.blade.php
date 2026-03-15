@forelse($nasabahs as $nasabah)
<tr class="hover:bg-white/5 transition-colors">
    <td class="px-6 py-4 font-mono text-sky-400">{{ $nasabah->nik }}</td>
    <td class="px-6 py-4 text-white font-medium">{{ $nasabah->nama }}</td>
    <td class="px-6 py-4 text-slate-400">{{ $nasabah->telepon }}</td>
    <td class="px-6 py-4 text-slate-400 max-w-xs truncate">{{ $nasabah->alamat }}</td>
    <td class="px-6 py-4 text-right space-x-2">
        <a href="{{ route('nasabah.edit', $nasabah) }}" class="text-sky-400 hover:text-sky-300">Edit</a>
        <form action="{{ route('nasabah.destroy', $nasabah) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="text-rose-400 hover:text-rose-300" onclick="return confirm('Hapus nasabah ini?')">Hapus</button>
        </form>
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
