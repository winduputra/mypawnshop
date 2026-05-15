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
            ->with('nasabah.cabang', 'detailTransaksi.barang');

        // Branch isolation
        if ($user->role === 'kasir' && $user->cabang_id) {
            $belumLelangQuery->whereHas('nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }

        $belumLelang = ($statusFilter === 'semua' || $statusFilter === 'baru')
            ? $belumLelangQuery->latest()->get()
            : collect();

        // ── 2. Record lelang yang sudah dibuat ──
        $lelangQuery = Lelang::with('transaksiRahn.nasabah.cabang', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser', 'ownerEditedByUser');

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
        $lelang = request()->boolean('transaksi') ? null : Lelang::with('transaksiRahn.nasabah.cabang', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser', 'ownerEditedByUser')
            ->find($id);

        if ($lelang) {
            return view('lelang.show', compact('lelang'));
        }

        // Jika belum ada record lelang, tampilkan form buat baru
        $transaksi = TransaksiRahn::with('nasabah.cabang', 'detailTransaksi.barang')->findOrFail($id);

        return view('lelang.show', compact('transaksi'));
    }

    /**
     * Admin membuat record lelang baru (draft) dan langsung kirim ke Owner
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner', 'superadmin'])) {
            abort(403, 'Kasir hanya dapat melihat data lelang.');
        }

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
            $totalKewajiban = $transaksi->sisa_pinjaman + $biaya + $ijarah;
            $sisaDana = max(0, $harga - $totalKewajiban);

            $lelang = Lelang::create([
                'no_lelang'          => Lelang::generateNoLelang(),
                'transaksi_rahn_id'  => $transaksi->id,
                'user_id'            => Auth::id(),
                'harga_lelang'       => $harga,
                'biaya_lelang'       => $biaya,
                'ijarah'             => $ijarah,
                'sisa_pinjaman'      => $transaksi->sisa_pinjaman,
                'sisa_untuk_nasabah' => $sisaDana,
                'sisa_dana_kembali'  => $sisaDana,
                'status_lelang'      => 'pending', // langsung kirim ke owner
            ]);

            $transaksi->update(['status' => 'lelang_pending']);
            $transaksi->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'lelang_submitted',
                'status_approval' => 'lelang_pending',
                'note' => "Data lelang {$lelang->no_lelang} dibuat dan dikirim ke Owner.",
            ]);

            return redirect()->route('lelang.index', ['status' => 'pending'])
                ->with('success', 'Data lelang berhasil dikirim ke Owner untuk approval.');
        });
    }

    /**
     * Admin kirim ulang lelang yang dibatalkan ke Owner (setelah revisi)
     */
    public function kirimKeOwner($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner', 'superadmin'])) {
            abort(403, 'Kasir hanya dapat melihat data lelang.');
        }

        $lelang = Lelang::findOrFail($id);

        if (!in_array($lelang->status_lelang, ['draft', 'dibatalkan'])) {
            return back()->with('error', 'Lelang tidak bisa dikirim ulang.');
        }

        $lelang->update([
            'status_lelang' => 'pending',
            'catatan_owner' => null,
        ]);

        $lelang->transaksiRahn->update([
            'status' => $lelang->status_lelang === 'aktif' ? 'lelang_aktif' : 'lelang_pending',
        ]);
        $lelang->transaksiRahn->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'lelang_resubmitted',
            'status_approval' => 'lelang_pending',
            'note' => "Lelang {$lelang->no_lelang} dikirim ulang ke Owner.",
        ]);

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
        $lelang->transaksiRahn->histories()->create([
            'user_id' => $user->id,
            'action' => 'lelang_approved',
            'status_approval' => 'lelang_aktif',
            'note' => "Lelang {$lelang->no_lelang} disetujui Owner dan aktif.",
        ]);

        return redirect()->route('lelang.index', ['status' => 'aktif'])
            ->with('success', 'Lelang disetujui! Barang sekarang dalam status AKTIF dilelang.');
    }

    /**
     * Owner tolak / minta revisi, tanpa mengembalikan edit ke Admin.
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
            'catatan_owner' => $request->catatan_owner ?? 'Owner meminta revisi data lelang.',
        ]);
        $lelang->transaksiRahn->histories()->create([
            'user_id' => $user->id,
            'action' => 'lelang_rejected',
            'status_approval' => 'lelang_pending',
            'note' => $request->catatan_owner ?? 'Owner meminta revisi data lelang.',
        ]);

        return redirect()->route('lelang.index')
            ->with('success', 'Lelang ditolak/revisi oleh Owner. Owner dapat langsung edit data lelang.');
    }

    /**
     * Owner edit data lelang langsung dan tercatat sistem.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'harga_lelang' => 'required|numeric|min:0',
            'biaya_lelang' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        if (!in_array($user->role, ['owner', 'superadmin'])) {
            abort(403, 'Hanya Owner/Superadmin yang dapat mengedit data lelang setelah dikirim.');
        }

        $lelang = Lelang::findOrFail($id);

        if (!in_array($lelang->status_lelang, ['pending', 'aktif'])) {
            return back()->with('error', 'Hanya lelang pending/aktif yang dapat diedit Owner.');
        }

        $harga = floatval($request->harga_lelang);
        $biaya = floatval($request->biaya_lelang);
        $ijarah = floatval($lelang->transaksiRahn->biaya_penitipan);
        $totalKewajiban = $lelang->sisa_pinjaman + $biaya + $ijarah;
        $sisaDana = max(0, $harga - $totalKewajiban);
        $log = $lelang->owner_edit_log ?? [];
        $log[] = [
            'edited_at' => now()->toDateTimeString(),
            'edited_by' => $user->id,
            'harga_lelang_lama' => (float) $lelang->harga_lelang,
            'harga_lelang_baru' => $harga,
            'biaya_lelang_lama' => (float) $lelang->biaya_lelang,
            'biaya_lelang_baru' => $biaya,
            'ijarah_lama' => (float) $lelang->ijarah,
            'ijarah_baru' => $ijarah,
        ];

        $lelang->update([
            'harga_lelang'       => $harga,
            'biaya_lelang'       => $biaya,
            'ijarah'             => $ijarah,
            'sisa_untuk_nasabah' => $sisaDana,
            'sisa_dana_kembali'  => $sisaDana,
            'catatan_owner'      => $request->catatan_owner,
            'owner_edited_by'    => $user->id,
            'owner_edited_at'    => now(),
            'owner_edit_count'   => ($lelang->owner_edit_count ?? 0) + 1,
            'owner_edit_log'     => $log,
            'status_lelang'      => 'aktif',
            'approved_by'        => $lelang->approved_by ?: $user->id,
            'approved_at'        => $lelang->approved_at ?: now(),
            'tanggal_lelang'     => $lelang->tanggal_lelang ?: now()->toDateString(),
        ]);

        $lelang->transaksiRahn->update(['status' => 'lelang_aktif']);
        $lelang->transaksiRahn->histories()->create([
            'user_id' => $user->id,
            'action' => 'lelang_owner_edited',
            'status_approval' => 'lelang_aktif',
            'note' => "Owner mengedit dan mengaktifkan lelang {$lelang->no_lelang}.",
        ]);

        return redirect()->route('lelang.index', ['status' => 'aktif'])
            ->with('success', 'Data lelang berhasil diedit Owner, aktif, dan tercatat oleh sistem.');
    }

    /**
     * Kasir klik BAYAR → status TERJUAL + perhitungan final
     */
    public function bayar(Request $request, $id)
    {
        if (auth()->user()->role !== 'kasir') {
            abort(403, 'Hanya kasir yang dapat mencatat penjualan lelang.');
        }

        $lelang = Lelang::with('transaksiRahn')->findOrFail($id);

        if ($lelang->status_lelang !== 'aktif') {
            return back()->with('error', 'Hanya lelang aktif yang dapat dibayar.');
        }

        $request->validate([
            'pembeli'          => 'required|string|max:255',
            'alamat_pembeli'   => 'required|string',
            'telepon_pembeli'  => 'required|string|max:50',
        ]);

        return DB::transaction(function () use ($request, $lelang) {
            $transaksi = $lelang->transaksiRahn;
            $harga     = floatval($lelang->harga_lelang);
            $biaya     = floatval($lelang->biaya_lelang);

            $totalKewajiban = $transaksi->sisa_pinjaman + $biaya + floatval($lelang->ijarah);

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
                'alamat_pembeli'     => $request->alamat_pembeli,
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
            $transaksi->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'lelang_sold',
                'status_approval' => 'lelang_terjual',
                'note' => "Lelang {$lelang->no_lelang} terjual kepada {$request->pembeli}.",
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
        return back()->with('error', 'Pembatalan lelang ke Admin sudah dinonaktifkan. Owner dapat edit data lelang langsung.');
    }

    /**
     * Halaman hasil/nota lelang
     */
    public function hasil($id)
    {
        $lelang = Lelang::with('transaksiRahn.nasabah.cabang', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser')
            ->findOrFail($id);
        return view('lelang.hasil', compact('lelang'));
    }

    /**
     * Generate PDF Nota Lelang
     */
    public function cetakPdf(Lelang $lelang)
    {
        $lelang->load('transaksiRahn.nasabah.cabang', 'transaksiRahn.detailTransaksi.barang', 'user', 'approvedByUser');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lelang.pdf', compact('lelang'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Nota-Lelang-' . $lelang->no_lelang . '.pdf');
    }
}
