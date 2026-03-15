<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Nasabah;
use App\Models\FotoBarang;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('nasabah')->latest()->paginate(10);
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $nasabahs = Nasabah::orderBy('nama')->get();
        return view('barang.create', compact('nasabahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id',
            'nama_barang' => 'required',
            'kategori' => 'required|in:emas,elektronik,kendaraan',
            'deskripsi' => 'nullable',
            'berat' => 'nullable|numeric',
            'taksiran' => 'required|numeric',
            'fotos.*' => 'nullable|image|max:2048'
        ]);

        $barang = Barang::create($request->only([
            'nasabah_id', 'nama_barang', 'kategori', 'deskripsi', 'berat', 'taksiran'
        ]));

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('barang', 'public');
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'foto_path' => $path
                ]);
            }
        }

        return redirect()->route('barang.index')->with('success', 'Barang jaminan berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load('nasabah', 'fotoBarang');
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $nasabahs = Nasabah::orderBy('nama')->get();
        return view('barang.edit', compact('barang', 'nasabahs'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id',
            'nama_barang' => 'required',
            'kategori' => 'required|in:emas,elektronik,kendaraan',
            'deskripsi' => 'nullable',
            'berat' => 'nullable|numeric',
            'taksiran' => 'required|numeric',
        ]);

        $barang->update($request->only([
            'nasabah_id', 'nama_barang', 'kategori', 'deskripsi', 'berat', 'taksiran'
        ]));

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
