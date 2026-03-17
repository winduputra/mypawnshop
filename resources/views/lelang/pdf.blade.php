<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Lelang - {{ $lelang->transaksiRahn->no_transaksi }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; }
        .section-title { font-size: 14px; font-weight: bold; background-color: #f0f0f0; padding: 5px; margin: 15px 0 10px; border-left: 3px solid #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px; text-align: left; vertical-align: top; }
        .info-table td { border-bottom: 1px dotted #ccc; }
        .info-table td:first-child { font-weight: bold; width: 40%; }
        .item-table th, .item-table td { border: 1px solid #ddd; }
        .item-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; }
        .signature { float: right; width: 250px; text-align: center; }
        .signature p.name { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        .summary-box { background-color: #f9f9f9; border: 1px solid #ccc; padding: 10px; border-radius: 5px; margin-top: 15px; }
        .summary-box td { border-bottom: none; }
        .highlight { font-weight: bold; font-size: 13px; }
        .text-red { color: #d00; }
        .text-green { color: #008800; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">MyPawnShop</div>
        <div>Jl. Contoh Pegadaian No. 123, Kota Pegadaian</div>
        <div>Telp: 0812-3456-7890 | Email: cs@mypawnshop.com</div>
    </div>

    <div class="text-center">
        <div class="title">Berita Acara Eksekusi Lelang</div>
        <div>No. Transaksi: <strong>{{ $lelang->transaksiRahn->no_transaksi }}</strong></div>
    </div>

    <div class="section-title">A. Data Asal Transaksi</div>
    <table class="info-table">
        <tr>
            <td>Nama Nasabah (Rahin)</td>
            <td>: {{ $lelang->transaksiRahn->nasabah->nama }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: {{ $lelang->transaksiRahn->nasabah->nik }}</td>
        </tr>
        <tr>
            <td>Tanggal Jatuh Tempo</td>
            <td>: {{ \Carbon\Carbon::parse($lelang->transaksiRahn->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Total Hutang Terakhir (Pokok + Ujrah)</td>
            <td>: Rp {{ number_format($lelang->transaksiRahn->sisa_pinjaman + $lelang->biaya_lelang, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="section-title">B. Data Barang yang Dilelang (Marhun)</div>
    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Keterangan</th>
                <th>Taksiran Awal</th>
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

    <div class="section-title">C. Hasil Pelaksanaan Lelang</div>
    <table class="info-table">
        <tr>
            <td>Tanggal Lelang</td>
            <td>: {{ \Carbon\Carbon::parse($lelang->tanggal_lelang)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Nama Pembeli</td>
            <td>: {{ $lelang->pembeli }}</td>
        </tr>
        <tr>
            <td>Harga Terjual (Hasil Lelang)</td>
            <td class="highlight">: Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <table class="info-table" style="margin-bottom: 0;">
            <tr>
                <td style="width: 60%;">1. Pokok Sisa Pinjaman</td>
                <td class="text-right">Rp {{ number_format($lelang->transaksiRahn->total_pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>2. Biaya Lelang / Administrasi</td>
                <td class="text-right">Rp {{ number_format($lelang->biaya_lelang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 2px solid #333;"><strong>Total Kewajiban Nasabah (1+2)</strong></td>
                <td class="text-right" style="border-bottom: 2px solid #333;"><strong>Rp {{ number_format($lelang->transaksiRahn->total_pinjaman + $lelang->biaya_lelang, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Hasil Penjualan Lelang</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($lelang->harga_lelang, 0, ',', '.') }}</strong></td>
            </tr>
            
            @if($lelang->sisa_untuk_nasabah > 0)
            <tr>
                <td class="text-green highlight" style="padding-top: 10px;">> Uang Kelebihan (Hak Nasabah)</td>
                <td class="text-right text-green highlight" style="padding-top: 10px;">Rp {{ number_format($lelang->sisa_untuk_nasabah, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($lelang->kerugian > 0)
            <tr>
                <td class="text-red highlight" style="padding-top: 10px;">> Kerugian (Sisa Kewajiban Nasabah)</td>
                <td class="text-right text-red highlight" style="padding-top: 10px;">Rp {{ number_format($lelang->kerugian, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            @if($lelang->sisa_untuk_nasabah == 0 && $lelang->kerugian == 0)
            <tr>
                <td class="highlight" style="padding-top: 10px;">> Sisa Tagihan / Kelebihan</td>
                <td class="text-right highlight" style="padding-top: 10px;">Rp 0 (Lunas / Impas)</td>
            </tr>
            @endif
        </table>
    </div>

    <div style="margin-top: 20px; font-size: 11px; text-align: justify; color: #555;">
        <p>Demikian Berita Acara Eksekusi Lelang ini dibuat dengan sebenar-benarnya sesuai dengan ketentuan yang berlaku. @if($lelang->sisa_untuk_nasabah > 0) Uang kelebihan lelang sebesar Rp {{ number_format($lelang->sisa_untuk_nasabah, 0, ',', '.') }} dapat diambil oleh pihak Nasabah (Rahin) di kantor Pegadaian. @endif</p>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Petugas Lelang / Kasir,</p>
            <p class="name">{{ $lelang->user->name }}</p>
            <p>NIP: {{ str_pad($lelang->user->id, 6, "0", STR_PAD_LEFT) }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
