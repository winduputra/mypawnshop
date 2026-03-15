<x-app-layout>
    @section('header_title', 'Tambah Barang Jaminan')

    @section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('barang.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-white mb-6">Identitas Barang Jaminan</h3>

            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Nasabah</label>
                        <select name="nasabah_id" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="">-- Pilih Nasabah --</option>
                            @foreach($nasabahs as $nasabah)
                                <option value="{{ $nasabah->id }}">{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Honda Vario 150 2022" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Kategori</label>
                        <select name="kategori" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="emas">Emas</option>
                            <option value="elektronik">Elektronik</option>
                            <option value="kendaraan">Kendaraan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Berat / Satuan</label>
                        <input type="number" step="0.01" name="berat" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" placeholder="0.00">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nilai Taksiran (Rp)</label>
                        <input type="number" name="taksiran" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500 text-xl font-bold" required>
                        <p class="text-xs text-slate-500 mt-2 italic">Nilai pasar wajar saat ini untuk barang tersebut.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Deskripsi / Kondisi Barang</label>
                        <textarea name="deskripsi" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" placeholder="Detail kondisi, kelengkapan, dll."></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Foto Barang</label>
                        <input type="file" name="fotos[]" multiple class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                    </div>
                </div>

                <div class="pt-4 text-center">
                    <button type="submit" class="btn-gradient w-full py-4 text-lg">
                        Simpan Barang Jaminan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection
</x-app-layout>
