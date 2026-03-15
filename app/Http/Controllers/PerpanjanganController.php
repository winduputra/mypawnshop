<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perpanjangan;
use App\Models\TransaksiRahn;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerpanjanganController extends Controller
{
    public function store(Request $request, TransaksiRahn $transaksi)
    {
        $request->validate([
            'ujrah_dibayar' => 'required|numeric|min:0',
        ]);

        return \DB::transaction(function () use ($request, $transaksi) {
            $jatuh_tempo = Carbon::parse($transaksi->tanggal_jatuh_tempo)->addDays(30);
            $batas_lelang = Carbon::parse($transaksi->tanggal_batas_lelang)->addDays(30);

            Perpanjangan::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'tanggal_perpanjangan' => now()->toDateString(),
                'tambahan_tenor_hari' => '30',
                'tanggal_jatuh_tempo_baru' => $jatuh_tempo->toDateString(),
                'ujrah_dibayar' => $request->ujrah_dibayar,
            ]);

            $transaksi->update([
                'tanggal_jatuh_tempo' => $jatuh_tempo->toDateString(),
                'tanggal_batas_lelang' => $batas_lelang->toDateString(),
                'status' => 'diperpanjang',
            ]);

            return redirect()->back()->with('success', 'Tenor berhasil diperpanjang 30 hari.');
        });
    }
}
