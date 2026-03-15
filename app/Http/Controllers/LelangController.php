<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Lelang;

class LelangController extends Controller
{
    public function index()
    {
        // Items that have passed auction date and are not settled
        $transactions = TransaksiRahn::where('status', '!=', 'lunas')
            ->where('tanggal_batas_lelang', '<=', now()->toDateString())
            ->with('nasabah', 'detailTransaksi.barang')
            ->latest()
            ->paginate(10);
            
        return view('lelang.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaksi_rahn_id' => 'required|exists:transaksi_rahn,id',
            'harga_lelang' => 'required|numeric|min:0',
            'biaya_lelang' => 'required|numeric|min:0',
            'pembeli' => 'required|string',
            'tanggal_lelang' => 'required|date',
        ]);

        return \DB::transaction(function () use ($request) {
            $transaksi = TransaksiRahn::findOrFail($request->transaksi_rahn_id);
            
            $kelebihan = max(0, $request->harga_lelang - ($transaksi->total_pinjaman + $request->biaya_lelang));

            Lelang::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => \Auth::id(),
                'tanggal_lelang' => $request->tanggal_lelang,
                'harga_lelang' => $request->harga_lelang,
                'pembeli' => $request->pembeli,
                'sisa_untuk_nasabah' => $kelebihan,
            ]);

            $transaksi->update(['status' => 'lelang']);

            return redirect()->route('lelang.index')->with('success', 'Eksekusi lelang berhasil diselesaikan.');
        });
    }
}
