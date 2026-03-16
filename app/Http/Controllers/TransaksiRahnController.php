<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Nasabah;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\Setting;
use App\Models\Angsuran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiRahnController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $jatuh_tempo = $request->query('jatuh_tempo');

        $transactions = TransaksiRahn::with('nasabah', 'user')
            ->when($search, function ($query, $search) {
                return $query->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('nasabah', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                    });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($jatuh_tempo, function ($query, $jatuh_tempo) {
                if ($jatuh_tempo === 'segera') {
                    return $query->whereIn('status', ['aktif', 'diperpanjang'])
                        ->where('tanggal_jatuh_tempo', '<=', now()->addDays(7)->toDateString());
                } elseif ($jatuh_tempo === 'lewat') {
                    return $query->whereIn('status', ['aktif', 'diperpanjang'])
                        ->where('tanggal_jatuh_tempo', '<', now()->toDateString());
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('transaksi._table', compact('transactions'))->render();
        }

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        $nasabahs = Nasabah::with(['barang' => function ($query) {
            $query->whereDoesntHave('detailTransaksi.transaksiRahn', function ($q) {
                $q->whereIn('status', ['aktif', 'diperpanjang']);
            });
        }])->orderBy('nama')->get();
        
        $settings = Setting::all()->pluck('value', 'key');
        return view('transaksi.create', compact('nasabahs', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id',
            'barang_id' => 'required|exists:barang,id',
            'pinjaman_items' => 'required|array',
            'tanggal_transaksi' => 'required|date',
            'biaya_admin' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:bayar_dimuka,potong_pinjaman',
        ]);

        return DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);
            
            // Hardcode tenor automatically to 30 days
            $tenor = 30;
            
            $maxPercentage = Setting::getLoanPercentage($barang->kategori);
            $maxPinjaman = $barang->taksiran * $maxPercentage;
            
            $pinjaman = isset($request->pinjaman_items[$barang->id]) 
                ? min(floatval($request->pinjaman_items[$barang->id]), $maxPinjaman)
                : $maxPinjaman;

            // Ujrah = flat Rp per 30 days from settings (per item)
            $ujrah = Setting::getUjrah($barang->kategori);

            $total_taksiran = $barang->taksiran;
            $total_pinjaman = $pinjaman;
            $total_ujrah_per_30 = $ujrah;

            // Since tenor is exactly 30 days
            $biaya_penitipan = $ujrah;
            $biaya_admin = floatval($request->biaya_admin);

            // Calculate sisa_pinjaman based on metode pembayaran
            if ($request->metode_pembayaran === 'potong_pinjaman') {
                $sisa_pinjaman = $total_pinjaman; // Nasabah tetap utang penuh, biaya dipotong dari uang yg diterima
            } else {
                $sisa_pinjaman = $total_pinjaman; // Nasabah bayar biaya di awal secara terpisah
            }

            $tanggal_trx = Carbon::parse($request->tanggal_transaksi);
            $jatuh_tempo = $tanggal_trx->copy()->addDays($tenor);
            $batas_lelang = $jatuh_tempo->copy()->addDays(7);

            $no_trx = 'RAHN-' . $tanggal_trx->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $transaksi = TransaksiRahn::create([
                'no_transaksi' => $no_trx,
                'nasabah_id' => $request->nasabah_id,
                'user_id' => Auth::id(),
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'total_taksiran' => $total_taksiran,
                'total_pinjaman' => $total_pinjaman,
                'sisa_pinjaman' => $sisa_pinjaman,
                'biaya_admin' => $biaya_admin,
                'biaya_penitipan' => $biaya_penitipan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'ujrah_per_30hari' => $total_ujrah_per_30,
                'tenor_hari' => $tenor,
                'tanggal_jatuh_tempo' => $jatuh_tempo->toDateString(),
                'tanggal_batas_lelang' => $batas_lelang->toDateString(),
                'status' => 'aktif',
            ]);

            $transaksi->detailTransaksi()->create([
                'barang_id' => $barang->id,
                'taksiran_item' => $barang->taksiran,
                'pinjaman_item' => $pinjaman,
            ]);

            return redirect()->route('transaksi.show', $transaksi)->with('success', 'Transaksi Rahn berhasil dibuat.');
        });
    }

    public function show(TransaksiRahn $transaksi)
    {
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang', 'perpanjangan', 'pelunasan', 'lelang', 'angsuran.user');
        return view('transaksi.show', compact('transaksi'));
    }

    public function cetakKontrak(TransaksiRahn $transaksi)
    {
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang');
        
        $pdf = Pdf::loadView('transaksi.kontrak-pdf', compact('transaksi'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Kontrak-Gadai-' . $transaksi->no_transaksi . '.pdf');
    }

    public function cetakNotaLunas(TransaksiRahn $transaksi)
    {
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang', 'pelunasan', 'angsuran');
        
        $pdf = Pdf::loadView('transaksi.nota-lunas-pdf', compact('transaksi'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Nota-Lunas-' . $transaksi->no_transaksi . '.pdf');
    }

    public function bayarAngsuran(Request $request, TransaksiRahn $transaksi)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $jumlah = floatval($request->jumlah_bayar);

        if ($jumlah > $transaksi->sisa_pinjaman) {
            return back()->with('error', 'Jumlah bayar melebihi sisa pinjaman.');
        }

        return DB::transaction(function () use ($request, $transaksi, $jumlah) {
            $sisa = $transaksi->sisa_pinjaman - $jumlah;

            $angsuran = Angsuran::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'tanggal_bayar' => now()->toDateString(),
                'jumlah_bayar' => $jumlah,
                'sisa_pinjaman' => $sisa,
                'catatan' => $request->catatan,
            ]);

            $transaksi->update(['sisa_pinjaman' => $sisa]);

            // Auto lunas if sisa = 0
            if ($sisa <= 0) {
                $transaksi->update(['status' => 'lunas']);

                // Create pelunasan record
                \App\Models\Pelunasan::create([
                    'transaksi_rahn_id' => $transaksi->id,
                    'user_id' => Auth::id(),
                    'tanggal_pelunasan' => now()->toDateString(),
                    'total_pinjaman' => $transaksi->total_pinjaman,
                    'total_ujrah' => 0,
                    'total_bayar' => $transaksi->total_pinjaman,
                ]);

                return redirect()->back()->with('success', 'Pinjaman telah LUNAS! Barang jaminan dapat dikembalikan.');
            }

            return redirect()->back()->with('success', 'Angsuran sebesar Rp ' . number_format($jumlah, 0, ',', '.') . ' berhasil dicatat. Sisa pinjaman: Rp ' . number_format($sisa, 0, ',', '.'));
        });
    }

    public function cetakBuktiAngsuran(TransaksiRahn $transaksi, Angsuran $angsuran)
    {
        $transaksi->load('nasabah', 'user');

        $pdf = Pdf::loadView('transaksi.bukti-angsuran-pdf', compact('transaksi', 'angsuran'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Bukti-Angsuran-' . $transaksi->no_transaksi . '-' . $angsuran->id . '.pdf');
    }
}
