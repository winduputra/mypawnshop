@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center space-x-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <a href="{{ route('cabang.index') }}" class="rounded-xl border border-slate-200 p-3 text-slate-500 transition-colors hover:border-brand-gold hover:text-brand-green">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Cabang Baru</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Tambah Cabang</h1>
            <p class="mt-1 text-sm text-slate-500">Masukkan data cabang baru dengan lengkap.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('cabang.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="nama_cabang" class="mb-2 block text-sm font-semibold text-slate-700">Nama Cabang</label>
                    <input type="text" name="nama_cabang" id="nama_cabang" value="{{ old('nama_cabang') }}" required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-brand-green focus:outline-none focus:ring-2 focus:ring-brand-green/20">
                    @error('nama_cabang')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat" class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-brand-green focus:outline-none focus:ring-2 focus:ring-brand-green/20">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telepon" class="mb-2 block text-sm font-semibold text-slate-700">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-brand-green focus:outline-none focus:ring-2 focus:ring-brand-green/20">
                    @error('telepon')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t border-slate-200 pt-5">
                <a href="{{ route('cabang.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-brand-green px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-green/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
