<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Nasabah;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiRahnController extends Controller
{
    public function index()
    {
        $transactions = TransaksiRahn::with('nasabah', 'user')->latest()->paginate(10);
        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        $nasabahs = Nasabah::with('barang')->orderBy('nama')->get();
        return view('transaksi.create', compact('nasabahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barang,id',
            'tenor_hari' => 'required|in:30,60,90',
            'tanggal_transaksi' => 'required|date',
            'biaya_admin' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $nasabah = Nasabah::find($request->nasabah_id);
            $barangs = Barang::whereIn('id', $request->barang_ids)->get();
            
            $total_taksiran = 0;
            $total_pinjaman = 0;
            $total_ujrah_per_30 = 0;

            $details = [];
            foreach ($barangs as $barang) {
                // Syariah Logic: Calculate pinjaman & ujrah based on category
                if ($barang->kategori == 'emas') {
                    $pinjaman = $barang->taksiran * 0.85;
                    $ujrah = $barang->taksiran * 0.01;
                } elseif ($barang->kategori == 'elektronik') {
                    $pinjaman = $barang->taksiran * 0.70;
                    $ujrah = $barang->taksiran * 0.015;
                } else { // kendaraan
                    $pinjaman = $barang->taksiran * 0.75;
                    $ujrah = $barang->taksiran * 0.0125;
                }

                $total_taksiran += $barang->taksiran;
                $total_pinjaman += $pinjaman;
                $total_ujrah_per_30 += $ujrah;

                $details[] = new DetailTransaksi([
                    'barang_id' => $barang->id,
                    'taksiran_item' => $barang->taksiran,
                    'pinjaman_item' => $pinjaman,
                ]);
            }

            $tanggal_trx = Carbon::parse($request->tanggal_transaksi);
            $jatuh_tempo = $tanggal_trx->copy()->addDays((int)$request->tenor_hari);
            $batas_lelang = $jatuh_tempo->copy()->addDays(7);

            $no_trx = 'RAHN-' . $tanggal_trx->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $transaksi = TransaksiRahn::create([
                'no_transaksi' => $no_trx,
                'nasabah_id' => $request->nasabah_id,
                'user_id' => Auth::id(),
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'total_taksiran' => $total_taksiran,
                'total_pinjaman' => $total_pinjaman,
                'biaya_admin' => $request->biaya_admin,
                'ujrah_per_30hari' => $total_ujrah_per_30,
                'tenor_hari' => $request->tenor_hari,
                'tanggal_jatuh_tempo' => $jatuh_tempo->toDateString(),
                'tanggal_batas_lelang' => $batas_lelang->toDateString(),
                'status' => 'aktif',
            ]);

            $transaksi->detailTransaksi()->saveMany($details);

            return redirect()->route('transaksi.show', $transaksi)->with('success', 'Transaksi Rahn berhasil dibuat.');
        });
    }

    public function show(TransaksiRahn $transaksi)
    {
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang', 'perpanjangan', 'pelunasan', 'lelang');
        return view('transaksi.show', compact('transaksi'));
    }
}
