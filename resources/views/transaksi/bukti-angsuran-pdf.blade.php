<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Angsuran - {{ $transaksi->no_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.6; color: #1a1a1a; padding: 30px 40px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 2px; margin-bottom: 3px; }
        .header h2 { font-size: 14px; font-weight: bold; margin-bottom: 3px; color: #d97706; }
        .header p { font-size: 10px; color: #555; }
        .section-title { font-weight: bold; font-size: 12px; margin: 15px 0 8px; padding: 5px 10px; background: #f0f0f0; border-left: 4px solid #d97706; }
        .info table { width: 100%; margin-left: 20px; }
        .info td { padding: 2px 5px; vertical-align: top; }
        .info td:first-child { width: 160px; font-weight: bold; }
        .payment-box { text-align: center; margin: 20px 0; padding: 15px; border: 2px solid #d97706; border-radius: 10px; background: #fffbeb; }
        .payment-box h2 { font-size: 20px; color: #d97706; }
        .payment-box .amount { font-size: 28px; font-weight: bold; color: #1a1a1a; margin: 5px 0; }
        .sisa-box { text-align: center; margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 8px; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-section table { width: 100%; margin-top: 25px; }
        .signature-section td { width: 50%; text-align: center; padding: 10px; vertical-align: top; }
        .sign-line { border-bottom: 1px solid #333; margin: 50px auto 5px; width: 160px; }
        .sign-name { font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyPawnShop</h1>
        <h2>BUKTI PEMBAYARAN ANGSURAN</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Indonesia | Telp: (021) 123-4567</p>
    </div>

    <div class="payment-box">
        <h2>ANGSURAN DITERIMA</h2>
        <p class="amount">Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</p>
        <p style="font-size: 10px; color: #92400e;">Tanggal: {{ $angsuran->tanggal_bayar }}</p>
    </div>

    <div class="sisa-box">
        <p style="font-size: 10px; color: #555;">SISA PINJAMAN SETELAH PEMBAYARAN INI</p>
        <p style="font-size: 20px; font-weight: bold; color: {{ $angsuran->sisa_pinjaman <= 0 ? '#16a34a' : '#1a1a1a' }}">
            Rp {{ number_format($angsuran->sisa_pinjaman, 0, ',', '.') }}
            @if($angsuran->sisa_pinjaman <= 0) (LUNAS) @endif
        </p>
    </div>

    <div class="section-title">DATA TRANSAKSI</div>
    <div class="info">
        <table>
            <tr><td>No. Kontrak</td><td>: {{ $transaksi->no_transaksi }}</td></tr>
            <tr><td>Total Pinjaman Pokok</td><td>: Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Sebelum Bayar</td><td>: Rp {{ number_format($angsuran->sisa_pinjaman + $angsuran->jumlah_bayar, 0, ',', '.') }}</td></tr>
            <tr><td>Jumlah Dibayar</td><td>: Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Setelah Bayar</td><td>: Rp {{ number_format($angsuran->sisa_pinjaman, 0, ',', '.') }}</td></tr>
            @if($angsuran->catatan)
            <tr><td>Catatan</td><td>: {{ $angsuran->catatan }}</td></tr>
            @endif
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

    <div class="signature-section">
        <table>
            <tr>
                <td><p>Kasir</p><div class="sign-line"></div><p class="sign-name">{{ $angsuran->user->name ?? $transaksi->user->name }}</p></td>
                <td><p>Nasabah</p><div class="sign-line"></div><p class="sign-name">{{ $transaksi->nasabah->nama }}</p></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Simpan bukti ini sebagai tanda terima pembayaran angsuran yang sah.</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
