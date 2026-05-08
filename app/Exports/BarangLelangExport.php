<?php

namespace App\Exports;

use App\Models\Lelang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangLelangExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $status;

    public function __construct(?string $status = null)
    {
        $this->status = $status; // 'terjual' or 'belum'
    }

    public function collection()
    {
        $query = Lelang::with('transaksiRahn.nasabah', 'transaksiRahn.detailTransaksi.barang');

        if ($this->status === 'terjual') {
            $query->where('status_lelang', 'terjual');
        } elseif ($this->status === 'belum') {
            $query->whereIn('status_lelang', ['pending', 'aktif', 'dibatalkan', 'draft']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['No', 'ID Lelang', 'No. Transaksi', 'Nasabah', 'Barang', 'Harga Jual Lelang', 'Biaya Admin Lelang', 'Sisa Dana Kembali', 'Status', 'Tanggal Terjual', 'Pembeli'];
    }

    public function map($l): array
    {
        static $no = 0;
        $no++;
        $barangList = $l->transaksiRahn->detailTransaksi->map(fn($dt) => $dt->barang->nama_barang)->implode(', ');

        return [
            $no, $l->no_lelang ?? '-', $l->transaksiRahn->no_transaksi ?? '-',
            $l->transaksiRahn->nasabah->nama ?? '-', $barangList,
            $l->harga_lelang, $l->biaya_lelang,
            max($l->sisa_dana_kembali, $l->sisa_untuk_nasabah),
            ucfirst($l->status_lelang), $l->tanggal_terjual ?? '-', $l->pembeli ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '084C35']]]];
    }

    public function title(): string
    {
        if ($this->status === 'terjual') return 'Lelang Terjual';
        if ($this->status === 'belum') return 'Lelang Belum Terjual';
        return 'Semua Lelang';
    }
}
