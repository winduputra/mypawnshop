<x-app-layout>
    @section('header_title', 'Buat Transaksi Rahn')

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control { background: rgba(30, 41, 59, 0.6) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; border-radius: 0.75rem !important; color: white !important; padding: 0.75rem 1rem !important; }
        .ts-dropdown { background: #1e293b !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: white !important; border-radius: 0.75rem !important; margin-top: 5px !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important; }
        .ts-dropdown .option { padding: 10px 15px !important; }
        .ts-dropdown .active { background: #38bdf8 !important; color: white !important; }
        .ts-dropdown .create { display: none !important; }
        .ts-control input { color: white !important; }
        .ts-wrapper.single .ts-control { padding-right: 2rem !important; }
        .ts-wrapper.single .ts-control::after { border-color: #94a3b8 transparent transparent transparent !important; right: 15px !important; }
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
                                <input type="number" name="tenor_hari" id="tenorSelect" value="30" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-slate-400 cursor-not-allowed text-center font-bold" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Biaya Admin (Rp)</label>
                                <input type="text" name="biaya_admin" id="biayaAdminInput" value="{{ $settings['biaya_admin'] ?? 10000 }}" class="currency-input w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Metode Pembayaran Biaya</label>
                                <select name="metode_pembayaran" id="metodePembayaran" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                                    <option class="text-slate-900" value="potong_pinjaman">Dipotong dari Pinjaman</option>
                                    <option class="text-slate-900" value="bayar_dimuka">Dibayar di Awal (Cash)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 mt-1">Biaya admin + penitipan akan dipotong atau dibayar terpisah</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Dashboard -->
                <div class="space-y-6">
                    <div class="glass-card p-6 border-t-4 border-sky-500 sticky top-8">
                        <h3 class="text-lg font-semibold text-white mb-6">Ringkasan Pinjaman</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Total Taksiran</span>
                                <span class="text-sm text-white font-mono" id="sumTaksiran">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Pinjaman Pokok</span>
                                <span class="text-sm text-emerald-400 font-mono font-bold" id="sumPinjaman">Rp 0</span>
                            </div>
                            <div class="flex justify-between border-t border-white/5 pt-4">
                                <span class="text-sm text-slate-400">Penitipan / 30 Hari</span>
                                <span class="text-sm text-indigo-400 font-mono" id="sumUjrah">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Total Penitipan (Tenor)</span>
                                <span class="text-sm text-indigo-400 font-mono" id="sumTotalUjrah">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Biaya Admin</span>
                                <span class="text-sm text-white font-mono" id="sumBiayaAdmin">Rp 0</span>
                            </div>
                            <div class="flex justify-between border-t border-white/5 pt-4">
                                <span class="text-sm text-slate-400">Total Biaya</span>
                                <span class="text-sm text-rose-400 font-mono" id="sumTotalBiaya">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400">Metode</span>
                                <span class="text-xs text-amber-400 font-medium" id="sumMetode">Potong Pinjaman</span>
                            </div>
                            <div class="flex justify-between border-t-2 border-sky-500/30 pt-4">
                                <span class="text-sm text-white font-bold">Diterima Nasabah</span>
                                <span class="text-lg text-emerald-400 font-mono font-bold" id="sumDiterima">Rp 0</span>
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
        const SETTINGS = @json($settings);
        const UJRAH = {
            emas: parseFloat(SETTINGS.ujrah_emas || 50000),
            elektronik: parseFloat(SETTINGS.ujrah_elektronik || 75000),
            kendaraan: parseFloat(SETTINGS.ujrah_kendaraan || 100000)
        };
        const RATES = {
            emas: parseFloat(SETTINGS.persentase_emas || 85) / 100,
            elektronik: parseFloat(SETTINGS.persentase_elektronik || 70) / 100,
            kendaraan: parseFloat(SETTINGS.persentase_kendaraan || 75) / 100
        };

        const fmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

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
                const rate = RATES[barang.kategori] || 0.75;
                const maxPinjaman = barang.taksiran * rate;
                
                const div = document.createElement('div');
                div.className = 'p-4 glass bg-white/5 border border-white/5 rounded-xl hover:bg-white/10 transition-all';
                div.innerHTML = `
                    <div class="flex items-center cursor-pointer" onclick="toggleBarang(this)">
                        <input type="radio" name="barang_id" value="${barang.id}" 
                               data-taksiran="${barang.taksiran}" data-kategori="${barang.kategori}"
                               data-max-pinjaman="${maxPinjaman}"
                               class="barang-radio w-5 h-5 border-white/10 bg-slate-800 text-sky-500 focus:ring-sky-500 mr-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white">${barang.nama_barang}</p>
                            <p class="text-xs text-slate-500 uppercase">${barang.kategori} · Taksiran: ${fmt.format(barang.taksiran)} · Penitipan: ${fmt.format(UJRAH[barang.kategori] || 50000)}/30hr</p>
                        </div>
                    </div>
                    <div class="pinjaman-input-wrapper mt-3 hidden">
                        <label class="block text-xs text-slate-400 mb-1">Jumlah Pinjaman (Max: ${fmt.format(maxPinjaman)})</label>
                        <input type="text" name="pinjaman_items[${barang.id}]" 
                               value="${maxPinjaman}" 
                               data-max="${maxPinjaman}"
                               class="currency-input w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-sky-500 focus:ring-sky-500"
                               oninput="validateAndSummary(this, ${maxPinjaman})">
                        <div class="flex justify-between mt-1">
                            <p class="text-[10px] text-slate-500">Min: Rp 0</p>
                            <p class="text-[10px] text-slate-500">Max: ${fmt.format(maxPinjaman)}</p>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });

            // Initialize formatting for the newly added inputs
            if (typeof initCurrencyInputs === 'function') {
                initCurrencyInputs();
            }
        });

        function toggleBarang(el) {
            const cb = el.querySelector('input[type="radio"]');
            if (event.target !== cb) cb.checked = true;
            
            // Hide all and show selected
            document.querySelectorAll('.pinjaman-input-wrapper').forEach(w => w.classList.add('hidden'));
            const wrapper = el.parentElement.querySelector('.pinjaman-input-wrapper');
            if (wrapper) wrapper.classList.remove('hidden');
            
            updateSummary();
        }

        // Attach change listener to body for radio buttons to catch native clicks
        document.body.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('barang-radio')) {
                document.querySelectorAll('.pinjaman-input-wrapper').forEach(w => w.classList.add('hidden'));
                const wrapper = e.target.closest('.p-4').querySelector('.pinjaman-input-wrapper');
                if (wrapper) wrapper.classList.remove('hidden');
                updateSummary();
            }
        });

        // Delay validation slightly to let currency-formatter update the hidden input
        function validateAndSummary(input, max) {
            setTimeout(() => {
                const hiddenName = input.getAttribute('data-currency-for');
                const hiddenInput = document.querySelector(`input[name="${hiddenName}"]`);
                if (hiddenInput) {
                    let val = parseFloat(hiddenInput.value) || 0;
                    if (val > max) {
                        hiddenInput.value = max;
                        input.value = formatCurrency(max);
                    } else if (val < 0) {
                        hiddenInput.value = 0;
                        input.value = formatCurrency(0);
                    }
                }
                updateSummary();
            }, 50);
        }

        document.getElementById('tenorSelect').addEventListener('change', updateSummary);
        document.getElementById('metodePembayaran').addEventListener('change', updateSummary);

        // Listen to hidden input changes from currency formatter
        const biayaObs = new MutationObserver(updateSummary);
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const hiddenAdmin = document.querySelector('input[name="biaya_admin"][type="hidden"]');
                if (hiddenAdmin) {
                    hiddenAdmin.addEventListener('change', updateSummary);
                    // Also observe via input on display field
                    const displayAdmin = document.querySelector('[data-currency-for="biaya_admin"]');
                    if (displayAdmin) displayAdmin.addEventListener('input', function() { setTimeout(updateSummary, 50); });
                }
                updateSummary();
            }, 200);
        });

        function updateSummary() {
            const selected = document.querySelectorAll('.barang-radio:checked');
            let totalTaksiran = 0, totalPinjaman = 0, totalUjrah30 = 0;

            selected.forEach(cb => {
                const taksiran = parseFloat(cb.getAttribute('data-taksiran'));
                const kategori = cb.getAttribute('data-kategori');
                const maxP = parseFloat(cb.getAttribute('data-max-pinjaman'));
                
                // Get the corresponding hidden input for the pinjaman
                const barangId = cb.value;
                const hiddenInput = document.querySelector(`input[name="pinjaman_items[${barangId}]"]`);
                const pinjaman = hiddenInput ? (parseFloat(hiddenInput.value) || 0) : maxP;

                totalTaksiran += taksiran;
                totalPinjaman += pinjaman;
                totalUjrah30 += UJRAH[kategori] || 50000;
            });

            const tenor = parseInt(document.getElementById('tenorSelect').value) || 30;
            const metode = document.getElementById('metodePembayaran').value;

            // Get biaya admin from hidden input
            const hiddenAdmin = document.querySelector('input[name="biaya_admin"][type="hidden"]');
            const biayaAdmin = hiddenAdmin ? parseFloat(hiddenAdmin.value) || 0 : 0;

            const totalUjrahTenor = totalUjrah30 * (tenor / 30);
            const totalBiaya = biayaAdmin + totalUjrahTenor;

            let diterima;
            if (metode === 'potong_pinjaman') {
                diterima = totalPinjaman - totalBiaya;
            } else {
                diterima = totalPinjaman; // Biaya dibayar terpisah
            }

            document.getElementById('sumTaksiran').innerText = fmt.format(totalTaksiran);
            document.getElementById('sumPinjaman').innerText = fmt.format(totalPinjaman);
            document.getElementById('sumUjrah').innerText = fmt.format(totalUjrah30);
            document.getElementById('sumTotalUjrah').innerText = fmt.format(totalUjrahTenor) + ' (' + tenor + 'hr)';
            document.getElementById('sumBiayaAdmin').innerText = fmt.format(biayaAdmin);
            document.getElementById('sumTotalBiaya').innerText = fmt.format(totalBiaya);
            document.getElementById('sumMetode').innerText = metode === 'potong_pinjaman' ? 'Potong Pinjaman' : 'Bayar di Awal';
            document.getElementById('sumDiterima').innerText = fmt.format(Math.max(diterima, 0));
        }
    </script>
    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        var ts = new TomSelect("#nasabahSelect", {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: "-- Pilih Nasabah --",
            allowEmptyOption: true,
        });
        ts.on('change', function() {
            document.getElementById('nasabahSelect').dispatchEvent(new Event('change'));
        });
    </script>
    @endpush
</x-app-layout>
