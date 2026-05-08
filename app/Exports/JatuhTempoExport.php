<?php

namespace App\Exports;

use App\Models\TransaksiRahn;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JatuhTempoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang'])
            ->with('nasabah')
            ->orderBy('tanggal_jatuh_tempo')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'No. Transaksi', 'Nasabah', 'Telepon', 'Total Pinjaman', 'Sisa Pinjaman', 'Jatuh Tempo', 'Sisa Hari', 'Status'];
    }

    public function map($t): array
    {
        static $no = 0;
        $no++;
        $sisaHari = Carbon::now()->diffInDays(Carbon::parse($t->tanggal_jatuh_tempo), false);
        $label = $sisaHari >= 0 ? $sisaHari . ' hari lagi' : abs($sisaHari) . ' hari lewat';

        return [
            $no, $t->no_transaksi, $t->nasabah->nama ?? '-', $t->nasabah->telepon ?? '-',
            $t->total_pinjaman, $t->sisa_pinjaman,
            $t->tanggal_jatuh_tempo, $label, ucfirst($t->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]]];
    }

    public function title(): string { return 'Nasabah Jatuh Tempo'; }
}
