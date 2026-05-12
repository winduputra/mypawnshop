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
        return \DB::transaction(function () use ($request, $transaksi) {
            $detail = $transaksi->detailTransaksi()->with('barang')->first();
            if (!$detail || !$detail->barang) {
                return redirect()->back()->with('error', 'Gagal, data barang tidak ditemukan pada transaksi ini.');
            }

            // ─── No extension limit for any category (emas, elektronik, kendaraan) ───

            $today = Carbon::today();
            $jatuhTempoLama = Carbon::parse($transaksi->tanggal_jatuh_tempo);
            $selisihHari = $jatuhTempoLama->diffInDays($today, false); // positive = overdue

            // ─── CASE 1: Overdue > 7 hari → Tolak (masuk periode lelang) ───
            if ($selisihHari > 7) {
                return redirect()->back()->with('error', 'Perpanjangan tidak dapat dilakukan. Sudah melewati batas 7 hari dari jatuh tempo, barang masuk periode lelang.');
            }

            $biayaDasar = $transaksi->biaya_admin + $transaksi->biaya_penitipan;
            $biayaMultiplier = $transaksi->metode_pembayaran === 'bayar_pelunasan' ? 2 : 1;

            // ─── CASE 2: Overdue 1-7 hari → Wajib bayar 2x biaya, dapat +50 hari ───
            if ($selisihHari > 0 && $selisihHari <= 7) {
                $tambahanHari = 50; // penalty: 50 instead of 60
                $biayaMultiplier = 2;
                $ujrahDibayar = $biayaDasar * $biayaMultiplier;
                $isOverdue = true;
                $catatan = "Perpanjangan overdue ({$selisihHari} hari lewat jatuh tempo). Bayar 2x biaya admin dan penitipan, mendapat +50 hari.";
            }
            // ─── CASE 3: Belum jatuh tempo → Normal +30 hari ───
            else {
                $tambahanHari = 30;
                $ujrahDibayar = $biayaDasar * $biayaMultiplier;
                $isOverdue = false;
                $catatan = $biayaMultiplier === 2
                    ? 'Biaya awal ditunda sampai pelunasan, sehingga perpanjangan membayar 2x biaya admin dan penitipan.'
                    : 'Biaya awal sudah dibayar/dipotong, sehingga perpanjangan membayar 1x biaya admin dan penitipan.';
            }

            // Hitung tanggal baru selalu dari jatuh tempo lama (bukan dari hari ini)
            $jatuhTempoBaru = $jatuhTempoLama->copy()->addDays($tambahanHari);
            $batasLelangBaru = $jatuhTempoBaru->copy()->addDays(7);

            // Auto-generate nomor nota
            $todayStr = now()->format('Ymd');
            $lastNota = Perpanjangan::where('no_nota', 'like', "NOTA-EXT-{$todayStr}-%")
                ->orderByDesc('no_nota')
                ->first();
            $seq = 1;
            if ($lastNota) {
                $parts = explode('-', $lastNota->no_nota);
                $seq = intval(end($parts)) + 1;
            }
            $noNota = "NOTA-EXT-{$todayStr}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $perpanjangan = Perpanjangan::create([
                'transaksi_rahn_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'no_nota' => $noNota,
                'tanggal_perpanjangan' => now()->toDateString(),
                'tambahan_tenor_hari' => $tambahanHari,
                'tanggal_jatuh_tempo_baru' => $jatuhTempoBaru->toDateString(),
                'ujrah_dibayar' => $ujrahDibayar,
                'is_overdue_extension' => $isOverdue,
                'catatan' => $catatan,
            ]);

            $transaksi->update([
                'tanggal_jatuh_tempo' => $jatuhTempoBaru->toDateString(),
                'tanggal_batas_lelang' => $batasLelangBaru->toDateString(),
                'status' => 'diperpanjang',
            ]);

            $message = $isOverdue
                ? "Perpanjangan overdue berhasil. Tenor ditambah {$tambahanHari} hari (bayar 2x biaya admin dan penitipan). Nota: {$noNota}"
                : "Tenor berhasil diperpanjang {$tambahanHari} hari (bayar {$biayaMultiplier}x biaya admin dan penitipan). Nota: {$noNota}";

            return redirect()->back()->with('success', $message)
                ->with('perpanjangan_id', $perpanjangan->id);
        });
    }

    public function cetakNota(TransaksiRahn $transaksi, Perpanjangan $perpanjangan)
    {
        $transaksi->load('nasabah', 'user', 'detailTransaksi.barang');
        $perpanjangan->load('user');
        
        $pdf = Pdf::loadView('transaksi.nota-perpanjangan-pdf', compact('transaksi', 'perpanjangan'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Nota-Perpanjangan-' . ($perpanjangan->no_nota ?? $perpanjangan->id) . '.pdf';
        return $pdf->download($filename);
    }
}
