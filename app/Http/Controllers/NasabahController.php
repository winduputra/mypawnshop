<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $nasabahs = Nasabah::when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                             ->orWhere('nik', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('nasabah._table', compact('nasabahs'))->render();
        }

        return view('nasabah.index', compact('nasabahs'));
    }

    public function create()
    {
        return view('nasabah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:nasabah,nik',
            'nama' => 'required',
            'alamat' => 'required',
            'telepon' => 'required|unique:nasabah,telepon',
            'foto_ktp' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('nasabah', 'public');
        }

        Nasabah::create($data);

        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil ditambahkan.');
    }

    public function show(Nasabah $nasabah)
    {
        return view('nasabah.show', compact('nasabah'));
    }

    public function edit(Nasabah $nasabah)
    {
        return view('nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $request->validate([
            'nik' => 'required|unique:nasabah,nik,' . $nasabah->id,
            'nama' => 'required',
            'alamat' => 'required',
            'telepon' => 'required|unique:nasabah,telepon,' . $nasabah->id,
            'foto_ktp' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('nasabah', 'public');
        }

        $nasabah->update($data);

        return redirect()->route('nasabah.index')->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function destroy(Nasabah $nasabah)
    {
        $nasabah->delete();
        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil dihapus.');
    }
}
