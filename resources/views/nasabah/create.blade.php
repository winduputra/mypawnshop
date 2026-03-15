<x-app-layout>
    @section('header_title', 'Tambah Nasabah')

    @section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('nasabah.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-white mb-6">Formulir Nasabah Baru</h3>

            <form action="{{ route('nasabah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">NIK (Sesuai KTP)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('nik') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Telepon / WhatsApp</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    @error('telepon') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Foto KTP</label>
                    <input type="file" name="foto_ktp" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                    <p class="text-xs text-slate-500 mt-2">Maksimal 2MB (JPG/PNG)</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-gradient w-full py-4 text-lg">
                        Simpan Data Nasabah
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection
</x-app-layout>
