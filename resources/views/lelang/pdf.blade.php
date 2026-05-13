<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Lelang - {{ $lelang->no_lelang }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #084C35; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #084C35; margin-bottom: 5px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; color: #084C35; }
        .section-title { font-size: 14px; font-weight: bold; background-color: #f0f7f4; padding: 5px 10px; margin: 15px 0 10px; border-left: 3px solid #084C35; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px; text-align: left; vertical-align: top; }
        .info-table td { border-bottom: 1px dotted #ccc; }
        .info-table td:first-child { font-weight: bold; width: 40%; }
        .item-table th, .item-table td { border: 1px solid #ddd; }
        .item-table th { background-color: #f0f7f4; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; }
        .signature { float: right; width: 250px; text-align: center; }
        .signature-left { float: left; width: 250px; text-align: center; }
        .signature p.name { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        .summary-box { background-color: #f0f7f4; border: 1px solid #084C35; padding: 10px; border-radius: 5px; margin-top: 15px; }
        .summary-box td { border-bottom: none; }
        .highlight { font-weight: bold; font-size: 13px; }
        .text-red { color: #d00; }
        .text-green { color: #084C35; }
        .nota-id { font-size: 18px; font-weight: bold; color: #084C35; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">HARMANS GADAI SYARIAH</div>
        <div>Jl. Contoh Pegadaian No. 123, Kota Pegadaian</div>
        <div>Telp: 0812-3456-7890 | Email: cs@harmansgadai.com</div>
    </div>

    <div class="text-center">
        <div class="title">Nota Lelang</div>
        <div class="nota-id">{{ $lelang->no_lelang }}</div>
    </div>

    <div class="section-title">A. Data Pinjaman</div>
    <table class="info-table">
        <tr>
            <td>No. Transaksi</td>
            <td>: {{ $lelang->transaksiRahn->no_transaksi }}</td>
        </tr>
        <tr>
            <td>Nama Nasabah (Rahin)</td>
            <td>: {{ $lelang->transaksiRahn->nasabah->nama }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: {{ $lelang->transaksiRahn->nasabah->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Cabang Asal Barang</td>
            <td>: {{ $lelang->transaksiRahn->nasabah->cabang->nama_cabang ?? $lelang->transaksiRahn->nasabah->cabang->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Jatuh Tempo</td>
            <td>: {{ \Carbon\Carbon::parse($lelang->transaksiRahn->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="section-title">B. Barang Jaminan (Marhun)</div>
    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Keterangan</th>
                <th>Taksiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lelang->transaksiRahn->detailTransaksi as $index => $dt)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center" style="text-transform: capitalize;">{{ $dt->barang->kategori }}</td>
                <td>{{ $dt->barang->nama_barang }}</td>
                <td>{{ $dt->barang->keterangan }}</td>
                <td class="text-right">Rp {{ number_format($dt->taksiran_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">C. Detail Nota Lelang</div>
    <div class="summary-box">
        <table class="info-table" style="margin-bottom: 0;">
            <tr>
                <td>ID Lelang</td>
                <td class="text-right highlight">{{ $lelang->no_lelang }}</td>
            </tr>
            <tr>
                <td>Tanggal Terjual</td>
                <td class="text-right">{{ $lelang->tanggal_terjual ? \Carbon\Carbon::parse($lelang->tanggal_terjual)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Harga Jual Lelang</td>
                <td class="text-right highlight">Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Admin Lelang</td>
                <td class="text-right">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pokok Pinjaman</td>
                <td class="text-right">Rp {{ number_format($lelang->transaksiRahn->total_pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa Pokok Pinjaman</td>
                <td class="text-right">Rp {{ number_format($lelang->sisa_pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 2px solid #084C35;"><strong>Total Kewajiban</strong></td>
                <td class="text-right" style="border-bottom: 2px solid #084C35;"><strong>Rp {{ number_format($lelang->sisa_pinjaman + $lelang->biaya_lelang, 0, ',', '.') }}</strong></td>
            </tr>

            @if(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah) > 0)
            <tr>
                <td class="text-green highlight" style="padding-top: 10px;">▸ Sisa Dana Kembali (Hak Nasabah)</td>
                <td class="text-right text-green highlight" style="padding-top: 10px;">Rp {{ number_format(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah), 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($lelang->kerugian > 0)
            <tr>
                <td class="text-red highlight" style="padding-top: 10px;">▸ Kerugian (Sisa Kewajiban Nasabah)</td>
                <td class="text-right text-red highlight" style="padding-top: 10px;">Rp {{ number_format($lelang->kerugian, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah) == 0 && $lelang->kerugian == 0)
            <tr>
                <td class="highlight" style="padding-top: 10px;">▸ Sisa</td>
                <td class="text-right highlight" style="padding-top: 10px;">Rp 0 (Lunas / Impas)</td>
            </tr>
            @endif
        </table>
    </div>

    @if($lelang->pembeli)
    <div style="margin-top: 15px;">
        <table class="info-table">
            <tr><td>Pembeli</td><td>: {{ $lelang->pembeli }}</td></tr>
            <tr><td>Alamat Pembeli</td><td>: {{ $lelang->alamat_pembeli ?? '-' }}</td></tr>
            @if($lelang->telepon_pembeli)
            <tr><td>Telepon Pembeli</td><td>: {{ $lelang->telepon_pembeli }}</td></tr>
            @endif
        </table>
    </div>
    @endif

    <div style="margin-top: 20px; font-size: 11px; text-align: justify; color: #555;">
        <p>Demikian Nota Lelang ini dibuat dengan sebenar-benarnya sesuai dengan ketentuan yang berlaku.
        @if(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah) > 0)
        Sisa dana kembali sebesar Rp {{ number_format(max($lelang->sisa_dana_kembali, $lelang->sisa_untuk_nasabah), 0, ',', '.') }} dapat diambil oleh pihak Nasabah (Rahin) di kantor Pegadaian.
        @endif
        </p>
    </div>

    <div class="footer">
        <div class="signature-left">
            <p>Disetujui oleh,</p>
            <p class="name">{{ $lelang->approvedByUser->name ?? '-' }}</p>
            <p>Owner</p>
        </div>
        <div class="signature">
            <p>Petugas Lelang,</p>
            <p class="name">{{ $lelang->user->name }}</p>
            <p>NIP: {{ str_pad($lelang->user->id, 6, "0", STR_PAD_LEFT) }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
