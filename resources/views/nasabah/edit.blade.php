<x-app-layout>
@section('header_title', 'Edit Nasabah')
@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('nasabah.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Edit Data Nasabah</h2>
        <p class="text-sm text-slate-500 mt-1">Perbarui data nasabah sesuai kebutuhan.</p>
    </div>
    <form action="{{ route('nasabah.update', $nasabah) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LEFT: Data Pribadi --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-amber-400 mb-5">Data Pribadi</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">ID Nasabah</label>
                        <input type="text" value="NSB-{{ str_pad($nasabah->id, 5, '0', STR_PAD_LEFT) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 font-mono text-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $nasabah->nama) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">NIK (16 Digit) <span class="text-rose-400">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik', $nasabah->nik) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required maxlength="16">
                        @error('nik') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Pekerjaan <span class="text-rose-400">*</span></label>
                            <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $nasabah->pekerjaan) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Nama Ibu Kandung <span class="text-rose-400">*</span></label>
                            <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung', $nasabah->nama_ibu_kandung) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Status Pernikahan <span class="text-rose-400">*</span></label>
                        @php $sp = old('status_pernikahan', $nasabah->status_pernikahan); @endphp
                        <select name="status_pernikahan" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="" disabled {{ !$sp ? 'selected' : '' }} class="bg-white">Pilih...</option>
                            <option value="Menikah" {{ $sp == 'Menikah' ? 'selected' : '' }} class="bg-white">Menikah</option>
                            <option value="Belum Menikah" {{ $sp == 'Belum Menikah' ? 'selected' : '' }} class="bg-white">Belum Menikah</option>
                            <option value="Duda/Janda" {{ $sp == 'Duda/Janda' ? 'selected' : '' }} class="bg-white">Duda/Janda</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Foto KTP</label>
                        @if($nasabah->foto_ktp)
                        <div class="mb-2"><img src="{{ asset('storage/' . $nasabah->foto_ktp) }}" alt="KTP" class="w-28 rounded-lg border border-slate-300"></div>
                        @endif
                        <input type="file" name="foto_ktp" accept="image/jpeg,image/png" class="w-full text-slate-500 text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                        <p class="text-[10px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah. JPG/JPEG/PNG maks 500KB.</p>
                        @error('foto_ktp') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            {{-- RIGHT: Kontak & Alamat + Rekening --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Kontak & Alamat</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">No. HP <span class="text-rose-400">*</span></label>
                                <input type="text" name="telepon" value="{{ old('telepon', $nasabah->telepon) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                @error('telepon') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">No. WA Aktif</label>
                                <input type="text" name="no_wa" value="{{ old('no_wa', $nasabah->no_wa) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Email Aktif <span class="text-rose-400">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $nasabah->email) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Alamat Lengkap Sesuai KTP <span class="text-rose-400">*</span></label>
                            <textarea name="alamat" id="alamat_ktp" rows="2" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-medium text-slate-500">Alamat Domisili Sekarang</label>
                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                    <input type="checkbox" id="sama_ktp" class="w-3.5 h-3.5 rounded border-white/20 bg-white text-sky-500 focus:ring-sky-500">
                                    <span class="text-[11px] text-sky-400">Sama dengan KTP</span>
                                </label>
                            </div>
                            <textarea name="alamat_domisili" id="alamat_domisili" rows="2" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('alamat_domisili', $nasabah->alamat_domisili) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-amber-400 mb-5">Informasi Rekening</h3>
                    <div class="space-y-4">
                        @php
                            $banks = ['Mandiri','BCA','BRI','BNI','BTN','BSI','Permata','CIMB Niaga','Bank Lampung'];
                            $currentBank = old('nama_bank', $nasabah->nama_bank);
                            $isLainnya = $currentBank && !in_array($currentBank, $banks);
                        @endphp
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Nama Bank <span class="text-rose-400">*</span></label>
                            <select id="select_bank" name="nama_bank" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required onchange="checkBank(this.value)">
                                <option value="" disabled class="bg-white">Pilih Bank...</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank }}" {{ $currentBank == $bank ? 'selected' : '' }} class="bg-white">{{ $bank }}</option>
                                @endforeach
                                <option value="Lainnya" {{ $isLainnya ? 'selected' : '' }} class="bg-white">Lainnya</option>
                            </select>
                        </div>
                        <div id="manual_bank_container" class="{{ $isLainnya ? '' : 'hidden' }}">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Nama Bank (Manual) <span class="text-rose-400">*</span></label>
                            <input type="text" id="manual_bank" name="manual_bank" value="{{ $isLainnya ? $currentBank : '' }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nomor Rekening <span class="text-rose-400">*</span></label>
                                <input type="text" name="no_rekening" value="{{ old('no_rekening', $nasabah->no_rekening) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nama Pemilik <span class="text-rose-400">*</span></label>
                                <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $nasabah->nama_pemilik_rekening) }}" class="w-full bg-white border border-slate-200 bg-white border-slate-300 rounded-lg px-3 py-2.5 text-slate-800 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('nasabah.index') }}" class="bg-white border border-slate-200 px-6 py-3 rounded-xl text-sm text-slate-600 hover:bg-white/10 transition font-medium">Batal</a>
            <button type="submit" class="bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all px-8 py-3 rounded-xl text-sm font-semibold">Perbarui Nasabah</button>
        </div>
    </form>
</div>
<script>
function checkBank(v){const c=document.getElementById('manual_bank_container'),m=document.getElementById('manual_bank'),s=document.getElementById('select_bank');if(v==='Lainnya'){c.classList.remove('hidden');m.required=true;m.name='nama_bank';s.name='_ignore';}else{c.classList.add('hidden');m.required=false;m.name='manual_bank';s.name='nama_bank';}}
document.addEventListener('DOMContentLoaded',()=>{const sv=document.getElementById('select_bank').value;if(sv==='Lainnya')checkBank('Lainnya');const cb=document.getElementById('sama_ktp'),k=document.getElementById('alamat_ktp'),d=document.getElementById('alamat_domisili');function sync(){if(cb.checked){d.value=k.value;d.readOnly=true;d.classList.add('opacity-50');}else{d.readOnly=false;d.classList.remove('opacity-50');}}cb.addEventListener('change',sync);k.addEventListener('input',()=>{if(cb.checked)d.value=k.value;});});
</script>
@endsection
</x-app-layout>
