<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Angsuran ke-{{ $angsuranKe }} - {{ $transaksi->no_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.6; color: #1a1a1a; padding: 30px 40px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 2px; margin-bottom: 3px; }
        .header h2 { font-size: 14px; font-weight: bold; margin-bottom: 3px; color: #d97706; }
        .header p { font-size: 10px; color: #555; }
        .nota-number { text-align: right; font-size: 10px; margin-bottom: 10px; }
        .nota-number strong { font-size: 12px; color: #333; }
        .section-title { font-weight: bold; font-size: 12px; margin: 15px 0 8px; padding: 5px 10px; background: #f0f0f0; border-left: 4px solid #d97706; }
        .info table { width: 100%; margin-left: 20px; }
        .info td { padding: 3px 5px; vertical-align: top; }
        .info td:first-child { width: 200px; font-weight: bold; }
        .payment-box { text-align: center; margin: 20px 0; padding: 15px; border: 2px solid #d97706; border-radius: 10px; background: #fffbeb; }
        .payment-box h2 { font-size: 20px; color: #d97706; }
        .payment-box .amount { font-size: 28px; font-weight: bold; color: #1a1a1a; margin: 5px 0; }
        .payment-box .angsuran-ke { font-size: 14px; font-weight: bold; color: #92400e; margin-bottom: 5px; }
        .sisa-box { text-align: center; margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 8px; }
        table.items { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; font-size: 10px; }
        table.items th { background: #f5f5f5; font-weight: bold; text-transform: uppercase; }
        .right { text-align: right; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-section table { width: 100%; margin-top: 25px; }
        .signature-section td { width: 50%; text-align: center; padding: 10px; vertical-align: top; }
        .sign-line { border-bottom: 1px solid #333; margin: 50px auto 5px; width: 160px; }
        .sign-name { font-weight: bold; font-size: 11px; }
        .sign-dto { font-size: 9px; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>HARMANS GADAI SYARIAH</h1>
        <h2>BUKTI PEMBAYARAN ANGSURAN</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Indonesia | Telp: (021) 123-4567</p>
    </div>

    {{-- Nota info --}}
    <div class="nota-number">
        No. Kontrak: <strong>{{ $transaksi->no_transaksi }}</strong><br>
        No. Register Akad: <strong>{{ $transaksi->no_register_akad ?? '-' }}</strong><br>
        Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

    {{-- Payment box with Angsuran ke- --}}
    <div class="payment-box">
        <p class="angsuran-ke">ANGSURAN KE-{{ $angsuranKe }}</p>
        <h2>PEMBAYARAN DITERIMA</h2>
        <p class="amount">Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</p>
        <p style="font-size: 10px; color: #92400e;">Tanggal Bayar: {{ \Carbon\Carbon::parse($angsuran->tanggal_bayar)->format('d F Y') }}</p>
    </div>

    {{-- Sisa pinjaman setelah bayar --}}
    <div class="sisa-box">
        <p style="font-size: 10px; color: #555;">SISA POKOK PINJAMAN SETELAH PEMBAYARAN INI</p>
        <p style="font-size: 20px; font-weight: bold; color: {{ $angsuran->sisa_pinjaman <= 0 ? '#16a34a' : '#1a1a1a' }}">
            Rp {{ number_format($angsuran->sisa_pinjaman, 0, ',', '.') }}
            @if($angsuran->sisa_pinjaman <= 0) (LUNAS) @endif
        </p>
    </div>

    {{-- WAJIB: Nama Customer --}}
    <div class="section-title">DATA NASABAH</div>
    <div class="info">
        <table>
            <tr><td>Nama</td><td>: <strong>{{ $transaksi->nasabah->nama }}</strong></td></tr>
            <tr><td>NIK</td><td>: {{ $transaksi->nasabah->nik }}</td></tr>
            <tr><td>Telepon</td><td>: {{ $transaksi->nasabah->telepon }}</td></tr>
        </table>
    </div>

    {{-- WAJIB: Barang Jaminan --}}
    <div class="section-title">BARANG JAMINAN (MARHUN)</div>
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
                <td class="right">Rp {{ number_format($d->taksiran_item, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($d->pinjaman_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- WAJIB: Pokok Pinjaman, Nilai Angsuran, Sisa, Jatuh Tempo --}}
    <div class="section-title">RINCIAN PEMBAYARAN ANGSURAN</div>
    <div class="info">
        <table>
            <tr><td>Pokok Pinjaman</td><td>: <strong>Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Sisa Sebelum Bayar</td><td>: Rp {{ number_format($angsuran->sisa_pinjaman + $angsuran->jumlah_bayar, 0, ',', '.') }}</td></tr>
            <tr><td>Nilai Angsuran (ke-{{ $angsuranKe }})</td><td>: <strong>Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Sisa Pokok Pinjaman</td><td>: <strong>Rp {{ number_format($angsuran->sisa_pinjaman, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Jatuh Tempo</td><td>: <strong>{{ \Carbon\Carbon::parse($transaksi->tanggal_jatuh_tempo)->format('d F Y') }}</strong></td></tr>
            <tr><td>Status</td><td>: <strong style="color: #16a34a;">LUNAS</strong></td></tr>
            @if($angsuran->catatan)
            <tr><td>Catatan</td><td>: {{ $angsuran->catatan }}</td></tr>
            @endif
        </table>
    </div>

    {{-- WAJIB: Tanda Tangan Kasir (Otomatis dto) --}}
    <div class="signature-section">
        <table>
            <tr>
                <td>
                    <p>Kasir</p>
                    <div class="sign-line"></div>
                    <p class="sign-dto">dto (ditandatangani oleh)</p>
                    <p class="sign-name">{{ $angsuran->user->name ?? $transaksi->user->name }}</p>
                </td>
                <td>
                    <p>Nasabah</p>
                    <div class="sign-line"></div>
                    <p class="sign-dto">&nbsp;</p>
                    <p class="sign-name">{{ $transaksi->nasabah->nama }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Simpan bukti ini sebagai tanda terima pembayaran angsuran yang sah.</p>
        <p>No. Register Akad: {{ $transaksi->no_register_akad ?? '-' }} | Angsuran ke-{{ $angsuranKe }}</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
