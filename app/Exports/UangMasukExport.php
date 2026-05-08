<?php

namespace App\Exports;

use App\Models\TransaksiRahn;
use App\Models\Perpanjangan;
use App\Models\Lelang;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UangMasukExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function array(): array
    {
        $rows = [];
        $no = 0;
        $totalIjarah = 0;
        $totalAdmin = 0;
        $totalAdminLelang = 0;

        // Ijarah dari transaksi baru (biaya_penitipan)
        $transaksi = TransaksiRahn::whereIn('status_approval', ['disetujui'])
            ->select('no_transaksi', 'tanggal_transaksi', 'biaya_penitipan', 'biaya_admin')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        foreach ($transaksi as $t) {
            $no++;
            $rows[] = [$no, $t->no_transaksi, $t->tanggal_transaksi, 'Transaksi Gadai', $t->biaya_penitipan, $t->biaya_admin, 0];
            $totalIjarah += $t->biaya_penitipan;
            $totalAdmin += $t->biaya_admin;
        }

        // Ijarah dari perpanjangan
        $perpanjangan = Perpanjangan::with('transaksiRahn')
            ->orderBy('tanggal_perpanjangan', 'desc')
            ->get();

        foreach ($perpanjangan as $p) {
            $no++;
            $rows[] = [$no, $p->transaksiRahn->no_transaksi ?? '-', $p->tanggal_perpanjangan, 'Perpanjangan', $p->ujrah_dibayar, 0, 0];
            $totalIjarah += $p->ujrah_dibayar;
        }

        // Biaya Admin Lelang
        $lelangTerjual = Lelang::with('transaksiRahn')
            ->where('status_lelang', 'terjual')
            ->orderBy('tanggal_terjual', 'desc')
            ->get();

        foreach ($lelangTerjual as $l) {
            $no++;
            $rows[] = [$no, $l->transaksiRahn->no_transaksi ?? '-', $l->tanggal_terjual, 'Lelang Terjual', 0, 0, $l->biaya_lelang];
            $totalAdminLelang += $l->biaya_lelang;
        }

        // Total row
        $rows[] = ['', '', '', 'TOTAL', $totalIjarah, $totalAdmin, $totalAdminLelang];

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'No. Transaksi', 'Tanggal', 'Sumber', 'Ijarah (Biaya Penitipan)', 'Biaya Admin', 'Biaya Admin Lelang'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]],
            $lastRow => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8F5E9']]],
        ];
    }

    public function title(): string { return 'Uang Masuk'; }
}
