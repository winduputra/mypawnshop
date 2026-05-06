<x-app-layout>
@section('header_title', 'Tambah Barang Jaminan')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
.ts-control{background:rgba(30,41,59,.6)!important;border:1px solid rgba(255,255,255,.1)!important;border-radius:.75rem!important;color:#fff!important;padding:.6rem 1rem!important}
.ts-dropdown{background:#1e293b!important;border:1px solid rgba(255,255,255,.1)!important;color:#fff!important;border-radius:.75rem!important;margin-top:5px!important;box-shadow:0 10px 15px -3px rgba(0,0,0,.5)!important}
.ts-dropdown .option{padding:10px 15px!important}.ts-dropdown .active{background:#38bdf8!important;color:#fff!important}
.ts-dropdown .create{display:none!important}.ts-control input{color:#fff!important}
.ts-wrapper.single .ts-control{padding-right:2rem!important}
.ts-wrapper.single .ts-control::after{border-color:#94a3b8 transparent transparent transparent!important;right:15px!important}
</style>
@endpush
@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Registrasi Barang Jaminan</h2>
        <p class="text-sm text-slate-400 mt-1">Lengkapi data barang jaminan (Rahn) di bawah ini.</p>
    </div>
    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LEFT: Identitas Barang --}}
            <div class="glass-card p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Identitas Barang</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">ID Barang Jaminan (Otomatis)</label>
                        <input type="text" value="BRG-{{ str_pad($nextId, 5, '0', STR_PAD_LEFT) }}" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white font-mono text-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Pilih Nasabah <span class="text-rose-400">*</span></label>
                        <select name="nasabah_id" id="nasabah_id" class="w-full" required>
                            <option value="">-- Pilih Nasabah --</option>
                            @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Jenis Barang <span class="text-rose-400">*</span></label>
                        <select name="kategori" id="kategori_select" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="" disabled {{ !old('kategori') ? 'selected' : '' }} class="bg-slate-800">Pilih Jenis...</option>
                            <option value="elektronik" {{ old('kategori') == 'elektronik' ? 'selected' : '' }} class="bg-slate-800">Elektronik</option>
                            <option value="emas" {{ old('kategori') == 'emas' ? 'selected' : '' }} class="bg-slate-800">Emas</option>
                            <option value="kendaraan" {{ old('kategori') == 'kendaraan' ? 'selected' : '' }} class="bg-slate-800">Kendaraan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Merk / Type Barang <span class="text-rose-400">*</span></label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" required placeholder="Contoh: iPhone 15 Pro Max 256GB">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Merk / Type (Detail)</label>
                        <input type="text" name="merk_type" value="{{ old('merk_type') }}" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Apple / A2849">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1" id="label_nomor_seri">Nomor Seri / Rangka / Polisi</label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Masukkan nomor identitas barang">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Spesifikasi</label>
                        <textarea name="spesifikasi" rows="2" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Detail spesifikasi barang">{{ old('spesifikasi') }}</textarea>
                    </div>
                </div>
            </div>
            {{-- RIGHT: Kelengkapan, Kondisi, Taksiran, Foto --}}
            <div class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Kelengkapan & Kondisi</h3>
                    <div class="space-y-4">
                        {{-- Dynamic Kelengkapan Checkboxes --}}
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-2">Kelengkapan Barang</label>
                            <div id="kelengkapan_container" class="space-y-2">
                                {{-- Populated by JS based on kategori --}}
                            </div>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="kelengkapan_lainnya" class="flex-1 glass bg-white/5 border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Lainnya...">
                                <button type="button" onclick="addKelengkapan()" class="glass px-3 py-2 rounded-lg text-xs text-sky-400 hover:bg-white/10">+ Tambah</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Kondisi Fisik Barang <span class="text-rose-400">*</span></label>
                            <textarea name="kondisi_fisik" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500" required placeholder="Deskripsikan kondisi fisik barang secara detail...">{{ old('kondisi_fisik') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Perkiraan Nilai Taksiran (Rp) <span class="text-rose-400">*</span></label>
                            <input type="text" name="taksiran" id="input_taksiran" value="{{ old('taksiran') }}" class="w-full glass bg-white/5 border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:border-sky-500 focus:ring-sky-500 font-bold text-lg" required placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Foto Barang</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Foto 1 <span class="text-rose-400">*</span></label>
                            <input type="file" name="foto_1" class="w-full text-slate-400 text-xs file:mr-2 file:py-1.5 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Foto 2</label>
                            <input type="file" name="foto_2" class="w-full text-slate-400 text-xs file:mr-2 file:py-1.5 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Foto 3</label>
                            <input type="file" name="foto_3" class="w-full text-slate-400 text-xs file:mr-2 file:py-1.5 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">Minimal 1 foto wajib. Maks 2MB per file (JPG/PNG).</p>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('barang.index') }}" class="glass px-6 py-3 rounded-xl text-sm text-slate-300 hover:bg-white/10 transition font-medium">Batal</a>
            <button type="submit" class="btn-gradient px-8 py-3 rounded-xl text-sm font-semibold">Simpan Barang Jaminan</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
new TomSelect("#nasabah_id",{create:false,sortField:{field:"text",direction:"asc"},placeholder:"-- Pilih Nasabah --",allowEmptyOption:true});

const kelengkapanMap = {
    elektronik: ['Nota Pembelian','Box','Surat Garansi','Charger'],
    emas: ['Nota Pembelian','Certieye/Sertifikat'],
    kendaraan: ['STNK','BPKB']
};
const checkedItems = new Set({!! json_encode(old('kelengkapan', [])) !!});

function renderKelengkapan(kategori) {
    const c = document.getElementById('kelengkapan_container');
    c.innerHTML = '';
    const items = kelengkapanMap[kategori] || [];
    items.forEach(item => {
        const checked = checkedItems.has(item) ? 'checked' : '';
        c.insertAdjacentHTML('beforeend', makeCheckbox(item, checked));
    });
    // render custom items from old values
    checkedItems.forEach(item => {
        if (!items.includes(item)) {
            c.insertAdjacentHTML('beforeend', makeCheckbox(item, 'checked'));
        }
    });
}

function makeCheckbox(label, checked) {
    return `<label class="flex items-center gap-2 cursor-pointer select-none">
        <input type="checkbox" name="kelengkapan[]" value="${label}" ${checked} class="w-3.5 h-3.5 rounded border-white/20 bg-white/5 text-sky-500 focus:ring-sky-500">
        <span class="text-sm text-slate-300">${label}</span>
    </label>`;
}

function addKelengkapan() {
    const input = document.getElementById('kelengkapan_lainnya');
    const val = input.value.trim();
    if (!val) return;
    const c = document.getElementById('kelengkapan_container');
    c.insertAdjacentHTML('beforeend', makeCheckbox(val, 'checked'));
    input.value = '';
}

document.getElementById('kategori_select').addEventListener('change', function() {
    renderKelengkapan(this.value);
    const lbl = document.getElementById('label_nomor_seri');
    if (this.value === 'kendaraan') lbl.textContent = 'Nomor Rangka / Nomor Polisi';
    else if (this.value === 'emas') lbl.textContent = 'Nomor Seri / Kode';
    else lbl.textContent = 'Nomor Seri / IMEI';
});

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    const kat = document.getElementById('kategori_select').value;
    if (kat) renderKelengkapan(kat);

    // Currency format for taksiran
    const inp = document.getElementById('input_taksiran');
    inp.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        this.value = v ? parseInt(v).toLocaleString('id-ID') : '';
    });
    // On submit, clean the value
    inp.closest('form').addEventListener('submit', function() {
        inp.value = inp.value.replace(/\D/g, '');
    });
});
</script>
@endsection
</x-app-layout>
