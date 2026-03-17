<?php

namespace App\Http\Controllers;

use App\Models\TarifUjrah;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TarifUjrahController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_barang' => 'required|in:emas,elektronik,kendaraan',
            'min_taksiran' => 'required|numeric|min:0',
            'max_taksiran' => 'required|numeric|gt:min_taksiran',
            'tarif' => 'required|numeric|min:0',
        ]);

        TarifUjrah::create($validated);

        return redirect()->route('pengaturan.index')->with('success', 'Tarif Ujrah berhasil ditambahkan.');
    }

    public function update(Request $request, TarifUjrah $tarifUjrah)
    {
        $validated = $request->validate([
            'kategori_barang' => 'required|in:emas,elektronik,kendaraan',
            'min_taksiran' => 'required|numeric|min:0',
            'max_taksiran' => 'required|numeric|gt:min_taksiran',
            'tarif' => 'required|numeric|min:0',
        ]);

        $tarifUjrah->update($validated);

        return redirect()->route('pengaturan.index')->with('success', 'Tarif Ujrah berhasil diperbarui.');
    }

    public function destroy(TarifUjrah $tarifUjrah)
    {
        $tarifUjrah->delete();

        return redirect()->route('pengaturan.index')->with('success', 'Tarif Ujrah berhasil dihapus.');
    }
}
