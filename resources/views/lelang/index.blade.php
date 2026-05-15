<x-app-layout>
    @section('header_title', 'Manajemen Lelang')

    @section('content')
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-slate-800">Manajemen Lelang</h3>
        <p class="text-sm text-slate-500">Kelola proses lelang barang gadai yang melewati jatuh tempo H+8.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">{{ session('error') }}</div>
    @endif

    {{-- Status Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @php $tabs = ['semua'=>'Semua','baru'=>'Baru (H+8)','pending'=>'Pending','aktif'=>'Aktif','terjual'=>'Terjual','dibatalkan'=>'Dibatalkan']; @endphp
        @foreach($tabs as $key => $label)
        <a href="{{ route('lelang.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition-all border
           {{ $statusFilter === $key ? 'bg-[#084C35] text-[#D6A639] border-[#084C35]' : 'bg-white text-slate-600 border-slate-200 hover:border-[#084C35]/30 hover:text-[#084C35]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Tabel Transaksi Baru (belum punya record lelang) --}}
    @if($belumLelang->count() > 0 && in_array($statusFilter, ['semua', 'baru']))
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden text-sm mb-6">
        <div class="px-6 py-4 border-b border-slate-200 bg-amber-50">
            <h4 class="font-semibold text-amber-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Transaksi Melewati H+8 — Siap Diproses Lelang
            </h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-slate-50">
                        <th class="px-6 py-3">No. Transaksi</th>
                        <th class="px-6 py-3">Nasabah</th>
                        <th class="px-6 py-3">Cabang</th>
                        <th class="px-6 py-3">Barang</th>
                        <th class="px-6 py-3">Pokok Pinjaman</th>
                        <th class="px-6 py-3">Sisa Pokok</th>
                        <th class="px-6 py-3">Ijarah</th>
                        <th class="px-6 py-3">Tgl Batas</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($belumLelang as $trx)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sky-600 font-bold text-xs">{{ $trx->no_transaksi }}</td>
                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium">{{ $trx->nasabah->nama }}</div>
                            <div class="text-xs text-slate-400">{{ $trx->nasabah->telepon }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600">{{ $trx->nasabah->cabang->nama_cabang ?? $trx->nasabah->cabang->nama ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <ul class="text-slate-600 text-xs space-y-0.5">
                                @foreach($trx->detailTransaksi as $dt)
                                <li>• {{ $dt->barang->nama_barang }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-800">Rp {{ number_format($trx->total_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 font-mono text-rose-600 font-semibold">Rp {{ number_format($trx->sisa_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 font-mono text-slate-600">Rp {{ number_format($trx->biaya_penitipan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-rose-500 font-medium text-xs">{{ \Carbon\Carbon::parse($trx->tanggal_batas_lelang)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('lelang.show', ['lelang' => $trx->id, 'transaksi' => 1]) }}"
                               class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold {{ in_array(auth()->user()->role, ['admin','owner','superadmin']) ? 'bg-[#cf9e50] hover:bg-[#b48842] text-white' : 'bg-slate-100 text-slate-600' }} shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ in_array(auth()->user()->role, ['admin','owner','superadmin']) ? 'Proses Lelang' : 'Lihat' }}</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tabel Record Lelang --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden text-sm">
        <div class="px-6 py-4 border-b border-slate-200">
            <h4 class="font-semibold text-slate-800">Daftar Lelang</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-slate-50">
                        <th class="px-6 py-3">ID Lelang</th>
                        <th class="px-6 py-3">No. Transaksi</th>
                        <th class="px-6 py-3">Nasabah</th>
                        <th class="px-6 py-3">Cabang</th>
                        <th class="px-6 py-3">Pokok Pinjaman</th>
                        <th class="px-6 py-3">Harga Jual</th>
                        <th class="px-6 py-3">Biaya Admin</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lelangRecords as $l)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-[#084C35] font-bold text-xs">{{ $l->no_lelang }}</td>
                        <td class="px-6 py-4 font-mono text-sky-600 text-xs">{{ $l->transaksiRahn->no_transaksi }}</td>
                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium text-xs">{{ $l->transaksiRahn->nasabah->nama }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600">{{ $l->transaksiRahn->nasabah->cabang->nama_cabang ?? $l->transaksiRahn->nasabah->cabang->nama ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-slate-700 text-xs">Rp {{ number_format($l->sisa_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 font-mono text-slate-800 font-semibold text-xs">Rp {{ number_format($l->harga_lelang, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 font-mono text-slate-600 text-xs">Rp {{ number_format($l->biaya_lelang, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @php $statusColors = ['draft'=>'bg-slate-100 text-slate-600','pending'=>'bg-amber-100 text-amber-700','aktif'=>'bg-blue-100 text-blue-700','terjual'=>'bg-emerald-100 text-emerald-700','dibatalkan'=>'bg-red-100 text-red-700']; @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$l->status_lelang] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($l->status_lelang) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- View Detail --}}
                                <a href="{{ route('lelang.show', $l->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                {{-- Owner: Approve/Reject for pending --}}
                                @if($l->status_lelang === 'pending' && in_array(auth()->user()->role, ['owner','superadmin']))
                                <form action="{{ route('lelang.approve', $l->id) }}" method="POST" class="inline" onsubmit="return confirm('Setujui lelang ini?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-all" title="Approve">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approve
                                    </button>
                                </form>
                                <button onclick="showRejectModal({{ $l->id }})" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all" title="Tolak">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak
                                </button>
                                <a href="{{ route('lelang.show', $l->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#084C35] text-[#D6A639] hover:bg-[#063d2a] transition-all" title="Edit">Edit</a>
                                @endif

                                {{-- Kasir: input penjualan lelang aktif --}}
                                @if($l->status_lelang === 'aktif' && auth()->user()->role === 'kasir')
                                <button onclick="showBayarModal({{ $l->id }}, '{{ $l->no_lelang }}')" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#cf9e50] text-white hover:bg-[#b48842] transition-all">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Input Jual
                                </button>
                                @endif

                                {{-- Owner: Edit for aktif --}}
                                @if($l->status_lelang === 'aktif' && in_array(auth()->user()->role, ['owner','superadmin']))
                                <a href="{{ route('lelang.show', $l->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#084C35] text-[#D6A639] hover:bg-[#063d2a] transition-all">Edit</a>
                                @endif

                                {{-- Terjual: Lihat Nota --}}
                                @if($l->status_lelang === 'terjual')
                                <a href="{{ route('lelang.hasil', $l->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-all">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Nota
                                </a>
                                @endif

                                {{-- Dibatalkan lama: lihat saja --}}
                                @if($l->status_lelang === 'dibatalkan')
                                <a href="{{ route('lelang.show', $l->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700 hover:bg-amber-200 transition-all">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Detail
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-slate-400">Tidak ada data lelang untuk filter ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Reject --}}
    <div id="modal-reject" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tolak / Minta Revisi Lelang</h3>
            <form id="form-reject" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-1">Catatan untuk Admin</label>
                    <textarea name="catatan_owner" rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none" placeholder="Revisi harga jual / biaya admin..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition-all">Tolak Lelang</button>
                    <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')" class="flex-1 border border-slate-300 text-slate-600 py-2.5 rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Bayar --}}
    <div id="modal-bayar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Pembayaran Lelang</h3>
            <p class="text-sm text-slate-500 mb-4">ID: <span id="bayar-no-lelang" class="font-mono font-bold text-[#084C35]"></span></p>
            <form id="form-bayar" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm text-slate-600 mb-1">Nama Pembeli</label>
                    <input type="text" name="pembeli" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-1">Alamat Pembeli</label>
                    <textarea name="alamat_pembeli" rows="2" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-1">Nomor Telpon Pembeli</label>
                    <input type="text" name="telepon_pembeli" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2.5 rounded-xl transition-all">Konfirmasi Bayar</button>
                    <button type="button" onclick="document.getElementById('modal-bayar').classList.add('hidden')" class="flex-1 border border-slate-300 text-slate-600 py-2.5 rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Batalkan --}}
    <div id="modal-batalkan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Batalkan Lelang Aktif</h3>
            <form id="form-batalkan" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-1">Alasan Pembatalan</label>
                    <textarea name="catatan_owner" rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-[#084C35]/30 focus:outline-none" placeholder="Barang tidak terjual, revisi harga..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition-all">Batalkan Lelang</button>
                    <button type="button" onclick="document.getElementById('modal-batalkan').classList.add('hidden')" class="flex-1 border border-slate-300 text-slate-600 py-2.5 rounded-xl hover:bg-slate-50 transition-all">Tutup</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function showRejectModal(id) {
            document.getElementById('form-reject').action = '/lelang/' + id + '/reject';
            document.getElementById('modal-reject').classList.remove('hidden');
        }
        function showBayarModal(id, noLelang) {
            document.getElementById('form-bayar').action = '/lelang/' + id + '/bayar';
            document.getElementById('bayar-no-lelang').textContent = noLelang;
            document.getElementById('modal-bayar').classList.remove('hidden');
        }
        function showBatalkanModal(id) {
            document.getElementById('form-batalkan').action = '/lelang/' + id + '/batalkan';
            document.getElementById('modal-batalkan').classList.remove('hidden');
        }
        // Close modals on backdrop click
        ['modal-reject','modal-bayar','modal-batalkan'].forEach(function(id) {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) this.classList.add('hidden');
            });
        });
    </script>
    @endpush
    @endsection
</x-app-layout>
