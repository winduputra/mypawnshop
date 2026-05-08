<?php

namespace App\Exports;

use App\Models\TransaksiRahn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PinjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return TransaksiRahn::with('nasabah')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'No. Transaksi', 'Nasabah', 'Telepon', 'Tanggal', 'Tenor (Hari)', 'Total Taksiran', 'Total Pinjaman', 'Sisa Pinjaman', 'Biaya Admin', 'Ijarah', 'Jatuh Tempo', 'Status'];
    }

    public function map($t): array
    {
        static $no = 0;
        $no++;
        return [
            $no, $t->no_transaksi, $t->nasabah->nama ?? '-', $t->nasabah->telepon ?? '-',
            $t->tanggal_transaksi, $t->tenor_hari,
            $t->total_taksiran, $t->total_pinjaman, $t->sisa_pinjaman,
            $t->biaya_admin, $t->biaya_penitipan, $t->tanggal_jatuh_tempo, ucfirst($t->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]]];
    }

    public function title(): string { return 'Laporan Pinjaman'; }
}
