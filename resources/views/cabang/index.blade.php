@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Manajemen Cabang</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Data Cabang</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data cabang MyPawnShop dengan tampilan yang rapi dan mudah dibaca.</p>
        </div>
        @if(auth()->user()->role !== 'admin')
        <div>
            <a href="{{ route('cabang.create') }}" class="inline-flex items-center space-x-2 rounded-xl bg-brand-green px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-green/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Cabang</span>
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Nama Cabang</th>
                        <th class="px-6 py-4 font-semibold">Alamat</th>
                        <th class="px-6 py-4 font-semibold">Telepon</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($cabangs as $index => $cabang)
                    <tr class="transition-colors duration-200 hover:bg-emerald-50/50">
                        <td class="px-6 py-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $cabang->nama_cabang }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $cabang->alamat ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $cabang->telepon ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('cabang.edit', $cabang) }}" class="rounded-lg p-2 text-brand-green transition-colors hover:bg-emerald-50 hover:text-emerald-900" aria-label="Edit cabang">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('cabang.destroy', $cabang) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-rose-500 transition-colors hover:bg-rose-50 hover:text-rose-700" aria-label="Hapus cabang">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            Belum ada data cabang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
