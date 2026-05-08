<?php

namespace App\Exports;

use App\Models\DetailTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $kategori;

    public function __construct(?string $kategori = null)
    {
        $this->kategori = $kategori;
    }

    public function collection()
    {
        return DetailTransaksi::whereHas('transaksiRahn', fn($q) => $q->whereIn('status', ['aktif', 'diperpanjang', 'lelang_aktif', 'lelang_pending']))
            ->with('barang.nasabah', 'transaksiRahn')
            ->when($this->kategori, fn($q) => $q->whereHas('barang', fn($q2) => $q2->where('kategori', $this->kategori)))
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Kategori', 'Nama Barang', 'Merk/Type', 'Nomor Seri', 'Kondisi', 'Berat', 'Pemilik (Nasabah)', 'No. Transaksi', 'Taksiran', 'Pinjaman', 'Status Transaksi'];
    }

    public function map($dt): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            ucfirst($dt->barang->kategori ?? '-'),
            $dt->barang->nama_barang,
            $dt->barang->merk_type,
            $dt->barang->nomor_seri,
            $dt->barang->kondisi_fisik,
            $dt->barang->berat,
            $dt->barang->nasabah->nama ?? '-',
            $dt->transaksiRahn->no_transaksi ?? '-',
            $dt->taksiran_item,
            $dt->pinjaman_item,
            ucfirst($dt->transaksiRahn->status ?? '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]]];
    }

    public function title(): string
    {
        return $this->kategori ? 'Stock ' . ucfirst($this->kategori) : 'Stock Opname';
    }
}
