@php
    $logoPath = public_path('images/logo.jpeg');
    $logoExists = file_exists($logoPath);
    $nasabah = $transaksi->nasabah;
    $pelunasan = $transaksi->pelunasan;
    $nomorNota = 'LNS-' . $transaksi->no_transaksi;
    $tanggalPelunasan = $pelunasan?->tanggal_pelunasan
        ? \Carbon\Carbon::parse($pelunasan->tanggal_pelunasan)->translatedFormat('d F Y')
        : now()->translatedFormat('d F Y');
    $cabang = $nasabah->cabang->nama_cabang ?? '-';
    $nomorAkad = $transaksi->no_register_akad ?? $transaksi->no_transaksi;
    $sisaPokok = $pelunasan->total_pinjaman ?? $transaksi->sisa_pinjaman ?? 0;
    $sisaUjrah = $pelunasan->total_ujrah ?? 0;
    $biayaLain = 0;
    $totalPelunasan = $pelunasan->total_bayar ?? ($sisaPokok + $sisaUjrah + $biayaLain);
    $petugas = $pelunasan->user->name ?? $transaksi->user->name ?? '-';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Pelunasan - {{ $nomorNota }}</title>
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
        .solid-line { border-top: 1px solid #000; height: 1px; margin: 12px 0; }
        .items, .signature { table-layout: fixed; }
        .meta { width: 360px; border-collapse: collapse; margin: 0 auto 8px; padding-left: 38px; table-layout: auto; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta-label { width: 86px; }
        .meta-sep { width: 10px; text-align: center; }
        .section-title { font-size: 11px; font-weight: normal; margin: 10px 0 5px; }
        .info { width: 360px; border-collapse: collapse; table-layout: auto; }
        .info td { padding: 2px 0; vertical-align: top; word-wrap: break-word; }
        .info-label { width: 126px; }
        .info-sep { width: 10px; text-align: center; }
        .items { width: 100%; border-collapse: collapse; margin: 12px auto 0; }
        .items th, .items td { border: 1px solid #000; padding: 5px 6px; word-wrap: break-word; }
        .items th { font-size: 10.5px; font-weight: normal; text-align: left; }
        .items th:first-child, .items td:first-child { width: 58%; }
        .items th:last-child, .items td:last-child { width: 42%; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .check-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .check-table td { padding: 1px 0; vertical-align: middle; }
        .check-box { width: 14px; height: 14px; border: 1px solid #000; text-align: center; font-size: 9px; line-height: 12px; padding: 0; }
        .check-spacer { width: 8px; }
        .check-label { padding-left: 2px; }
        .signature { width: 100%; border-collapse: collapse; margin: 14px auto 0; }
        .signature td { width: 50%; vertical-align: top; padding: 0 12px 0 0; word-wrap: break-word; }
        .signature-right { text-align: left; }
        .signature-heading { height: 44px; }
        .signature-space { height: 92px; }
        .signature-line { display: inline-block; width: 190px; font-weight: bold; text-decoration: underline; }
        .note { margin-top: 10px; font-size: 9px; }
        .footer { margin-top: 12px; text-align: center; font-size: 8.5px; }
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
            <div class="title">NOTA PELUNASAN - PEMBIAYAAN RAHN</div>
        </div>

        <table class="meta">
            <tr><td class="meta-label">Nomor Nota</td><td class="meta-sep">:</td><td>{{ $nomorNota }}</td></tr>
            <tr><td class="meta-label">Tanggal</td><td class="meta-sep">:</td><td>{{ $tanggalPelunasan }}</td></tr>
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

        <div class="section-title">Rincian Pelunasan</div>
        <table class="items">
            <thead>
                <tr><th>Uraian</th><th>Jumlah (Rp)</th></tr>
            </thead>
            <tbody>
                <tr><td>Sisa Pokok Pinjaman (Qard)</td><td class="right">{{ number_format($sisaPokok, 0, ',', '.') }}</td></tr>
                <tr><td>Sisa Biaya Penitipan (Ujrah)</td><td class="right">{{ number_format($sisaUjrah, 0, ',', '.') }}</td></tr>
                <tr><td>Biaya Lain (Bila ada)</td><td class="right">{{ number_format($biayaLain, 0, ',', '.') }}</td></tr>
                <tr><td>Total Pelunasan</td><td class="right bold">{{ number_format($totalPelunasan, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>

        <div class="dotted"></div>

        <div>Status :</div>
        <table class="check-table">
            <tr><td class="check-box">x</td><td class="check-spacer"></td><td class="check-label bold">LUNAS SELURUHNYA</td></tr>
        </table>

        <div class="dotted"></div>

        <div>Barang jaminan :</div>
        <table class="check-table">
            <tr><td class="check-box"></td><td class="check-spacer"></td><td class="check-label">Telah dikembalikan kepada nasabah pada tanggal ................................</td></tr>
            <tr><td class="check-box"></td><td class="check-spacer"></td><td class="check-label">Belum diambil nasabah, wajib diambil paling lambat ................................</td></tr>
        </table>

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

        <div class="solid-line"></div>

        <div class="note">
            Keterangan lebih lanjut :<br>
            Barang jaminan telah dikembalikan dalam kondisi sama dengan saat diterima. Tidak ada kewajiban tersisa.<br>
            Akad dinyatakan selesai.
        </div>

        <div class="dotted"></div>

        <div class="footer">Nota ini sebagai bukti sah pelunasan dan pengembalian barang.</div>
    </div>
</body>
</html>
