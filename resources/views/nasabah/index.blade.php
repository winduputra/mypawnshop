<x-app-layout>
    @section('header_title', 'Data Nasabah')

    @section('content')
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-1/3">
            <form action="{{ route('nasabah.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari NIK, nama, telepon, atau email..." 
                       class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-2 text-white text-sm focus:border-sky-500 transition-all">
                <button type="submit" class="absolute right-3 top-2 text-slate-500 hover:text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
        <a href="{{ route('nasabah.create') }}" class="btn-gradient whitespace-nowrap">
            Tambah Nasabah
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                        <th class="px-6 py-4">NIK</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Telepon</th>
                        <th class="px-6 py-4">Alamat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="nasabah-table-body" class="divide-y divide-white/5 text-sm">
                    @include('nasabah._table')
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const tableBody = document.getElementById('nasabah-table-body');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    const query = searchInput.value;
                    
                    tableBody.style.opacity = '0.5';

                    // Update URL display without reload
                    const newUrl = query ? `{{ route('nasabah.index') }}?search=${query}` : `{{ route('nasabah.index') }}`;
                    window.history.pushState({path: newUrl}, '', newUrl);

                    fetch(newUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableBody.innerHTML = html;
                        tableBody.style.opacity = '1';
                    });
                }, 300);
            });
        });
    </script>
    @endsection
</x-app-layout>
