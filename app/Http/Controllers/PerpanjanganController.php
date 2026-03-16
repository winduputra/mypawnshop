<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perpanjangan;
use App\Models\TransaksiRahn;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PerpanjanganController extends Controller
{
    public function store(Request $request, TransaksiRahn $transaksi)
    {
        // No need to validate ujrah_dibayar from request anymore

        return \DB::transaction(function () use ($request, $transaksi) {
            // Validate extension limits based on category
            $detail = $transaksi->detailTransaksi()->with('barang')->first();
            if (!$detail || !$detail->barang) {
                return redirect()->back()->with('error', 'Gagal, data barang tidak ditemukan pada transaksi ini.');
            }

            $kategori = $detail->barang->kategori;
            $limits = [
                'emas' => 11,
                'elektronik' => 2,
                'kendaraan' => 3
            ];
            
            $limit = $limits[$kategori] ?? 1;
            $perhitunganCount = $transaksi->perpanjangan()->count();

            if ($perhitunganCount >= $limit) {
                return redirect()->back()->with('error', "Gagal, barang kategori " . ucfirst($kategori) . " hanya bisa diperpanjang maksimal {$limit} kali.");
            }

            $jatuh_tempo = Carbon::parse($transaksi->tanggal_jatuh_tempo)->addDays(30);
            $batas_lelang = Carbon::parse($transaksi->tanggal_batas_lelang)->addDays(30);

            Perpanjangan::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'tanggal_perpanjangan' => now()->toDateString(),
                'tambahan_tenor_hari' => '30',
                'tanggal_jatuh_tempo_baru' => $jatuh_tempo->toDateString(),
                'ujrah_dibayar' => $transaksi->ujrah_per_30hari,
            ]);

            $transaksi->update([
                'tanggal_jatuh_tempo' => $jatuh_tempo->toDateString(),
                'tanggal_batas_lelang' => $batas_lelang->toDateString(),
                'status' => 'diperpanjang',
            ]);

            return redirect()->back()->with('success', 'Tenor berhasil diperpanjang 30 hari.');
        });
    }

    public function cetakNota(TransaksiRahn $transaksi, Perpanjangan $perpanjangan)
    {
        // Add auth or role check if needed, for now allow if user is authenticated
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang');
        
        $pdf = Pdf::loadView('transaksi.nota-perpanjangan-pdf', compact('transaksi', 'perpanjangan'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Nota-Perpanjangan-' . $transaksi->no_transaksi . '-' . $perpanjangan->id . '.pdf');
    }
}
