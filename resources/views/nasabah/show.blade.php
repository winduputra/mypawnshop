<x-app-layout>
    @section('header_title', 'Detail Nasabah')

    @section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('nasabah.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar
            </a>
            <div class="flex gap-3">
                <a href="{{ route('nasabah.edit', $nasabah) }}" class="glass px-4 py-2 rounded-xl text-sm text-sky-400 hover:bg-white/10 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Data
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Info Card -->
            <div class="md:col-span-2 glass-card p-8">
                <h3 class="text-xl font-bold text-white mb-6">Informasi Nasabah</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Nama Lengkap</p>
                        <p class="text-lg font-medium text-white">{{ $nasabah->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">NIK</p>
                        <p class="text-lg font-medium text-white">{{ $nasabah->nik }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Nomor Telepon</p>
                        <p class="text-lg font-medium text-sky-400">{{ $nasabah->telepon }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Email</p>
                        <p class="text-lg font-medium text-white">{{ $nasabah->email ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-slate-500 uppercase font-semibold">Alamat Lengkap</p>
                        <p class="text-base text-white mt-1">{{ $nasabah->alamat }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Photo Card -->
            <div class="space-y-6">
                <div class="glass-card p-6 text-center">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-4">Foto Nasabah</p>
                    @if($nasabah->foto)
                        <img src="{{ asset('storage/' . $nasabah->foto) }}" alt="Foto Nasabah" class="w-full max-w-[200px] h-auto object-cover rounded-xl border-2 border-white/10 mx-auto shadow-lg">
                    @else
                        <div class="w-full max-w-[200px] aspect-square rounded-xl bg-slate-800 border-2 border-dashed border-white/20 flex flex-col items-center justify-center text-slate-500 mx-auto">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <p class="text-sm">Tidak Ada Foto</p>
                        </div>
                    @endif
                </div>

                <div class="glass-card p-6 text-center">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-4">Foto Alat Identitas (KTP)</p>
                    @if($nasabah->foto_ktp)
                        <a href="{{ asset('storage/' . $nasabah->foto_ktp) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/' . $nasabah->foto_ktp) }}" alt="Foto KTP" class="w-full rounded-xl border-2 border-white/10 shadow-lg hover:opacity-80 transition">
                        </a>
                        <p class="text-[10px] text-slate-500 mt-2">Klik gambar untuk memperbesar</p>
                    @else
                        <div class="w-full aspect-video rounded-xl bg-slate-800 border-2 border-dashed border-white/20 flex flex-col items-center justify-center text-slate-500 mx-auto">
                            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            <p class="text-sm">Tidak Ada KTP</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
