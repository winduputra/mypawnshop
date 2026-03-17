<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        $tarifUjrahs = \App\Models\TarifUjrah::orderBy('kategori_barang')->orderBy('min_taksiran')->get();
        return view('pengaturan.index', compact('settings', 'tarifUjrahs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'biaya_admin' => 'required|numeric|min:0',
            'ujrah_emas' => 'required|numeric|min:0',
            'ujrah_elektronik' => 'required|numeric|min:0',
            'ujrah_kendaraan' => 'required|numeric|min:0',
            'persentase_emas' => 'required|numeric|min:1|max:100',
            'persentase_elektronik' => 'required|numeric|min:1|max:100',
            'persentase_kendaraan' => 'required|numeric|min:1|max:100',
        ]);

        $keys = [
            'biaya_admin', 'ujrah_emas', 'ujrah_elektronik', 'ujrah_kendaraan',
            'persentase_emas', 'persentase_elektronik', 'persentase_kendaraan'
        ];

        foreach ($keys as $key) {
            Setting::setValue($key, $request->input($key));
        }

        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }
}
