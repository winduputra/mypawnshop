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
        $user = auth()->user();

        $transactions = TransaksiRahn::with('nasabah.cabang', 'user')
            ->when($user->role === 'kasir', function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->cabang_id) {
                        $query->orWhereHas('nasabah', fn($q2) => $q2->where('cabang_id', $user->cabang_id));
                    }
                });
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('no_transaksi', 'like', "%{$search}%")
                      ->orWhere('no_register_akad', 'like', "%{$search}%")
                      ->orWhereHas('nasabah', function ($q2) use ($search) {
                          $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                      });
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
                $q->whereIn('status', ['aktif', 'diperpanjang', 'draft']);
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
            'metode_pembayaran' => 'required|in:bayar_dimuka,potong_pinjaman,bayar_pelunasan',
        ]);

        return DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);
            $tenor = 30;
            
            $maxPercentage = Setting::getLoanPercentage($barang->kategori);
            $maxPinjaman = $barang->taksiran * $maxPercentage;
            
            $pinjaman = isset($request->pinjaman_items[$barang->id]) 
                ? min(floatval($request->pinjaman_items[$barang->id]), $maxPinjaman)
                : $maxPinjaman;

            // Ijarah = percentage of taksiran per 30 days
            $ijarahPersen = Setting::getIjarahPersen();
            $ujrah = $barang->taksiran * ($ijarahPersen / 100);

            // Biaya admin per kategori
            $biayaAdmin = Setting::getBiayaAdmin($barang->kategori);

            $total_taksiran = $barang->taksiran;
            $total_pinjaman = $pinjaman;
            $biaya_penitipan = $ujrah;
            $sisa_pinjaman = $total_pinjaman;

            $tanggal_trx = Carbon::parse($request->tanggal_transaksi);
            $jatuh_tempo = $tanggal_trx->copy()->addDays($tenor);
            $batas_lelang = $jatuh_tempo->copy()->addDays(7);

            $no_trx = 'RAHN-' . $tanggal_trx->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $transaksi = TransaksiRahn::create([
                'no_transaksi' => $no_trx,
                'no_register_akad' => null, // assigned on approval
                'nasabah_id' => $request->nasabah_id,
                'user_id' => Auth::id(),
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'total_taksiran' => $total_taksiran,
                'total_pinjaman' => $total_pinjaman,
                'sisa_pinjaman' => $sisa_pinjaman,
                'biaya_admin' => $biayaAdmin,
                'biaya_penitipan' => $biaya_penitipan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'ujrah_per_30hari' => $ujrah,
                'tenor_hari' => $tenor,
                'tanggal_jatuh_tempo' => $jatuh_tempo->toDateString(),
                'tanggal_batas_lelang' => $batas_lelang->toDateString(),
                'status' => 'draft',
                'status_approval' => 'draft',
            ]);

            $transaksi->detailTransaksi()->create([
                'barang_id' => $barang->id,
                'taksiran_item' => $barang->taksiran,
                'pinjaman_item' => $pinjaman,
            ]);

            return redirect()->route('transaksi.show', $transaksi)->with('success', 'Draft akad berhasil dibuat. Silakan kirim ke admin untuk verifikasi.');
        });
    }

    /**
     * Kasir sends draft to admin for review.
     */
    public function kirimKeAdmin(TransaksiRahn $transaksi)
    {
        if ($transaksi->status_approval !== 'draft' && $transaksi->status_approval !== 'pending') {
            return back()->with('error', 'Akad tidak dapat dikirim.');
        }

        $wasPending = $transaksi->status_approval === 'pending';

        $transaksi->update([
            'status_approval' => 'dikirim',
            'catatan_admin' => null, // clear previous notes
        ]);

        $transaksi->histories()->create([
            'user_id' => Auth::id(),
            'action' => $wasPending ? 'resubmitted' : 'sent',
            'status_approval' => 'dikirim',
            'note' => $wasPending ? 'Akad diajukan ulang ke admin.' : 'Akad dikirim ke admin untuk verifikasi.',
        ]);

        return back()->with('success', 'Akad berhasil dikirim ke admin untuk diverifikasi.');
    }

    /**
     * Admin review page.
     */
    public function review(TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'owner'])) {
            abort(403);
        }

        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang.fotoBarang');
        return view('transaksi.review', compact('transaksi'));
    }

    /**
     * Admin approves akad.
     */
    public function approveAkad(Request $request, TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'owner'])) {
            abort(403);
        }

        $request->validate([
            'taksiran_final' => 'required|numeric|min:0',
            'catatan_admin'  => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $transaksi) {
            $taksiran_final = floatval($request->taksiran_final);
            $taksiranAwal = (float) $transaksi->total_taksiran;
            $detail = $transaksi->detailTransaksi->first();
            $barang = $detail->barang;

            // Recalculate based on taksiran final
            $maxPercentage = Setting::getLoanPercentage($barang->kategori);
            $maxPinjaman = $taksiran_final * $maxPercentage;
            $pinjaman = min($transaksi->total_pinjaman, $maxPinjaman);

            $ijarahPersen = Setting::getIjarahPersen();
            $ujrah = $taksiran_final * ($ijarahPersen / 100);

            if (abs($taksiran_final - $taksiranAwal) > 0.01) {
                $transaksi->update([
                    'no_register_akad' => null,
                    'taksiran_final' => $taksiran_final,
                    'total_taksiran' => $taksiran_final,
                    'total_pinjaman' => $pinjaman,
                    'sisa_pinjaman' => $pinjaman,
                    'ujrah_per_30hari' => $ujrah,
                    'biaya_penitipan' => $ujrah,
                    'status' => 'draft',
                    'status_approval' => 'menunggu_persetujuan_nasabah',
                    'approved_by' => Auth::id(),
                    'approved_at' => null,
                    'catatan_admin' => $request->catatan_admin,
                ]);

                $detail->update([
                    'taksiran_item' => $taksiran_final,
                    'pinjaman_item' => $pinjaman,
                ]);

                $barang->update([
                    'taksiran' => $taksiran_final,
                ]);

                $transaksi->histories()->create([
                    'user_id' => Auth::id(),
                    'action' => 'customer_confirmation_requested',
                    'status_approval' => 'menunggu_persetujuan_nasabah',
                    'note' => $request->catatan_admin ?: 'Nilai taksiran final berubah, menunggu konfirmasi nasabah melalui kasir.',
                ]);

                return redirect()->route('transaksi.show', $transaksi)->with('success', 'Nilai taksiran final berubah. Transaksi dikirim kembali ke kasir untuk konfirmasi nasabah.');
            }

            $noRegister = $this->generateNoRegisterAkad();

            $transaksi->update([
                'no_register_akad' => $noRegister,
                'taksiran_final' => $taksiran_final,
                'total_taksiran' => $taksiran_final,
                'total_pinjaman' => $pinjaman,
                'sisa_pinjaman' => $pinjaman,
                'ujrah_per_30hari' => $ujrah,
                'biaya_penitipan' => $ujrah,
                'status' => 'aktif',
                'status_approval' => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_admin' => $request->catatan_admin,
            ]);

            $transaksi->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'approved',
                'status_approval' => 'disetujui',
                'note' => $request->catatan_admin ?: "Akad disetujui dengan nomor register {$noRegister}.",
            ]);

            // Update detail
            $detail->update([
                'taksiran_item' => $taksiran_final,
                'pinjaman_item' => $pinjaman,
            ]);

            // Update barang taksiran
            $barang->update([
                'taksiran' => $taksiran_final,
            ]);

            return redirect()->route('transaksi.show', $transaksi)->with('success', "Akad DISETUJUI. No Register: {$noRegister}");
        });
    }

    /**
     * Cashier confirms that customer accepts admin final valuation.
     */
    public function nasabahSetuju(TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if ($user->role !== 'kasir' || $transaksi->status_approval !== 'menunggu_persetujuan_nasabah') {
            abort(403);
        }

        return DB::transaction(function () use ($transaksi) {
            $noRegister = $this->generateNoRegisterAkad();

            $transaksi->update([
                'no_register_akad' => $noRegister,
                'status' => 'aktif',
                'status_approval' => 'disetujui',
                'approved_by' => $transaksi->approved_by ?: Auth::id(),
                'approved_at' => now(),
            ]);

            $transaksi->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'customer_approved',
                'status_approval' => 'disetujui',
                'note' => "Nasabah menyetujui nilai taksiran final. Akad diterbitkan dengan nomor register {$noRegister}.",
            ]);

            return redirect()->route('transaksi.show', $transaksi)->with('success', "Nasabah setuju. Akad diterbitkan dengan No Register: {$noRegister}");
        });
    }

    /**
     * Cashier closes transaction when customer rejects admin final valuation.
     */
    public function nasabahTidakSetuju(TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if ($user->role !== 'kasir' || $transaksi->status_approval !== 'menunggu_persetujuan_nasabah') {
            abort(403);
        }

        $transaksi->update([
            'no_register_akad' => null,
            'status' => 'ditolak',
            'status_approval' => 'ditolak',
            'catatan_admin' => trim(($transaksi->catatan_admin ? $transaksi->catatan_admin . "\n" : '') . 'Nasabah tidak setuju dengan nilai taksiran final.'),
        ]);

        $transaksi->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'customer_rejected',
            'status_approval' => 'ditolak',
            'note' => 'Nasabah tidak setuju dengan nilai taksiran final. Transaksi ditutup tanpa nomor register akad.',
        ]);

        return redirect()->route('transaksi.show', $transaksi)->with('success', 'Nasabah tidak setuju. Transaksi ditutup tanpa nomor register akad.');
    }

    /**
     * Admin marks akad as pending (needs revision).
     */
    public function pendingAkad(Request $request, TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'owner'])) {
            abort(403);
        }

        $request->validate([
            'catatan_admin' => 'required|string|min:5',
        ]);

        $transaksi->update([
            'status_approval' => 'pending',
            'catatan_admin' => $request->catatan_admin,
        ]);

        $transaksi->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'pending',
            'status_approval' => 'pending',
            'note' => $request->catatan_admin,
        ]);

        return redirect()->route('transaksi.show', $transaksi)->with('success', 'Akad dikembalikan ke kasir dengan status PENDING.');
    }

    /**
     * Admin rejects akad.
     */
    public function rejectAkad(Request $request, TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'owner'])) {
            abort(403);
        }

        $request->validate([
            'catatan_admin' => 'required|string|min:5',
        ]);

        $transaksi->update([
            'status' => 'ditolak',
            'status_approval' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        $transaksi->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'status_approval' => 'ditolak',
            'note' => $request->catatan_admin,
        ]);

        return redirect()->route('transaksi.show', $transaksi)->with('success', 'Akad DITOLAK.');
    }

    public function show(TransaksiRahn $transaksi)
    {
        $transaksi->load('nasabah.cabang', 'user', 'approvedByUser', 'detailTransaksi.barang', 'perpanjangan.user', 'pelunasan', 'lelang.user', 'lelang.approvedByUser', 'lelang.ownerEditedByUser', 'angsuran.user', 'histories.user');
        $noTeleponCs = Setting::getValue('no_telepon_cs', '6281234567890');
        return view('transaksi.show', compact('transaksi', 'noTeleponCs'));
    }

    public function edit(TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if ($user->role !== 'kasir' || !in_array($transaksi->status_approval, ['draft', 'pending'])) {
            abort(403);
        }

        $transaksi->load('nasabah', 'detailTransaksi.barang');
        $settings = Setting::all()->pluck('value', 'key');

        return view('transaksi.edit', compact('transaksi', 'settings'));
    }

    public function update(Request $request, TransaksiRahn $transaksi)
    {
        $user = auth()->user();
        if ($user->role !== 'kasir' || !in_array($transaksi->status_approval, ['draft', 'pending'])) {
            abort(403);
        }

        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|in:bayar_dimuka,potong_pinjaman,bayar_pelunasan',
            'total_pinjaman' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($request, $transaksi) {
            $detail = $transaksi->detailTransaksi()->with('barang')->firstOrFail();
            $barang = $detail->barang;
            $tenor = 30;
            $maxPinjaman = $barang->taksiran * Setting::getLoanPercentage($barang->kategori);
            $pinjaman = min((float) $request->total_pinjaman, $maxPinjaman);
            $ujrah = $barang->taksiran * (Setting::getIjarahPersen() / 100);
            $biayaAdmin = Setting::getBiayaAdmin($barang->kategori);
            $tanggalTrx = Carbon::parse($request->tanggal_transaksi);

            $transaksi->update([
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'total_taksiran' => $barang->taksiran,
                'total_pinjaman' => $pinjaman,
                'sisa_pinjaman' => $pinjaman,
                'biaya_admin' => $biayaAdmin,
                'biaya_penitipan' => $ujrah,
                'metode_pembayaran' => $request->metode_pembayaran,
                'ujrah_per_30hari' => $ujrah,
                'tenor_hari' => $tenor,
                'tanggal_jatuh_tempo' => $tanggalTrx->copy()->addDays($tenor),
                'tanggal_batas_lelang' => $tanggalTrx->copy()->addDays($tenor + 7),
            ]);

            $detail->update([
                'taksiran_item' => $barang->taksiran,
                'pinjaman_item' => $pinjaman,
            ]);

            $transaksi->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'status_approval' => $transaksi->status_approval,
                'note' => 'Kasir memperbaiki akad pending.',
            ]);

            return redirect()->route('transaksi.show', $transaksi)->with('success', 'Akad berhasil diperbarui. Silakan kirim ulang ke admin.');
        });
    }
    public function cetakKontrak(TransaksiRahn $transaksi)
    {
        if ($transaksi->status_approval !== 'disetujui') {
            return back()->with('error', 'Kontrak hanya bisa dicetak setelah akad disetujui.');
        }

        $transaksi->load('nasabah.cabang', 'user', 'detailTransaksi.barang');
        
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
        $masukMasaLelang = in_array($transaksi->status, ['lelang', 'lelang_pending', 'lelang_aktif', 'lelang_terjual'])
            || ($transaksi->tanggal_batas_lelang && Carbon::parse($transaksi->tanggal_batas_lelang)->lte(now()));

        if ($masukMasaLelang) {
            return back()->with('error', 'Bayar angsuran tidak dapat dilakukan. Barang pada transaksi ini sedang lelang atau terlelang.');
        }

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
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang');
        $angsuran->load('user');

        // Calculate "Angsuran ke-" (which installment number this is)
        $angsuranKe = Angsuran::where('transaksi_rahn_id', $transaksi->id)
            ->where('id', '<=', $angsuran->id)
            ->count();

        $pdf = Pdf::loadView('transaksi.bukti-angsuran-pdf', compact('transaksi', 'angsuran', 'angsuranKe'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Bukti-Angsuran-' . $transaksi->no_transaksi . '-' . $angsuranKe . '.pdf');
    }

    private function generateNoRegisterAkad(): string
    {
        $today = now()->format('Ymd');
        $lastAkad = TransaksiRahn::whereNotNull('no_register_akad')
            ->where('no_register_akad', 'like', "AKD-{$today}-%")
            ->orderByDesc('no_register_akad')
            ->first();

        $seq = 1;
        if ($lastAkad) {
            $parts = explode('-', $lastAkad->no_register_akad);
            $seq = intval(end($parts)) + 1;
        }

        return "AKD-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
