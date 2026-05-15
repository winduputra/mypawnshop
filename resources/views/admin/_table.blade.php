<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold text-slate-500 uppercase bg-white">
                <th class="px-6 py-4">Nama Admin</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Kantor Cabang</th>
                <th class="px-6 py-4">Dibuat</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-sm">
            @forelse($admins as $admin)
            <tr class="hover:bg-white transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">{{ $admin->name }}</td>
                <td class="px-6 py-4 text-slate-500">{{ $admin->email }}</td>
                <td class="px-6 py-4 text-slate-600">{{ $admin->cabang->nama_cabang ?? 'Semua cabang' }}</td>
                <td class="px-6 py-4 text-slate-500">{{ $admin->created_at->format('d M Y') }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.edit', $admin) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-sky-400 hover:text-sky-300 border border-sky-400/20 hover:border-sky-300/30 transition-colors">Edit</a>
                    <form action="{{ route('admin.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus akun admin ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-rose-400 hover:text-rose-300 border border-rose-400/20 hover:border-rose-300/30 transition-colors">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada admin ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($admins->hasPages())
<div class="p-6 border-t border-slate-200">{{ $admins->links() }}</div>
@endif
