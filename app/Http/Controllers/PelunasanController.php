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
        if (!in_array($transaksi->status, ['aktif', 'diperpanjang'])) {
            return back()->with('error', 'Transaksi tidak dapat dilunasi.');
        }

        $sisaPinjaman = (float) $transaksi->sisa_pinjaman;

        if ($sisaPinjaman <= 0) {
            return back()->with('error', 'Sisa pinjaman sudah tidak ada.');
        }

        return \DB::transaction(function () use ($transaksi, $sisaPinjaman) {
            Pelunasan::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'tanggal_pelunasan' => now()->toDateString(),
                'total_pinjaman' => $sisaPinjaman,
                'total_ujrah' => 0,
                'total_bayar' => $sisaPinjaman,
            ]);

            $transaksi->update([
                'sisa_pinjaman' => 0,
                'status' => 'lunas',
            ]);

            return redirect()->back()->with('success', 'Transaksi berhasil dilunasi.');
        });
    }
}
