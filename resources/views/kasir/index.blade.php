<x-app-layout>
    @section('header_title', 'Kelola Kasir')

    @section('content')
    <div class="max-w-5xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex-1 w-full sm:max-w-sm relative">
                <input type="text" id="search-kasir" placeholder="Cari nama, email, atau cabang..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <div id="search-spinner" class="absolute right-3 top-3 hidden">
                    <svg class="w-4 h-4 text-sky-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('kasir.create') }}" class="btn-gradient flex-shrink-0 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Kasir</span>
            </a>
        </div>

        <div class="glass-card overflow-hidden" id="kasir-table-wrapper">
            <div id="kasir-table-body">
                @include('kasir._table', ['kasirs' => $kasirs])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search-kasir');
        const spinner    = document.getElementById('search-spinner');
        const tableBody  = document.getElementById('kasir-table-body');

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            spinner.classList.remove('hidden');
            searchTimeout = setTimeout(() => {
                fetch('{{ route('kasir.index') }}?search=' + encodeURIComponent(this.value), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    spinner.classList.add('hidden');
                })
                .catch(() => spinner.classList.add('hidden'));
            }, 400);
        });
    </script>
    @endpush
    @endsection
</x-app-layout>
