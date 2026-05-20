@php
    $logoPath = public_path('images/logo.jpeg');
    $logoExists = file_exists($logoPath);
    $nasabah = $transaksi->nasabah;
    $nomorNota = $perpanjangan->no_nota ?? ('PRJ-' . $transaksi->no_transaksi);
    $tanggalPerpanjangan = $perpanjangan->tanggal_perpanjangan
        ? \Carbon\Carbon::parse($perpanjangan->tanggal_perpanjangan)->translatedFormat('d F Y')
        : now()->translatedFormat('d F Y');
    $cabang = $nasabah->cabang->nama_cabang ?? '-';
    $nomorAkad = $transaksi->no_register_akad ?? $transaksi->no_transaksi;
    $jatuhTempoBaru = $perpanjangan->tanggal_jatuh_tempo_baru
        ? \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)
        : null;
    $jatuhTempoSebelumnya = $jatuhTempoBaru
        ? $jatuhTempoBaru->copy()->subDays($perpanjangan->tambahan_tenor_hari ?? 0)->translatedFormat('d F Y')
        : '-';
    $jatuhTempoBaruText = $jatuhTempoBaru ? $jatuhTempoBaru->translatedFormat('d F Y') : '-';
    $perpanjanganKe = $transaksi->perpanjangan()->where('id', '<=', $perpanjangan->id)->count();
    $ujrahDibayar = $perpanjangan->ujrah_dibayar ?? 0;
    $petugas = $perpanjangan->user->name ?? $transaksi->user->name ?? '-';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Perpanjangan - {{ $nomorNota }}</title>
    <style>
        @page { margin: 16mm 24mm 18mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.35; color: #000; background: #fff; }
        .receipt { width: 90%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 8px; }
        .logo { display: block; width: 190px; height: auto; margin: 0 auto 6px; }
        .brand-fallback { font-size: 12px; font-weight: bold; margin-bottom: 8px; }
        .title { font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .dotted { border-top: 1px dashed #000; height: 1px; margin: 10px 0; }
        .solid-line { border-top: 1px solid #000; height: 1px; margin: 16px 0; }
        .items, .signature { table-layout: fixed; }
        .meta { width: 360px; border-collapse: collapse; margin: 0 auto 8px; padding-left: 38px; table-layout: auto; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta-label { width: 86px; }
        .meta-sep { width: 10px; text-align: center; }
        .section-title { font-size: 11px; font-weight: normal; margin: 12px 0 5px; }
        .info { width: 360px; border-collapse: collapse; table-layout: auto; }
        .info td { padding: 2px 0; vertical-align: top; word-wrap: break-word; }
        .info-label { width: 126px; }
        .info-sep { width: 10px; text-align: center; }
        .items { width: 100%; border-collapse: collapse; margin: 18px auto 0; }
        .items th, .items td { border: 1px solid #000; padding: 5px 6px; word-wrap: break-word; }
        .items th { font-size: 10.5px; font-weight: normal; text-align: left; }
        .items th:first-child, .items td:first-child { width: 58%; }
        .items th:last-child, .items td:last-child { width: 42%; }
        .right { text-align: right; }
        .check-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .check-table td { padding: 1px 0; vertical-align: middle; }
        .check-box { width: 14px; height: 14px; border: 1px solid #000; text-align: center; font-size: 9px; line-height: 12px; padding: 0; }
        .check-spacer { width: 8px; }
        .check-label { padding-left: 2px; }
        .statement { margin-top: 8px; }
        .signature { width: 100%; border-collapse: collapse; margin: 22px auto 0; }
        .signature td { width: 50%; vertical-align: top; padding: 0 12px 0 0; word-wrap: break-word; }
        .signature-right { text-align: left; }
        .signature-heading { height: 44px; }
        .signature-space { height: 92px; }
        .signature-line { display: inline-block; width: 190px; font-weight: bold; text-decoration: underline; }
        .footer { margin-top: 22px; text-align: center; font-size: 8.5px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if($logoExists)
                <img class="logo" src="{{ $logoPath }}" alt="Harmans Gadai Syariah">
            @else
                <div class="brand-fallback">HARMANS GADAI SYARIAH</div>
            @endif
            <div class="title">NOTA PERPANJANGAN - PEMBIAYAAN RAHN</div>
        </div>

        <table class="meta">
            <tr><td class="meta-label">Nomor Nota</td><td class="meta-sep">:</td><td>{{ $nomorNota }}</td></tr>
            <tr><td class="meta-label">Tanggal</td><td class="meta-sep">:</td><td>{{ $tanggalPerpanjangan }}</td></tr>
            <tr><td class="meta-label">Cabang</td><td class="meta-sep">:</td><td>{{ $cabang }}</td></tr>
        </table>

        <div class="dotted"></div>

        <div class="section-title">Data Nasabah</div>
        <table class="info">
            <tr><td class="info-label">Nama Nasabah</td><td class="info-sep">:</td><td>{{ $nasabah->nama ?? '-' }}</td></tr>
            <tr><td class="info-label">Nomor Akad</td><td class="info-sep">:</td><td>{{ $nomorAkad }}</td></tr>
            <tr><td class="info-label">Nomor HP</td><td class="info-sep">:</td><td>{{ $nasabah->telepon ?? '-' }}</td></tr>
        </table>

        <div class="solid-line"></div>

        <div class="section-title">Rincian Perpanjangan</div>
        <table class="items">
            <thead>
                <tr><th>Uraian</th><th>Jumlah (Rp)</th></tr>
            </thead>
            <tbody>
                <tr><td>Jatuh tempo sebelumnya</td><td>{{ $jatuhTempoSebelumnya }}</td></tr>
                <tr><td>Perpanjangan ke-</td><td>{{ $perpanjanganKe }}</td></tr>
                <tr><td>Jatuh tempo baru</td><td>{{ $jatuhTempoBaruText }}</td></tr>
                <tr><td>Ujrah (biaya penitipan) yang dibayar</td><td class="right">{{ number_format($ujrahDibayar, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>

        <div class="dotted"></div>

        <div>Dibayarkan secara :</div>
        <table class="check-table">
            <tr><td class="check-box"></td><td class="check-spacer"></td><td class="check-label">Tunai</td></tr>
            <tr><td class="check-box"></td><td class="check-spacer"></td><td class="check-label">Transfer (Bukti transfer terlampir)</td></tr>
        </table>

        <div class="dotted"></div>

        <div class="statement">
            Pernyataan :<br>
            Dengan membayar ujrah di atas, NASABAH memperpanjang jangka waktu gadai tanpa<br>
            mengubah pokok pinjaman. Seluruh ketentuan akad tetap berlaku.
        </div>

        <table class="signature">
            <tr>
                <td>
                    <div class="signature-heading">Nasabah Peminjam,</div>
                    <div class="signature-space"></div>
                    <div class="signature-line">{{ $nasabah->nama ?? '-' }}</div>
                </td>
                <td class="signature-right">
                    <div class="signature-heading">
                        Hormat kami,<br>
                        HARMANS GADAI SYARIAH<br>
                        Penerima,
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-line">{{ $petugas }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">Nota ini sah sebagai bukti perpanjangan dan pembayaran ujrah</div>
    </div>
</body>
</html>
