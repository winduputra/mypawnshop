<x-app-layout>
    @section('header_title', 'Kelola Admin')

    @section('content')
    <div class="max-w-5xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-600 text-sm">{{ session('error') }}</div>
        @endif

        <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex-1 w-full sm:max-w-sm relative">
                <input type="text" id="search-admin" placeholder="Cari nama, email, atau cabang..." class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <a href="{{ route('admin.create') }}" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all flex-shrink-0">Tambah Admin</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="admin-table-wrapper">
            <div id="admin-table-body">@include('admin._table', ['admins' => $admins])</div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search-admin');
        const tableBody = document.getElementById('admin-table-body');

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch('{{ route('admin.index') }}?search=' + encodeURIComponent(this.value), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => tableBody.innerHTML = html);
            }, 300);
        });
    </script>
    @endpush
    @endsection
</x-app-layout>
