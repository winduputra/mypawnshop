<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NasabahController extends Controller
{
    private function branchScope($query)
    {
        $user = Auth::user();
        if ($user->role === 'kasir' && $user->cabang_id) {
            $query->where('cabang_id', $user->cabang_id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Nasabah::when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('telepon', 'like', "%{$search}%");
            })
            ->latest();

        $this->branchScope($query);

        $nasabahs = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('nasabah._table', compact('nasabahs'))->render();
        }

        return view('nasabah.index', compact('nasabahs'));
    }

    public function create()
    {
        $nextId = (Nasabah::max('id') ?? 0) + 1;
        return view('nasabah.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'                   => 'required|unique:nasabah,nik',
            'nama'                  => 'required',
            'email'                 => 'required|email|unique:nasabah,email',
            'alamat'                => 'required',
            'alamat_domisili'       => 'nullable',
            'telepon'               => 'required|unique:nasabah,telepon',
            'no_wa'                 => 'nullable',
            'foto_ktp'              => 'required|mimes:jpeg,jpg,png|max:500',
            'foto'                  => 'nullable|mimes:jpeg,jpg,png|max:500',
            'nama_bank'             => 'required',
            'no_rekening'           => 'required',
            'nama_pemilik_rekening' => 'required',
            'nama_ibu_kandung'      => 'required',
            'pekerjaan'             => 'required',
            'status_pernikahan'     => 'required|in:Menikah,Belum Menikah,Duda/Janda',
        ]);

        $data = $request->all();

        // Auto-assign cabang_id from the logged-in kasir
        $user = Auth::user();
        if ($user->role === 'kasir' && $user->cabang_id) {
            $data['cabang_id'] = $user->cabang_id;
        }

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('nasabah', 'public');
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('nasabah', 'public');
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
            'nik'                   => 'required|unique:nasabah,nik,' . $nasabah->id,
            'nama'                  => 'required',
            'email'                 => 'required|email|unique:nasabah,email,' . $nasabah->id,
            'alamat'                => 'required',
            'alamat_domisili'       => 'nullable',
            'telepon'               => 'required|unique:nasabah,telepon,' . $nasabah->id,
            'no_wa'                 => 'nullable',
            'foto_ktp'              => 'nullable|mimes:jpeg,jpg,png|max:500',
            'foto'                  => 'nullable|mimes:jpeg,jpg,png|max:500',
            'nama_bank'             => 'required',
            'no_rekening'           => 'required',
            'nama_pemilik_rekening' => 'required',
            'nama_ibu_kandung'      => 'required',
            'pekerjaan'             => 'required',
            'status_pernikahan'     => 'required|in:Menikah,Belum Menikah,Duda/Janda',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_ktp')) {
            if ($nasabah->foto_ktp) Storage::disk('public')->delete($nasabah->foto_ktp);
            $data['foto_ktp'] = $request->file('foto_ktp')->store('nasabah', 'public');
        }

        if ($request->hasFile('foto')) {
            if ($nasabah->foto) Storage::disk('public')->delete($nasabah->foto);
            $data['foto'] = $request->file('foto')->store('nasabah', 'public');
        }

        $nasabah->update($data);

        return redirect()->route('nasabah.index')->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function destroy(Nasabah $nasabah)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner'])) {
            return back()->with('error', 'Hanya admin/owner yang dapat menghapus nasabah.');
        }

        if ($nasabah->foto_ktp) Storage::disk('public')->delete($nasabah->foto_ktp);
        if ($nasabah->foto)     Storage::disk('public')->delete($nasabah->foto);

        $nasabah->delete();
        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil dihapus.');
    }
}
