<x-app-layout>
    @section('header_title', 'Barang Jaminan')

    @section('content')
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-2/3">
            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari nama barang, taksiran, atau nasabah..." 
                       class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2 text-slate-800 text-sm focus:border-sky-500 transition-all">
                @if(in_array(auth()->user()->role, ['admin', 'owner', 'superadmin', 'superuser']))
                <select name="cabang_id" class="w-full md:w-56 bg-white border border-slate-300 rounded-xl px-4 py-2 text-slate-800 text-sm focus:border-sky-500 transition-all">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}" {{ (string) $cabangId === (string) $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>
                @endif
                <button type="submit" class="bg-[#084C35] text-[#D6A639] font-semibold rounded-xl px-4 py-2 text-sm">Filter</button>
            </form>
        </div>
        <a href="{{ route('barang.create') }}" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all">
            Tambah Barang
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white">
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Pemilik (Nasabah)</th>
                        <th class="px-6 py-4">Cabang</th>
                        <th class="px-6 py-4">Taksiran</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="barang-table-body" class="divide-y divide-slate-200 text-sm">
                    @include('barang._table')
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const cabangSelect = document.querySelector('select[name="cabang_id"]');
            const tableBody = document.getElementById('barang-table-body');
            let timeout = null;

            function reloadTable() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    const params = new URLSearchParams();
                    if (searchInput.value) params.set('search', searchInput.value);
                    if (cabangSelect && cabangSelect.value) params.set('cabang_id', cabangSelect.value);
                    const query = params.toString();
                    
                    tableBody.style.opacity = '0.5';

                    // Update URL display without reload
                    const newUrl = query ? `{{ route('barang.index') }}?${query}` : `{{ route('barang.index') }}`;
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
            }

            searchInput.addEventListener('input', reloadTable);
            if (cabangSelect) cabangSelect.addEventListener('change', reloadTable);
        });
    </script>
    @endsection
</x-app-layout>
