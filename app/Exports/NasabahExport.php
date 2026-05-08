<?php

namespace App\Exports;

use App\Models\Nasabah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NasabahExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Nasabah::with('cabang')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama', 'Telepon', 'WhatsApp', 'Email', 'Alamat', 'Alamat Domisili', 'Pekerjaan', 'Status Pernikahan', 'Nama Ibu Kandung', 'Bank', 'No. Rekening', 'Pemilik Rekening', 'Cabang'];
    }

    public function map($n): array
    {
        static $no = 0;
        $no++;
        return [
            $no, $n->nik, $n->nama, $n->telepon, $n->no_wa, $n->email,
            $n->alamat, $n->alamat_domisili, $n->pekerjaan, $n->status_pernikahan,
            $n->nama_ibu_kandung, $n->nama_bank, $n->no_rekening, $n->nama_pemilik_rekening,
            $n->cabang->nama_cabang ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]]];
    }

    public function title(): string { return 'Data Nasabah'; }
}
