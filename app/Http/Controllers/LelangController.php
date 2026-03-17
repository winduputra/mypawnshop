<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Lelang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LelangController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Show ALL past-due transactions (whether auctioned or not) so status is visible
        $query = TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang', 'lelang'])
            ->where('tanggal_batas_lelang', '<=', now()->toDateString())
            ->with('nasabah', 'detailTransaksi.barang', 'lelang');

        // Branch isolation for kasir
        if ($user->role === 'kasir' && $user->cabang_id) {
            $query->whereHas('nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }

        $transactions = $query->latest()->paginate(15);

        return view('lelang.index', compact('transactions'));
    }

    public function show($lelang)
    {
        $transaksi = TransaksiRahn::with('nasabah', 'detailTransaksi.barang', 'lelang')->findOrFail($lelang);
        return view('lelang.show', compact('transaksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaksi_rahn_id' => 'required|exists:transaksi_rahn,id',
            'harga_lelang'      => 'required|numeric|min:0',
            'biaya_lelang'      => 'required|numeric|min:0',
            'pembeli'           => 'required|string',
            'tanggal_lelang'    => 'required|date',
        ]);

        return DB::transaction(function () use ($request) {
            $transaksi       = TransaksiRahn::findOrFail($request->transaksi_rahn_id);
            $harga           = floatval($request->harga_lelang);
            $biaya           = floatval($request->biaya_lelang);

            // Rumus: Harga Terjual - (Sisa Pinjaman + Biaya Lelang)
            // Contoh: 1.800.000 - (1.050.000 + 0) = 750.000 → hak nasabah
            $total_kewajiban = $transaksi->sisa_pinjaman + $biaya;

            $kelebihan = 0;  // Dana kembali ke nasabah (hak nasabah)
            $kerugian  = 0;  // Kekurangan / sisa utang nasabah
            $sisa_pinjaman_setelah = 0;

            if ($harga >= $total_kewajiban) {
                // Ada kelebihan — hak nasabah
                $kelebihan = $harga - $total_kewajiban;
            } else {
                // Kurang — nasabah masih punya sisa utang
                $kerugian              = $total_kewajiban - $harga;
                $sisa_pinjaman_setelah = $kerugian;
            }

            $lelang = Lelang::create([
                'transaksi_rahn_id'  => $transaksi->id,
                'user_id'            => Auth::id(),
                'tanggal_lelang'     => $request->tanggal_lelang,
                'harga_lelang'       => $harga,
                'biaya_lelang'       => $biaya,
                'pembeli'            => $request->pembeli,
                'sisa_untuk_nasabah' => $kelebihan,
                'kerugian'           => $kerugian,
                'sisa_pinjaman'      => $sisa_pinjaman_setelah,
            ]);

            $transaksi->update([
                'status'        => 'lelang',
                'sisa_pinjaman' => $sisa_pinjaman_setelah,
            ]);

            return redirect()->route('lelang.hasil', $lelang->id);
        });
    }

    public function hasil($id)
    {
        $lelang = Lelang::with('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user')
            ->findOrFail($id);
        return view('lelang.hasil', compact('lelang'));
    }

    public function cetakPdf(Lelang $lelang)
    {
        $lelang->load('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lelang.pdf', compact('lelang'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan-Lelang-' . $lelang->transaksiRahn->no_transaksi . '.pdf');
    }
}
