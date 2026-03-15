<x-app-layout>
    @section('header_title', 'Edit Barang Jaminan')

    @section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-white mb-6">Edit Data Barang Jaminan</h3>

            <form action="{{ route('barang.update', $barang) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Nasabah</label>
                        <select name="nasabah_id" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            @foreach($nasabahs as $nasabah)
                                <option value="{{ $nasabah->id }}" {{ $barang->nasabah_id == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Kategori</label>
                        <select name="kategori" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="emas" {{ $barang->kategori == 'emas' ? 'selected' : '' }}>Emas</option>
                            <option value="elektronik" {{ $barang->kategori == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                            <option value="kendaraan" {{ $barang->kategori == 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Berat / Satuan</label>
                        <input type="number" step="0.01" name="berat" value="{{ old('berat', $barang->berat) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nilai Taksiran (Rp)</label>
                        <input type="number" name="taksiran" value="{{ old('taksiran', $barang->taksiran) }}" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500 text-xl font-bold" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Deskripsi / Kondisi Barang</label>
                        <textarea name="deskripsi" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-gradient w-full py-4 text-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection
</x-app-layout>
