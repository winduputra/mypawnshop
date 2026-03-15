<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelunasan;
use App\Models\TransaksiRahn;
use Illuminate\Support\Facades\Auth;

class PelunasanController extends Controller
{
    public function store(Request $request, TransaksiRahn $transaksi)
    {
        $request->validate([
            'total_bayar' => 'required|numeric|min:0',
        ]);

        return \DB::transaction(function () use ($request, $transaksi) {
            Pelunasan::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'tanggal_pelunasan' => now()->toDateString(),
                'total_pinjaman' => $transaksi->total_pinjaman,
                'total_ujrah' => $request->total_bayar - $transaksi->total_pinjaman,
                'total_bayar' => $request->total_bayar,
            ]);

            $transaksi->update(['status' => 'lunas']);

            return redirect()->back()->with('success', 'Transaksi berhasil dilunasi.');
        });
    }
}
