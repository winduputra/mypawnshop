<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Nasabah;
use App\Models\FotoBarang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    private function getBaseQuery()
    {
        $user = Auth::user();
        $query = Barang::with('nasabah', 'fotoBarang');
        if ($user->role === 'kasir' && $user->cabang_id) {
            $query->whereHas('nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }
        return $query;
    }

    private function getNasabahScope()
    {
        $user = Auth::user();
        $query = Nasabah::orderBy('nama');
        if ($user->role === 'kasir' && $user->cabang_id) {
            $query->where('cabang_id', $user->cabang_id);
        }
        return $query;
    }
    public function index(Request $request)
    {
        $search = $request->query('search');

        $barangs = $this->getBaseQuery()
            ->when($search, function ($query, $search) {
                return $query->where('nama_barang', 'like', "%{$search}%")
                             ->orWhere('taksiran', 'like', "%{$search}%")
                             ->orWhereHas('nasabah', function ($q) use ($search) {
                                 $q->where('nama', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('barang._table', compact('barangs'))->render();
        }

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $nasabahs = $this->getNasabahScope()->get();
        $nextId = (Barang::max('id') ?? 0) + 1;
        return view('barang.create', compact('nasabahs', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id'    => 'required|exists:nasabah,id',
            'nama_barang'   => 'required',
            'kategori'      => 'required|in:emas,elektronik,kendaraan',
            'merk_type'     => 'nullable',
            'nomor_seri'    => 'nullable',
            'spesifikasi'   => 'nullable',
            'kelengkapan'   => 'nullable|array',
            'kondisi_fisik' => 'nullable',
            'deskripsi'     => 'nullable',
            'taksiran'      => 'required|numeric|min:0',
            'foto_1'        => 'required|image|max:2048',
            'foto_2'        => 'nullable|image|max:2048',
            'foto_3'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nasabah_id', 'nama_barang', 'kategori', 'merk_type',
            'nomor_seri', 'spesifikasi', 'kondisi_fisik', 'deskripsi', 'taksiran',
        ]);
        $data['kelengkapan'] = $request->input('kelengkapan', []);
        $barang = Barang::create($data);

        $fotos = ['foto_1', 'foto_2', 'foto_3'];

        foreach ($fotos as $index => $fotoKey) {
            if ($request->hasFile($fotoKey)) {
                $path = $request->file($fotoKey)->store('barang', 'public');
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'foto_path' => $path,
                    'keterangan' => 'Foto ' . ($index + 1)
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
        if (!in_array(auth()->user()->role, ['admin', 'owner'])) {
            return redirect()->route('barang.index')->with('error', 'Hanya admin/owner yang dapat mengedit barang.');
        }

        $nasabahs = Nasabah::orderBy('nama')->get();
        return view('barang.edit', compact('barang', 'nasabahs'));
    }

    public function update(Request $request, Barang $barang)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner'])) {
            return redirect()->route('barang.index')->with('error', 'Hanya admin/owner yang dapat mengedit barang.');
        }

        $request->validate([
            'nasabah_id'    => 'required|exists:nasabah,id',
            'nama_barang'   => 'required',
            'kategori'      => 'required|in:emas,elektronik,kendaraan',
            'merk_type'     => 'nullable',
            'nomor_seri'    => 'nullable',
            'spesifikasi'   => 'nullable',
            'kelengkapan'   => 'nullable|array',
            'kondisi_fisik' => 'nullable',
            'deskripsi'     => 'nullable',
            'taksiran'      => 'required|numeric|min:0',
            'foto_1'        => 'nullable|image|max:2048',
            'foto_2'        => 'nullable|image|max:2048',
            'foto_3'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nasabah_id', 'nama_barang', 'kategori', 'merk_type',
            'nomor_seri', 'spesifikasi', 'kondisi_fisik', 'deskripsi', 'taksiran',
        ]);
        $data['kelengkapan'] = $request->input('kelengkapan', []);
        $barang->update($data);

        $fotos = ['foto_1' => 'Foto 1', 'foto_2' => 'Foto 2', 'foto_3' => 'Foto 3'];

        foreach ($fotos as $fotoKey => $keterangan) {
            if ($request->hasFile($fotoKey)) {
                // Hapus foto lama dengan keterangan yang sama jika ada
                $oldFoto = $barang->fotoBarang()->where('keterangan', $keterangan)->first();
                if ($oldFoto) {
                    Storage::disk('public')->delete($oldFoto->foto_path);
                    $oldFoto->delete();
                }

                $path = $request->file($fotoKey)->store('barang', 'public');
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'foto_path' => $path,
                    'keterangan' => $keterangan
                ]);
            }
        }

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner'])) {
            return back()->with('error', 'Hanya admin/owner yang dapat menghapus barang jaminan.');
        }

        foreach ($barang->fotoBarang as $foto) {
            Storage::disk('public')->delete($foto->foto_path);
        }
        
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
