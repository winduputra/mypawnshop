<x-app-layout>
    @section('header_title', 'Manajemen User')
    @section('content')
    <div class="max-w-6xl mx-auto">
        @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">{{ session('success') }}</div>@endif
        <form method="GET" class="mb-6 flex flex-wrap gap-3 items-center">
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." class="bg-white border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800">
            <select name="role" class="bg-white border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800">
                <option value="">Semua Role</option>
                @foreach($roles as $role)<option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>@endforeach
            </select>
            <button class="bg-[#084C35] text-[#D6A639] font-semibold rounded-xl px-4 py-2 text-sm">Filter</button>
            <a href="{{ route('users.create') }}" class="ml-auto bg-[#cf9e50] text-white font-semibold rounded-xl px-4 py-2 text-sm">Tambah User</a>
        </form>
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead><tr class="text-xs uppercase text-slate-500 bg-white"><th class="px-6 py-4">Nama</th><th class="px-6 py-4">Email</th><th class="px-6 py-4">Role</th><th class="px-6 py-4">Cabang</th><th class="px-6 py-4 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $item)
                    <tr><td class="px-6 py-4 font-medium">{{ $item->name }}</td><td class="px-6 py-4 text-slate-500">{{ $item->email }}</td><td class="px-6 py-4">{{ ucfirst($item->role) }}</td><td class="px-6 py-4 text-slate-500">{{ $item->cabang->nama_cabang ?? '-' }}</td><td class="px-6 py-4 text-right space-x-2"><a href="{{ route('users.edit', $item) }}" class="text-sky-500">Edit</a><form action="{{ route('users.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="text-rose-500">Hapus</button></form></td></tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-200">{{ $users->links() }}</div>
        </div>
    </div>
    @endsection
</x-app-layout>
