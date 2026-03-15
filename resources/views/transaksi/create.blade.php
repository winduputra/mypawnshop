<x-app-layout>
    @section('header_title', 'Buat Transaksi Rahn')

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            background: rgba(30, 41, 59, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            color: white !important;
            padding: 0.75rem 1rem !important;
        }
        .ts-dropdown {
            background: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            border-radius: 0.75rem !important;
            margin-top: 5px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }
        .ts-dropdown .option {
            padding: 10px 15px !important;
        }
        .ts-dropdown .active {
            background: #38bdf8 !important;
            color: white !important;
        }
        .ts-dropdown .create {
            display: none !important;
        }
        .ts-control input {
            color: white !important;
        }
        .ts-wrapper.single .ts-control {
            padding-right: 2rem !important;
        }
        .ts-wrapper.single .ts-control::after {
            border-color: #94a3b8 transparent transparent transparent !important;
            right: 15px !important;
        }
    </style>
    @endpush

    @section('content')
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('transaksi.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <form action="{{ route('transaksi.store') }}" method="POST" id="rahnForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Form Input -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="glass-card p-8">
                        <h3 class="text-xl font-bold text-white mb-6">Data Transaksi</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Nasabah</label>
                                <select name="nasabah_id" id="nasabahSelect" class="w-full" required>
                                    <option value="">-- Pilih Nasabah --</option>
                                    @foreach($nasabahs as $nasabah)
                                        <option value="{{ $nasabah->id }}" data-barang='@json($nasabah->barang)'>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Barang Jaminan</label>
                                <div id="barangContainer" class="space-y-3">
                                    <p class="text-sm text-slate-500 italic">Pilih nasabah terlebih dahulu...</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Tanggal Transaksi</label>
                                <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Tenor (Hari)</label>
                                <select name="tenor_hari" id="tenorSelect" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                                    <option value="30" selected>30 Hari</option>
                                    <option value="60">60 Hari</option>
                                    <option value="90">90 Hari</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Biaya Admin (Rp)</label>
                                <input type="number" name="biaya_admin" value="10000" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Dashboard -->
                <div class="space-y-6">
                    <div class="glass-card p-6 border-t-4 border-sky-500">
                        <h3 class="text-lg font-semibold text-white mb-6">Ringkasan Pinjaman</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Total Taksiran</span>
                                <span class="text-sm text-white font-mono" id="sumTaksiran">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Max Marhun Bih</span>
                                <span class="text-sm text-sky-400 font-mono font-bold" id="sumPinjaman">Rp 0</span>
                            </div>
                            <div class="flex justify-between border-t border-white/5 pt-4">
                                <span class="text-sm text-slate-400">Estimasi Ujrah</span>
                                <span class="text-sm text-indigo-400 font-mono" id="sumUjrah">Rp 0 / 30hr</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="btn-gradient w-full py-4 rounded-2xl shadow-sky-500/20">
                                Proses Transaksi
                            </button>
                            <p class="text-[10px] text-slate-500 mt-4 text-center leading-tight">
                                Dengan memproses transaksi, Anda menyetujui akad Rahn syariah sesuai ketentuan MyPawnShop.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('nasabahSelect').addEventListener('change', function() {
            const container = document.getElementById('barangContainer');
            container.innerHTML = '';
            
            if (!this.value) {
                container.innerHTML = '<p class="text-sm text-slate-500 italic">Pilih nasabah terlebih dahulu...</p>';
                updateSummary();
                return;
            }

            const barangs = JSON.parse(this.options[this.selectedIndex].getAttribute('data-barang'));
            
            if (barangs.length === 0) {
                container.innerHTML = '<p class="text-sm text-rose-400">Nasabah ini belum memiliki aset terdaftar.</p>';
                updateSummary();
                return;
            }

            barangs.forEach(barang => {
                const div = document.createElement('div');
                div.className = 'flex items-center p-4 glass bg-white/5 border border-white/5 rounded-xl hover:bg-white/10 transition-all cursor-pointer';
                div.innerHTML = `
                    <input type="checkbox" name="barang_ids[]" value="${barang.id}" 
                           data-taksiran="${barang.taksiran}" data-kategori="${barang.kategori}"
                           class="w-5 h-5 rounded border-white/10 bg-slate-800 text-sky-500 focus:ring-sky-500 mr-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">${barang.nama_barang}</p>
                        <p class="text-xs text-slate-500 uppercase">${barang.kategori} - Taksiran: Rp ${new Intl.NumberFormat('id-ID').format(barang.taksiran)}</p>
                    </div>
                `;
                div.addEventListener('click', (e) => {
                    const cb = div.querySelector('input');
                    if (e.target !== cb) cb.checked = !cb.checked;
                    updateSummary();
                });
                container.appendChild(div);
            });
        });

        function updateSummary() {
            const selected = document.querySelectorAll('input[name="barang_ids[]"]:checked');
            let totalTaksiran = 0;
            let totalPinjaman = 0;
            let totalUjrah = 0;

            selected.forEach(cb => {
                const taksiran = parseFloat(cb.getAttribute('data-taksiran'));
                const kategori = cb.getAttribute('data-kategori');
                
                let pRate, uRate;
                if (kategori === 'emas') { pRate = 0.85; uRate = 0.01; }
                else if (kategori === 'elektronik') { pRate = 0.70; uRate = 0.015; }
                else { pRate = 0.75; uRate = 0.0125; }

                totalTaksiran += taksiran;
                totalPinjaman += taksiran * pRate;
                totalUjrah += taksiran * uRate;
            });

            const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
            document.getElementById('sumTaksiran').innerText = formatter.format(totalTaksiran);
            document.getElementById('sumPinjaman').innerText = formatter.format(totalPinjaman);
            document.getElementById('sumUjrah').innerText = formatter.format(totalUjrah) + ' / 30hr';
        }
    </script>
    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        var ts = new TomSelect("#nasabahSelect", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Pilih Nasabah --",
            allowEmptyOption: true,
        });

        // Trigger the original change event when Tom Select changes
        ts.on('change', function() {
            document.getElementById('nasabahSelect').dispatchEvent(new Event('change'));
        });
    </script>
    @endpush
</x-app-layout>
