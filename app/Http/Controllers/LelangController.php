<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Lelang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LelangController extends Controller
{
    /**
     * Daftar Lelang — menampilkan transaksi H+8 yang otomatis masuk & semua record lelang
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $statusFilter = $request->get('status', 'semua');

        // ── 1. Transaksi yang sudah melewati H+8 tapi BELUM punya record lelang ──
        $belumLelangQuery = TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang'])
            ->where('tanggal_batas_lelang', '<=', now()->toDateString())
            ->doesntHave('lelang')
            ->with('nasabah', 'detailTransaksi.barang');

        // Branch isolation
        if ($user->role === 'kasir' && $user->cabang_id) {
            $belumLelangQuery->whereHas('nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }

        $belumLelang = ($statusFilter === 'semua' || $statusFilter === 'baru')
            ? $belumLelangQuery->latest()->get()
            : collect();

        // ── 2. Record lelang yang sudah dibuat ──
        $lelangQuery = Lelang::with('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser');

        // Status filter
        if ($statusFilter !== 'semua' && $statusFilter !== 'baru') {
            $lelangQuery->where('status_lelang', $statusFilter);
        }

        // Branch isolation for kasir
        if ($user->role === 'kasir' && $user->cabang_id) {
            $lelangQuery->whereHas('transaksiRahn.nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }

        // Admin & Owner: bisa lihat semua cabang
        // Superadmin: bisa lihat semua data (no filter)

        $lelangRecords = $lelangQuery->latest()->get();

        return view('lelang.index', compact('belumLelang', 'lelangRecords', 'statusFilter'));
    }

    /**
     * Detail transaksi + form input harga lelang (untuk Admin)
     * Juga dipakai untuk review oleh Owner
     */
    public function show($id)
    {
        $user = auth()->user();

        // Cek apakah ini transaksi_rahn ID atau lelang ID
        $lelang = Lelang::with('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser')
            ->find($id);

        if ($lelang) {
            return view('lelang.show', compact('lelang'));
        }

        // Jika belum ada record lelang, tampilkan form buat baru
        $transaksi = TransaksiRahn::with('nasabah', 'detailTransaksi.barang')->findOrFail($id);
        return view('lelang.show', compact('transaksi'));
    }

    /**
     * Admin membuat record lelang baru (draft) dan langsung kirim ke Owner
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_rahn_id' => 'required|exists:transaksi_rahn,id',
            'harga_lelang'      => 'required|numeric|min:0',
            'biaya_lelang'      => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $transaksi = TransaksiRahn::findOrFail($request->transaksi_rahn_id);
            $harga     = floatval($request->harga_lelang);
            $biaya     = floatval($request->biaya_lelang);
            $ijarah    = floatval($transaksi->biaya_penitipan);

            // Hitung sisa dana kembali preview
            $totalKewajiban = $transaksi->sisa_pinjaman + $biaya;
            $sisaDana = max(0, $harga - $totalKewajiban);

            $lelang = Lelang::create([
                'no_lelang'          => Lelang::generateNoLelang(),
                'transaksi_rahn_id'  => $transaksi->id,
                'user_id'            => Auth::id(),
                'harga_lelang'       => $harga,
                'biaya_lelang'       => $biaya,
                'ijarah'             => $ijarah,
                'sisa_pinjaman'      => $transaksi->sisa_pinjaman,
                'sisa_dana_kembali'  => $sisaDana,
                'status_lelang'      => 'pending', // langsung kirim ke owner
            ]);

            $transaksi->update(['status' => 'lelang_pending']);

            return redirect()->route('lelang.index', ['status' => 'pending'])
                ->with('success', 'Data lelang berhasil dikirim ke Owner untuk approval.');
        });
    }

    /**
     * Admin kirim ulang lelang yang dibatalkan ke Owner (setelah revisi)
     */
    public function kirimKeOwner($id)
    {
        $lelang = Lelang::findOrFail($id);

        if (!in_array($lelang->status_lelang, ['draft', 'dibatalkan'])) {
            return back()->with('error', 'Lelang tidak bisa dikirim ulang.');
        }

        $lelang->update([
            'status_lelang' => 'pending',
            'catatan_owner' => null,
        ]);

        $lelang->transaksiRahn->update(['status' => 'lelang_pending']);

        return redirect()->route('lelang.index', ['status' => 'pending'])
            ->with('success', 'Lelang berhasil dikirim ulang ke Owner.');
    }

    /**
     * Owner approve lelang → status AKTIF (terlihat semua cabang)
     */
    public function approve($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['owner', 'superadmin'])) {
            abort(403, 'Hanya Owner/Superadmin yang dapat menyetujui lelang.');
        }

        $lelang = Lelang::findOrFail($id);
        if ($lelang->status_lelang !== 'pending') {
            return back()->with('error', 'Lelang tidak dalam status pending.');
        }

        $lelang->update([
            'status_lelang' => 'aktif',
            'approved_by'   => $user->id,
            'approved_at'   => now(),
            'tanggal_lelang' => now()->toDateString(),
        ]);

        $lelang->transaksiRahn->update(['status' => 'lelang_aktif']);

        return redirect()->route('lelang.index', ['status' => 'aktif'])
            ->with('success', 'Lelang disetujui! Barang sekarang dalam status AKTIF dilelang.');
    }

    /**
     * Owner tolak / minta revisi → status DIBATALKAN, kembali ke Admin
     */
    public function reject(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['owner', 'superadmin'])) {
            abort(403, 'Hanya Owner/Superadmin yang dapat menolak lelang.');
        }

        $lelang = Lelang::findOrFail($id);
        if ($lelang->status_lelang !== 'pending') {
            return back()->with('error', 'Lelang tidak dalam status pending.');
        }

        $lelang->update([
            'status_lelang' => 'dibatalkan',
            'catatan_owner' => $request->catatan_owner ?? 'Silakan revisi harga jual dan biaya admin lelang.',
        ]);

        $lelang->transaksiRahn->update(['status' => 'aktif']); // kembali ke status semula

        return redirect()->route('lelang.index')
            ->with('success', 'Lelang ditolak dan dikembalikan ke Admin untuk revisi.');
    }

    /**
     * Admin update harga setelah dibatalkan (revisi)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'harga_lelang' => 'required|numeric|min:0',
            'biaya_lelang' => 'required|numeric|min:0',
        ]);

        $lelang = Lelang::findOrFail($id);

        if ($lelang->status_lelang !== 'dibatalkan') {
            return back()->with('error', 'Hanya lelang yang dibatalkan yang dapat direvisi.');
        }

        $harga = floatval($request->harga_lelang);
        $biaya = floatval($request->biaya_lelang);
        $totalKewajiban = $lelang->sisa_pinjaman + $biaya;
        $sisaDana = max(0, $harga - $totalKewajiban);

        $lelang->update([
            'harga_lelang'      => $harga,
            'biaya_lelang'      => $biaya,
            'sisa_dana_kembali' => $sisaDana,
            'status_lelang'     => 'pending',
            'catatan_owner'     => null,
        ]);

        $lelang->transaksiRahn->update(['status' => 'lelang_pending']);

        return redirect()->route('lelang.index', ['status' => 'pending'])
            ->with('success', 'Harga lelang direvisi dan dikirim ulang ke Owner.');
    }

    /**
     * Admin klik BAYAR → status TERJUAL + perhitungan final
     */
    public function bayar(Request $request, $id)
    {
        $lelang = Lelang::with('transaksiRahn')->findOrFail($id);

        if ($lelang->status_lelang !== 'aktif') {
            return back()->with('error', 'Hanya lelang aktif yang dapat dibayar.');
        }

        $request->validate([
            'pembeli'          => 'nullable|string',
            'telepon_pembeli'  => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $lelang) {
            $transaksi = $lelang->transaksiRahn;
            $harga     = floatval($lelang->harga_lelang);
            $biaya     = floatval($lelang->biaya_lelang);

            $totalKewajiban = $transaksi->sisa_pinjaman + $biaya;

            $kelebihan = 0;
            $kerugian  = 0;
            $sisaPinjamanSetelah = 0;

            if ($harga >= $totalKewajiban) {
                $kelebihan = $harga - $totalKewajiban;
            } else {
                $kerugian = $totalKewajiban - $harga;
                $sisaPinjamanSetelah = $kerugian;
            }

            $lelang->update([
                'status_lelang'      => 'terjual',
                'tanggal_terjual'    => now()->toDateString(),
                'pembeli'            => $request->pembeli,
                'telepon_pembeli'    => $request->telepon_pembeli,
                'sisa_untuk_nasabah' => $kelebihan,
                'sisa_dana_kembali'  => $kelebihan,
                'kerugian'           => $kerugian,
                'sisa_pinjaman'      => $sisaPinjamanSetelah,
            ]);

            $transaksi->update([
                'status'        => 'lelang_terjual',
                'sisa_pinjaman' => $sisaPinjamanSetelah,
            ]);

            return redirect()->route('lelang.hasil', $lelang->id)
                ->with('success', 'Lelang berhasil dicatat sebagai TERJUAL!');
        });
    }

    /**
     * Owner batalkan lelang aktif → kembali ke Admin untuk revisi
     */
    public function batalkan(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['owner', 'superadmin'])) {
            abort(403, 'Hanya Owner/Superadmin yang dapat membatalkan lelang.');
        }

        $lelang = Lelang::findOrFail($id);
        if ($lelang->status_lelang !== 'aktif') {
            return back()->with('error', 'Hanya lelang aktif yang dapat dibatalkan.');
        }

        $lelang->update([
            'status_lelang' => 'dibatalkan',
            'catatan_owner' => $request->catatan_owner ?? 'Barang tidak terjual. Silakan revisi harga.',
            'approved_by'   => null,
            'approved_at'   => null,
        ]);

        $lelang->transaksiRahn->update(['status' => 'aktif']);

        return redirect()->route('lelang.index')
            ->with('success', 'Lelang dibatalkan. Dikembalikan ke Admin untuk revisi harga.');
    }

    /**
     * Halaman hasil/nota lelang
     */
    public function hasil($id)
    {
        $lelang = Lelang::with('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser')
            ->findOrFail($id);
        return view('lelang.hasil', compact('lelang'));
    }

    /**
     * Generate PDF Nota Lelang
     */
    public function cetakPdf(Lelang $lelang)
    {
        $lelang->load('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lelang.pdf', compact('lelang'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Nota-Lelang-' . $lelang->no_lelang . '.pdf');
    }
}
