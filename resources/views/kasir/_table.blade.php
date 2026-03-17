<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                <th class="px-6 py-4">Nama Kasir</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Kantor Cabang</th>
                <th class="px-6 py-4">Dibuat</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5 text-sm">
            @forelse($kasirs as $kasir)
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($kasir->name, 0, 2)) }}
                        </div>
                        <span class="text-white font-medium">{{ $kasir->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-400">{{ $kasir->email }}</td>
                <td class="px-6 py-4">
                    @if($kasir->cabang)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-sky-300 bg-sky-500/10 border border-sky-500/20">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $kasir->cabang->nama_cabang }}
                    </span>
                    @else
                    <span class="text-slate-600 text-xs italic">Belum ditetapkan</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-slate-400">{{ $kasir->created_at->format('d M Y') }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('kasir.edit', $kasir) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-sky-400 hover:text-sky-300 border border-sky-400/20 hover:border-sky-300/30 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('kasir.destroy', $kasir) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus akun kasir ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-rose-400 hover:text-rose-300 border border-rose-400/20 hover:border-rose-300/30 transition-colors">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-10 h-10 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p>Tidak ada kasir ditemukan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($kasirs->hasPages())
<div class="p-6 border-t border-white/5">
    {{ $kasirs->links() }}
</div>
@endif
