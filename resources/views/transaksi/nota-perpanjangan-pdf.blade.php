<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Perpanjangan - {{ $transaksi->no_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.6; color: #1a1a1a; padding: 30px 40px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 2px; margin-bottom: 3px; }
        .header h2 { font-size: 14px; font-weight: bold; margin-bottom: 3px; color: #4f46e5; }
        .header p { font-size: 10px; color: #555; }
        .section-title { font-weight: bold; font-size: 12px; margin: 15px 0 8px; padding: 5px 10px; background: #f0f0f0; border-left: 4px solid #4f46e5; }
        .info table { width: 100%; margin-left: 20px; }
        .info td { padding: 4px 5px; vertical-align: top; }
        .info td:first-child { width: 180px; font-weight: bold; }
        .lunas-badge { text-align: center; margin: 20px 0; padding: 15px; border: 3px solid #4f46e5; border-radius: 10px; background: #eef2ff; }
        .lunas-badge h2 { font-size: 20px; color: #4f46e5; }
        table.items { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; font-size: 10px; }
        table.items th { background: #f5f5f5; font-weight: bold; text-transform: uppercase; }
        .right { text-align: right; }
        .total-row { font-weight: bold; background: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-section table { width: 100%; margin-top: 30px; }
        .signature-section td { width: 50%; text-align: center; padding: 10px; vertical-align: top; }
        .sign-line { border-bottom: 1px solid #333; margin: 50px auto 5px; width: 160px; }
        .sign-name { font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyPawnShop</h1>
        <h2>NOTA PERPANJANGAN TENOR</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Indonesia | Telp: (021) 123-4567</p>
    </div>

    <div class="lunas-badge">
        <h2>PERPANJANGAN BERHASIL</h2>
        <p style="font-size: 12px; color: #4338ca;">Tenor transaksi Anda telah diperpanjang {{ $perpanjangan->tambahan_tenor_hari }} hari.</p>
    </div>

    <div class="section-title">DATA TRANSAKSI</div>
    <div class="info">
        <table>
            <tr><td>No. Kontrak</td><td>: {{ $transaksi->no_transaksi }}</td></tr>
            <tr><td>Tanggal Transaksi</td><td>: {{ $transaksi->tanggal_transaksi }}</td></tr>
            <tr><td>Tanggal Jatuh Tempo Lama</td><td>: {{ \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)->subDays($perpanjangan->tambahan_tenor_hari)->toDateString() }}</td></tr>
            <tr><td><strong>Tanggal Jatuh Tempo Baru</strong></td><td><strong>: {{ $perpanjangan->tanggal_jatuh_tempo_baru }}</strong></td></tr>
        </table>
    </div>

    <div class="section-title">DATA NASABAH</div>
    <div class="info">
        <table>
            <tr><td>Nama</td><td>: {{ $transaksi->nasabah->nama }}</td></tr>
            <tr><td>NIK</td><td>: {{ $transaksi->nasabah->nik }}</td></tr>
            <tr><td>Telepon</td><td>: {{ $transaksi->nasabah->telepon }}</td></tr>
        </table>
    </div>

    <div class="section-title">BARANG JAMINAN</div>
    <table class="items">
        <thead>
            <tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th class="right">Taksiran</th><th class="right">Pinjaman</th></tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksi as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->barang->nama_barang }}</td>
                <td style="text-transform: capitalize;">{{ $d->barang->kategori }}</td>
                <td class="right">{{ number_format($d->taksiran_item, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->pinjaman_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">RINCIAN BIAYA PERPANJANGAN</div>
    <div class="info">
        <table>
            <tr><td>Tanggal Pembayaran</td><td>: {{ $perpanjangan->tanggal_perpanjangan }}</td></tr>
            <tr><td>Biaya Penitipan {{ $perpanjangan->tambahan_tenor_hari }} Hari</td><td>: Rp {{ number_format($perpanjangan->ujrah_dibayar, 0, ',', '.') }}</td></tr>
            <tr><td>Status</td><td>: <strong style="color: #16a34a;">LUNAS</strong></td></tr>
            @if($perpanjangan->catatan)
            <tr><td>Catatan</td><td>: {{ $perpanjangan->catatan }}</td></tr>
            @endif
        </table>
    </div>

    <div class="signature-section">
        <table>
            <tr>
                <td><p>Kasir</p><div class="sign-line"></div><p class="sign-name">{{ $perpanjangan->user->name }}</p></td>
                <td><p>Nasabah</p><div class="sign-line"></div><p class="sign-name">{{ $transaksi->nasabah->nama }}</p></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Simpan nota ini sebagai bukti perpanjangan jatuh tempo yang sah.</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
