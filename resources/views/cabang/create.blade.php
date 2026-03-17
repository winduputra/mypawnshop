@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('cabang.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-white">Tambah Cabang</h1>
            <p class="text-sm text-gray-400 mt-1">Masukkan data cabang baru</p>
        </div>
    </div>

    <div class="bg-indigo-950/20 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        <form action="{{ route('cabang.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="nama_cabang" class="block text-sm font-medium text-gray-300 mb-1">Nama Cabang</label>
                    <input type="text" name="nama_cabang" id="nama_cabang" value="{{ old('nama_cabang') }}" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200">
                    @error('nama_cabang')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-300 mb-1">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-300 mb-1">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200">
                    @error('telepon')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-white/10 flex justify-end space-x-3">
                <a href="{{ route('cabang.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors duration-200">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
