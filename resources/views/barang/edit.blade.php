<x-app-layout>
@section('header_title', 'Edit Barang Jaminan')
@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Edit Barang Jaminan</h2>
        <p class="text-sm text-slate-500 mt-1">Perbarui data barang jaminan sesuai kebutuhan.</p>
    </div>
    <form action="{{ route('barang.update', $barang) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LEFT --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Identitas Barang</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">ID Barang Jaminan</label>
                        <input type="text" value="BRG-{{ str_pad($barang->id, 5, '0', STR_PAD_LEFT) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 font-mono text-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Nasabah <span class="text-rose-400">*</span></label>
                        <select name="nasabah_id" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}" {{ $barang->nasabah_id == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Jenis Barang <span class="text-rose-400">*</span></label>
                        @php $kat = old('kategori', $barang->kategori); @endphp
                        <select name="kategori" id="kategori_select" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="elektronik" {{ $kat == 'elektronik' ? 'selected' : '' }} class="bg-white">Elektronik</option>
                            <option value="emas" {{ $kat == 'emas' ? 'selected' : '' }} class="bg-white">Emas</option>
                            <option value="kendaraan" {{ $kat == 'kendaraan' ? 'selected' : '' }} class="bg-white">Kendaraan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Merk / Type Barang <span class="text-rose-400">*</span></label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Merk / Type (Detail)</label>
                        <input type="text" name="merk_type" value="{{ old('merk_type', $barang->merk_type) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1" id="label_nomor_seri">Nomor Seri / Rangka / Polisi</label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri', $barang->nomor_seri) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Spesifikasi</label>
                        <textarea name="spesifikasi" rows="2" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('spesifikasi', $barang->spesifikasi) }}</textarea>
                    </div>
                </div>
            </div>
            {{-- RIGHT --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Kelengkapan & Kondisi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-2">Kelengkapan Barang</label>
                            <div id="kelengkapan_container" class="space-y-2"></div>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="kelengkapan_lainnya" class="flex-1 bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Lainnya...">
                                <button type="button" onclick="addKelengkapan()" class="bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs text-sky-400 hover:bg-white/10">+ Tambah</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Kondisi Fisik Barang <span class="text-rose-400">*</span></label>
                            <textarea name="kondisi_fisik" rows="3" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>{{ old('kondisi_fisik', $barang->kondisi_fisik) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Perkiraan Nilai Taksiran (Rp) <span class="text-rose-400">*</span></label>
                            <input type="text" name="taksiran" id="input_taksiran" value="{{ old('taksiran', number_format($barang->taksiran, 0, '', '')) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500 font-bold text-lg" required>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Foto Barang</h3>
                    @php
                        $foto1 = $barang->fotoBarang->where('keterangan','Foto 1')->first();
                        $foto2 = $barang->fotoBarang->where('keterangan','Foto 2')->first();
                        $foto3 = $barang->fotoBarang->where('keterangan','Foto 3')->first();
                    @endphp
                    <div class="grid grid-cols-3 gap-4">
                        @foreach([['foto_1',$foto1,'Foto 1'],['foto_2',$foto2,'Foto 2'],['foto_3',$foto3,'Foto 3']] as [$key,$foto,$label])
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ $label }}</label>
                            @if($foto)<div class="mb-2"><img src="{{ asset('storage/'.$foto->foto_path) }}" class="w-full h-16 object-cover rounded-lg border border-slate-300"></div>@endif
                            <input type="file" name="{{ $key }}" accept="image/jpeg,image/png" class="w-full text-slate-500 text-xs file:mr-2 file:py-1.5 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400">
                            @error($key) <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">Kosongkan jika tidak ingin mengubah foto. JPG/JPEG/PNG maks 500KB per file.</p>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('barang.index') }}" class="bg-white border border-slate-200 px-6 py-3 rounded-xl text-sm text-slate-600 hover:bg-white/10 transition font-medium">Batal</a>
            <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-8 py-3 rounded-xl text-sm font-semibold">Perbarui Barang Jaminan</button>
        </div>
    </form>
</div>
<script>
const kelengkapanMap = {elektronik:['Nota Pembelian','Box','Surat Garansi','Charger'],emas:['Nota Pembelian','Certieye/Sertifikat'],kendaraan:['STNK','BPKB']};
const checkedItems = new Set({!! json_encode(old('kelengkapan', $barang->kelengkapan ?? [])) !!});
function renderKelengkapan(kategori){const c=document.getElementById('kelengkapan_container');c.innerHTML='';const items=kelengkapanMap[kategori]||[];items.forEach(i=>{c.insertAdjacentHTML('beforeend',makeCheckbox(i,checkedItems.has(i)?'checked':''))});checkedItems.forEach(i=>{if(!items.includes(i))c.insertAdjacentHTML('beforeend',makeCheckbox(i,'checked'))});}
function makeCheckbox(l,ch){return `<label class="flex items-center gap-2 cursor-pointer select-none"><input type="checkbox" name="kelengkapan[]" value="${l}" ${ch} class="w-3.5 h-3.5 rounded border-white/20 bg-white text-sky-500 focus:ring-sky-500"><span class="text-sm text-slate-600">${l}</span></label>`;}
function addKelengkapan(){const i=document.getElementById('kelengkapan_lainnya');const v=i.value.trim();if(!v)return;document.getElementById('kelengkapan_container').insertAdjacentHTML('beforeend',makeCheckbox(v,'checked'));i.value='';}
document.getElementById('kategori_select').addEventListener('change',function(){renderKelengkapan(this.value);});
document.addEventListener('DOMContentLoaded',()=>{const k=document.getElementById('kategori_select').value;if(k)renderKelengkapan(k);const inp=document.getElementById('input_taksiran');let v=inp.value.replace(/\D/g,'');if(v)inp.value=parseInt(v).toLocaleString('id-ID');inp.addEventListener('input',function(){let v=this.value.replace(/\D/g,'');this.value=v?parseInt(v).toLocaleString('id-ID'):'';});inp.closest('form').addEventListener('submit',function(){inp.value=inp.value.replace(/\D/g,'');});});
</script>
@endsection
</x-app-layout>
