<x-app-layout>
    @section('header_title', 'Tambah Barang Jaminan')
    
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
                        <select name="nasabah_id" id="nasabah_id" class="w-full" required>
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



                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Nilai Taksiran (Rp)</label>
                        <input type="number" name="taksiran" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500 text-xl font-bold" required>
                        <p class="text-xs text-slate-500 mt-2 italic">Nilai pasar wajar saat ini untuk barang tersebut.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Deskripsi / Kondisi Barang</label>
                        <textarea name="deskripsi" rows="3" class="w-full glass bg-white/5 border-white/10 rounded-xl px-4 py-3 text-white focus:border-sky-500 focus:ring-sky-500" placeholder="Detail kondisi, kelengkapan, dll."></textarea>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Foto Barang 1 <span class="text-rose-400">*</span></label>
                            <input type="file" name="foto_1" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20" required>
                            <p class="text-[10px] text-slate-500 mt-1">Wajib diunggah. Max 2MB.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Foto Barang 2</label>
                            <input type="file" name="foto_2" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                            <p class="text-[10px] text-slate-500 mt-1">Opsional. Max 2MB.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">Foto Barang 3</label>
                            <input type="file" name="foto_3" class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                            <p class="text-[10px] text-slate-500 mt-1">Opsional. Max 2MB.</p>
                        </div>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect("#nasabah_id", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Pilih Nasabah --",
            allowEmptyOption: true,
        });
    </script>
    @endpush
</x-app-layout>
