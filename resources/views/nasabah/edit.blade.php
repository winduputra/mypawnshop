<x-app-layout>
    @section('header_title', 'Edit Nasabah')

    @section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('nasabah.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-white mb-6">Edit Data Nasabah</h3>

            <form action="{{ route('nasabah.update', $nasabah) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">NIK (Sesuai KTP)</label>
                    <input type="text" name="nik" value="{{ old('nik', $nasabah->nik) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('nik') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $nasabah->nama) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Email <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $nasabah->email) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required placeholder="contoh@email.com">
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Telepon / WhatsApp</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $nasabah->telepon) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('telepon') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Bank <span class="text-rose-400">*</span></label>
                        <select id="select_bank" name="nama_bank" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required onchange="checkBank(this.value)">
                            <option value="" disabled class="bg-slate-800 text-white">Pilih Bank...</option>
                            @php
                                $banks = ['Mandiri', 'BCA', 'BRI', 'BNI', 'BTN', 'BSI', 'Permata', 'CIMB Niaga', 'Bank Lampung'];
                                $currentBank = old('nama_bank', $nasabah->nama_bank);
                                $isLainnya = $currentBank && !in_array($currentBank, $banks);
                            @endphp
                            @foreach($banks as $bank)
                                <option value="{{ $bank }}" {{ $currentBank == $bank ? 'selected' : '' }} class="bg-slate-800 text-white">{{ $bank }}</option>
                            @endforeach
                            <option value="Lainnya" {{ $isLainnya ? 'selected' : '' }} class="bg-slate-800 text-white">Lainnya (Ketik Manual)</option>
                        </select>
                    </div>

                    <div id="manual_bank_container" class="{{ $isLainnya ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nama Bank (Ketik Manual) <span class="text-rose-400">*</span></label>
                        <input type="text" id="manual_bank" name="manual_bank" value="{{ $isLainnya ? $currentBank : '' }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" placeholder="Masukkan nama bank...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">No. Rekening <span class="text-rose-400">*</span></label>
                        <input type="text" name="no_rekening" value="{{ old('no_rekening', $nasabah->no_rekening) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Foto KTP (Opsional)</label>
                        @if($nasabah->foto_ktp)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $nasabah->foto_ktp) }}" alt="KTP" class="w-32 rounded-lg border border-white/10">
                            </div>
                        @endif
                        <input type="file" name="foto_ktp" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Foto Nasabah (Opsional)</label>
                        @if($nasabah->foto)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $nasabah->foto) }}" alt="Foto Nasabah" class="w-32 rounded-lg border border-white/10">
                            </div>
                        @endif
                        <input type="file" name="foto" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-gradient w-full py-4 text-lg">
                        Perbarui Data Nasabah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function checkBank(value) {
            const container = document.getElementById('manual_bank_container');
            const manualInput = document.getElementById('manual_bank');
            const selectBank = document.getElementById('select_bank');
            
            if (value === 'Lainnya') {
                container.classList.remove('hidden');
                manualInput.setAttribute('required', 'required');
                manualInput.name = 'nama_bank';
                selectBank.name = 'select_bank_ignore'; 
            } else {
                container.classList.add('hidden');
                manualInput.removeAttribute('required');
                manualInput.name = 'manual_bank';
                selectBank.name = 'nama_bank';
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const selectValue = document.getElementById('select_bank').value;
            if (selectValue === 'Lainnya') {
                checkBank('Lainnya');
            }
        });
    </script>
    @endsection
</x-app-layout>
