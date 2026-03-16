<x-app-layout>
    @section('header_title', 'Kelola Kasir')

    @section('content')
    <div class="max-w-4xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Daftar Akun Kasir</h3>
            <a href="{{ route('kasir.create') }}" class="btn-gradient">
                Tambah Kasir
            </a>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Dibuat</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        @forelse($kasirs as $kasir)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-white font-medium">{{ $kasir->name }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $kasir->email }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $kasir->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('kasir.edit', $kasir) }}" class="text-sky-400 hover:text-sky-300 font-medium text-sm">Edit</a>
                                <form action="{{ route('kasir.destroy', $kasir) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus akun kasir ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-medium text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada akun kasir</td>
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
        </div>
    </div>
    @endsection
</x-app-layout>
