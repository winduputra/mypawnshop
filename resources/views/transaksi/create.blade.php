<x-app-layout>
@section('header_title', 'Buat Akad Pinjaman')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
.ts-control{background:#ffffff!important;border:1px solid #cbd5e1!important;border-radius:.75rem!important;color:#1e293b!important;padding:.6rem 1rem!important}
.ts-dropdown{background:#ffffff!important;border:1px solid #cbd5e1!important;color:#1e293b!important;border-radius:.75rem!important;margin-top:5px!important;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)!important}
.ts-dropdown .option{padding:10px 15px!important}.ts-dropdown .active{background:#f1f5f9!important;color:#1e293b!important}
.ts-dropdown .create{display:none!important}.ts-control input{color:#1e293b!important}
.ts-wrapper.single .ts-control{padding-right:2rem!important}
.ts-wrapper.single .ts-control::after{border-color:#64748b transparent transparent transparent!important;right:15px!important}
</style>
@endpush
@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('transaksi.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Buat Akad Pinjaman (Rahn)</h2>
        <p class="text-sm text-slate-500 mt-1">Lengkapi data akad. Setelah disimpan, akad akan berstatus <span class="text-amber-400 font-semibold">DRAFT</span> dan harus dikirim ke admin untuk verifikasi.</p>
    </div>

    <form action="{{ route('transaksi.store') }}" method="POST" id="rahnForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT: Form --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Data Akad</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Nasabah <span class="text-rose-400">*</span></label>
                            <select name="nasabah_id" id="nasabahSelect" class="w-full" required>
                                <option value="">-- Pilih Nasabah --</option>
                                @foreach($nasabahs as $nasabah)
                                <option value="{{ $nasabah->id }}" data-barang='@json($nasabah->barang)'>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Barang Jaminan <span class="text-rose-400">*</span></label>
                            <div id="barangContainer" class="space-y-3">
                                <p class="text-sm text-slate-500 italic">Pilih nasabah terlebih dahulu...</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tenor (Hari)</label>
                            <input type="number" name="tenor_hari" id="tenorSelect" value="30" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-500 text-sm text-center font-bold cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Metode Pembayaran Biaya</label>
                            <select name="metode_pembayaran" id="metodePembayaran" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm" required>
                                <option value="potong_pinjaman" class="bg-white">Dipotong dari Pinjaman</option>
                                <option value="bayar_dimuka" class="bg-white">Dibayar di Awal (Cash)</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Biaya admin + penitipan dipotong atau dibayar terpisah.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Summary --}}
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-sky-500 sticky top-8">
                    <h3 class="text-base font-semibold text-slate-800 mb-5">Ringkasan Pinjaman</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Total Taksiran</span><span class="text-slate-800 font-mono" id="sumTaksiran">Rp 0</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Plafon Pinjaman (QARD)</span><span class="text-emerald-400 font-mono font-bold" id="sumPinjaman">Rp 0</span></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3"><span class="text-slate-500">Ijarah / 30 Hari</span><span class="text-indigo-400 font-mono" id="sumUjrah">Rp 0</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 text-xs">Ijarah = <span id="ijarahPct">{{ $settings['ijarah_persen'] ?? 2 }}</span>% × Taksiran</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Biaya Admin</span><span class="text-slate-800 font-mono" id="sumBiayaAdmin">Rp 0</span></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3"><span class="text-slate-500">Total Biaya</span><span class="text-rose-400 font-mono" id="sumTotalBiaya">Rp 0</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Metode</span><span class="text-xs text-amber-400 font-medium" id="sumMetode">Potong Pinjaman</span></div>
                        <div class="flex justify-between border-t-2 border-sky-500/30 pt-3"><span class="text-slate-800 font-bold">Diterima Nasabah</span><span class="text-lg text-emerald-400 font-mono font-bold" id="sumDiterima">Rp 0</span></div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all w-full py-3 rounded-xl text-sm font-semibold">Simpan Draft Akad</button>
                        <p class="text-[10px] text-slate-500 mt-3 text-center">Akad akan tersimpan sebagai draft. Kirim ke admin untuk verifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const SETTINGS = @json($settings);
const BIAYA_ADMIN = {
    elektronik: parseFloat(SETTINGS.biaya_admin_elektronik || 35000),
    emas: parseFloat(SETTINGS.biaya_admin_emas || 25000),
    kendaraan: parseFloat(SETTINGS.biaya_admin_kendaraan || 50000)
};
const IJARAH_PCT = parseFloat(SETTINGS.ijarah_persen || 2) / 100;
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
        updateSummary(); return;
    }
    const barangs = JSON.parse(this.options[this.selectedIndex].getAttribute('data-barang'));
    if (barangs.length === 0) {
        container.innerHTML = '<p class="text-sm text-rose-400">Nasabah ini belum memiliki aset terdaftar atau semua sedang dalam akad.</p>';
        updateSummary(); return;
    }
    barangs.forEach(b => {
        const rate = RATES[b.kategori] || 0.75;
        const maxP = b.taksiran * rate;
        const div = document.createElement('div');
        div.className = 'p-4 bg-white border border-slate-200 bg-white border border-slate-200 rounded-xl hover:bg-white/10 transition-all';
        div.innerHTML = `
            <div class="flex items-center cursor-pointer" onclick="toggleBarang(this)">
                <input type="radio" name="barang_id" value="${b.id}" data-taksiran="${b.taksiran}" data-kategori="${b.kategori}" data-max-pinjaman="${maxP}" class="barang-radio w-5 h-5 border-slate-300 bg-white text-sky-500 focus:ring-sky-500 mr-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">${b.nama_barang}</p>
                    <p class="text-xs text-slate-500 uppercase">${b.kategori} · Taksiran: ${fmt.format(b.taksiran)} · Admin: ${fmt.format(BIAYA_ADMIN[b.kategori]||35000)}</p>
                </div>
            </div>
            <div class="pinjaman-input-wrapper mt-3 hidden">
                <label class="block text-xs text-slate-500 mb-1">Jumlah Pinjaman (Max: ${fmt.format(maxP)})</label>
                <input type="text" name="pinjaman_items[${b.id}]" value="${maxP}" data-max="${maxP}" class="currency-input w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2 text-slate-800 text-sm" oninput="validateAndSummary(this, ${maxP})">
                <div class="flex justify-between mt-1"><p class="text-[10px] text-slate-500">Min: Rp 0</p><p class="text-[10px] text-slate-500">Max: ${fmt.format(maxP)}</p></div>
            </div>`;
        container.appendChild(div);
    });
    if (typeof initCurrencyInputs === 'function') initCurrencyInputs();
});

function toggleBarang(el) {
    const cb = el.querySelector('input[type="radio"]');
    if (event.target !== cb) cb.checked = true;
    document.querySelectorAll('.pinjaman-input-wrapper').forEach(w => w.classList.add('hidden'));
    const wrapper = el.parentElement.querySelector('.pinjaman-input-wrapper');
    if (wrapper) wrapper.classList.remove('hidden');
    updateSummary();
}
document.body.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('barang-radio')) {
        document.querySelectorAll('.pinjaman-input-wrapper').forEach(w => w.classList.add('hidden'));
        const wrapper = e.target.closest('.p-4').querySelector('.pinjaman-input-wrapper');
        if (wrapper) wrapper.classList.remove('hidden');
        updateSummary();
    }
});
function validateAndSummary(input, max) {
    setTimeout(() => {
        const hName = input.getAttribute('data-currency-for');
        const hInput = document.querySelector(`input[name="${hName}"]`);
        if (hInput) {
            let val = parseFloat(hInput.value) || 0;
            if (val > max) { hInput.value = max; input.value = formatCurrency(max); }
            else if (val < 0) { hInput.value = 0; input.value = formatCurrency(0); }
        }
        updateSummary();
    }, 50);
}
document.getElementById('metodePembayaran').addEventListener('change', updateSummary);
document.addEventListener('DOMContentLoaded', function() { setTimeout(updateSummary, 200); });

function updateSummary() {
    const selected = document.querySelectorAll('.barang-radio:checked');
    let totalTaksiran = 0, totalPinjaman = 0, totalUjrah30 = 0, totalBiayaAdmin = 0;
    selected.forEach(cb => {
        const taksiran = parseFloat(cb.getAttribute('data-taksiran'));
        const kategori = cb.getAttribute('data-kategori');
        const maxP = parseFloat(cb.getAttribute('data-max-pinjaman'));
        const barangId = cb.value;
        const hiddenInput = document.querySelector(`input[name="pinjaman_items[${barangId}]"]`);
        const pinjaman = hiddenInput ? (parseFloat(hiddenInput.value) || 0) : maxP;
        totalTaksiran += taksiran;
        totalPinjaman += pinjaman;
        totalUjrah30 += taksiran * IJARAH_PCT;
        totalBiayaAdmin += BIAYA_ADMIN[kategori] || 35000;
    });
    const metode = document.getElementById('metodePembayaran').value;
    const totalBiaya = totalBiayaAdmin + totalUjrah30;
    let diterima = metode === 'potong_pinjaman' ? totalPinjaman - totalBiaya : totalPinjaman;
    const metodeLabels = {
        potong_pinjaman: 'Potong Pinjaman',
        bayar_dimuka: 'Bayar di Awal'
    };
    document.getElementById('sumTaksiran').innerText = fmt.format(totalTaksiran);
    document.getElementById('sumPinjaman').innerText = fmt.format(totalPinjaman);
    document.getElementById('sumUjrah').innerText = fmt.format(totalUjrah30);
    document.getElementById('sumBiayaAdmin').innerText = fmt.format(totalBiayaAdmin);
    document.getElementById('sumTotalBiaya').innerText = fmt.format(totalBiaya);
    document.getElementById('sumMetode').innerText = metodeLabels[metode] || '-';
    document.getElementById('sumDiterima').innerText = fmt.format(Math.max(diterima, 0));
}
</script>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
var ts = new TomSelect("#nasabahSelect",{create:false,sortField:{field:"text",direction:"asc"},placeholder:"-- Pilih Nasabah --",allowEmptyOption:true});
ts.on('change',function(){document.getElementById('nasabahSelect').dispatchEvent(new Event('change'));});
</script>
@endpush
</x-app-layout>
