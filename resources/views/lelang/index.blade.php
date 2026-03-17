<x-app-layout>
    @section('header_title', 'Manajemen Lelang')

    @section('content')
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-white">Daftar Marhun Siap & Sudah Lelang</h3>
        <p class="text-sm text-slate-500">Barang gadai yang sudah melewati batas tenggang dan masuk masa lelang.</p>
    </div>

    <div class="glass-card overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 uppercase bg-white/5">
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Barang Jaminan</th>
                        <th class="px-6 py-4">Pokok Hutang</th>
                        <th class="px-6 py-4">Tgl Batas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transactions as $trx)
                    @php $sudahLelang = $trx->status === 'lelang'; @endphp
                    <tr class="hover:bg-white/5 transition-colors {{ $sudahLelang ? 'opacity-80' : '' }}">
                        <td class="px-6 py-4 font-mono text-sky-400 font-bold uppercase">{{ $trx->no_transaksi }}</td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $trx->nasabah->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $trx->nasabah->telepon }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-slate-300">
                                @foreach($trx->detailTransaksi as $dt)
                                    <li>{{ $dt->barang->nama_barang }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-white font-semibold">Rp {{ number_format($trx->sisa_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-rose-400 font-medium">{{ \Carbon\Carbon::parse($trx->tanggal_batas_lelang)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @if($sudahLelang)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-emerald-300 bg-emerald-500/10 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                                Terlelang
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-amber-300 bg-amber-500/10 border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                Belum Lelang
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- Eye / Detail Button --}}
                                @if($sudahLelang && $trx->lelang)
                                <a href="{{ route('lelang.hasil', $trx->lelang->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 hover:text-sky-300 hover:border-sky-300/40 transition-colors"
                                   title="Lihat Detail Lelang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @else
                                <a href="{{ route('lelang.show', $trx->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-500/10 border border-slate-500/20 text-slate-400 hover:text-slate-300 hover:border-slate-300/40 transition-colors"
                                   title="Lihat Detail Transaksi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endif

                                {{-- Proses Lelang Button --}}
                                @if($sudahLelang)
                                <button type="button"
                                    onclick="showAlreadyAuctionedModal('{{ $trx->no_transaksi }}', {{ $trx->lelang->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-500/10 border border-slate-500/20 text-slate-500 cursor-not-allowed"
                                    title="Sudah Dilelang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @else
                                <a href="{{ route('lelang.show', $trx->id) }}"
                                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-medium btn-gradient"
                                   title="Proses Lelang">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Proses</span>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-slate-500">Tidak ada barang yang perlu dilelang saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    {{-- Modal: Already Auctioned --}}
    <div id="modal-already-auctioned" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
        <div class="glass-card p-8 max-w-md w-full text-center relative">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Lelang Sudah Selesai</h3>
            <p class="text-slate-400 text-sm mb-1">Nomor Gadai:</p>
            <p id="modal-no-trx" class="text-sky-400 font-mono font-bold text-base mb-4"></p>
            <p class="text-slate-400 text-sm mb-6">Lelang untuk nomor gadai ini sudah selesai dilaksanakan. Anda dapat melihat detail dan mencetak berita acara lelangnya.</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a id="modal-cetak-link" href="#"
                   class="flex-1 btn-gradient py-3 rounded-xl text-sm text-center flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Berita Acara</span>
                </a>
                <a id="modal-detail-link" href="#"
                   class="flex-1 py-3 rounded-xl text-sm text-slate-300 hover:text-white border border-white/10 hover:border-white/20 text-center transition-colors">
                    Lihat Detail
                </a>
                <button onclick="document.getElementById('modal-already-auctioned').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl text-sm text-slate-500 hover:text-slate-300 border border-white/5 hover:border-white/10 text-center transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showAlreadyAuctionedModal(noTrx, lelangId) {
            document.getElementById('modal-no-trx').textContent = noTrx;
            document.getElementById('modal-cetak-link').href = '/lelang/' + lelangId + '/cetak-pdf';
            document.getElementById('modal-detail-link').href = '/lelang/' + lelangId + '/hasil';
            document.getElementById('modal-already-auctioned').classList.remove('hidden');
        }
        // Close on backdrop click
        document.getElementById('modal-already-auctioned').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    </script>
    @endpush
    @endsection
</x-app-layout>
