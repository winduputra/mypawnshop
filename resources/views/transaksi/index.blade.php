<x-app-layout>
    @section('header_title', 'Transaksi Rahn')

    @section('content')
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-white">Daftar Gadai</h3>
        <a href="{{ route('transaksi.create') }}" class="btn-gradient">
            Transaksi Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs text-slate-500 mb-1 uppercase font-semibold">Cari Nama / No. Kontrak</label>
                <input type="text" id="filterSearch" placeholder="Ketik untuk mencari..."
                    class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1 uppercase font-semibold">Status</label>
                <select id="filterStatus" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="diperpanjang">Diperpanjang</option>
                    <option value="lunas">Lunas</option>
                    <option value="lelang">Lelang</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1 uppercase font-semibold">Jatuh Tempo</label>
                <select id="filterJatuhTempo" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="">Semua</option>
                    <option value="segera">Segera (≤ 7 hari)</option>
                    <option value="lewat">Sudah Lewat</option>
                </select>
            </div>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto" id="tableContainer">
            @include('transaksi._table')
        </div>
    </div>

    <script>
        let debounceTimer;
        const filterSearch = document.getElementById('filterSearch');
        const filterStatus = document.getElementById('filterStatus');
        const filterJatuhTempo = document.getElementById('filterJatuhTempo');

        function fetchFiltered() {
            const params = new URLSearchParams();
            if (filterSearch.value) params.set('search', filterSearch.value);
            if (filterStatus.value) params.set('status', filterStatus.value);
            if (filterJatuhTempo.value) params.set('jatuh_tempo', filterJatuhTempo.value);

            fetch(`{{ route('transaksi.index') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('tableContainer').innerHTML = html;
            });
        }

        filterSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFiltered, 300);
        });

        filterStatus.addEventListener('change', fetchFiltered);
        filterJatuhTempo.addEventListener('change', fetchFiltered);
    </script>
    @endsection
</x-app-layout>
