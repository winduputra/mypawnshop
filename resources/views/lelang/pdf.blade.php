@php
    $logoPath = public_path('images/logo.jpeg');
    $logoExists = file_exists($logoPath);
    $transaksi = $lelang->transaksiRahn;
    $nasabah = $transaksi->nasabah;
    $details = $transaksi->detailTransaksi ?? collect();

    $nomorNota = $lelang->no_lelang ?? '-';
    $tanggalLelang = $lelang->tanggal_terjual ?? $lelang->tanggal_lelang ?? $lelang->created_at ?? now();
    $tanggalNota = $tanggalLelang ? \Carbon\Carbon::parse($tanggalLelang)->translatedFormat('d F Y') : '-';
    $tanggalAkadRaw = $transaksi->tanggal_transaksi ?? null;
    $tanggalAkad = $tanggalAkadRaw ? \Carbon\Carbon::parse($tanggalAkadRaw)->translatedFormat('d F Y') : '-';
    $cabang = $nasabah->cabang->nama_cabang ?? $nasabah->cabang->nama ?? '-';
    $nomorAkad = $transaksi->no_register_akad ?? $transaksi->no_transaksi ?? '-';
    $detailPertama = $details->first();
    $barangPertama = $detailPertama?->barang;

    $hargaLelang = $lelang->harga_lelang ?? 0;
    $biayaLelang = $lelang->biaya_lelang ?? 0;
    $ujrahTercatat = $lelang->ijarah ?? 0;
    $ujrah = $ujrahTercatat > 0 ? $ujrahTercatat : ($transaksi->biaya_penitipan ?? $transaksi->ujrah_per_30hari ?? 0);
    $kelebihan = max($lelang->sisa_dana_kembali ?? 0, $lelang->sisa_untuk_nasabah ?? 0);
    $kekurangan = $lelang->kerugian ?? 0;
    $sisaPinjamanTercatat = $lelang->sisa_pinjaman ?? $transaksi->sisa_pinjaman ?? 0;
    $totalKewajibanTercatat = $sisaPinjamanTercatat + $ujrah + $biayaLelang;
    $totalPenggunaan = ($kelebihan > 0 || $kekurangan > 0)
        ? ($hargaLelang - $kelebihan + $kekurangan)
        : $totalKewajibanTercatat;
    $pokokPinjaman = max(0, $totalPenggunaan - $ujrah - $biayaLelang);
    $sisaLabel = $kelebihan > 0
        ? number_format($kelebihan, 0, ',', '.')
        : ($kekurangan > 0 ? number_format($kekurangan, 0, ',', '.') : '0');

@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Lelang - {{ $nomorNota }}</title>
    <style>
        @page { margin: 14mm 22mm 16mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; line-height: 1.3; color: #000; background: #fff; }
        .receipt { width: 90%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 7px; }
        .logo { display: block; width: 175px; height: auto; margin: 0 auto 5px; }
        .brand-fallback { font-size: 12px; font-weight: bold; margin-bottom: 7px; }
        .title { font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .dotted { border-top: 1px dashed #000; height: 1px; margin: 8px 0; }
        .solid-line { border-top: 1px solid #000; height: 1px; margin: 10px 0; }
        .meta { width: 360px; border-collapse: collapse; margin: 0 auto 7px; padding-left: 38px; table-layout: auto; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta-label { width: 86px; }
        .meta-sep { width: 10px; text-align: center; }
        .section-title { font-size: 10.5px; font-weight: normal; margin: 8px 0 4px; }
        .info { width: 100%; border-collapse: collapse; table-layout: auto; }
        .info td { padding: 1.5px 0; vertical-align: top; word-wrap: break-word; }
        .info-label { width: 160px; }
        .info-sep { width: 10px; text-align: center; }
        .items, .signature { table-layout: fixed; }
        .items { width: 100%; border-collapse: collapse; margin: 4px auto 0; }
        .items th, .items td { border: 1px solid #000; padding: 4px 5px; word-wrap: break-word; vertical-align: top; }
        .items th { font-size: 10px; font-weight: normal; text-align: left; }
        .items .amount { width: 34%; text-align: right; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .conclusion { margin-top: 8px; }
        .conclusion-title { margin-bottom: 3px; }
        .conclusion-row { margin-left: 12px; line-height: 1.45; }
        .signature { width: 100%; border-collapse: collapse; margin: 20px auto 0; }
        .signature td { width: 50%; vertical-align: top; padding: 0 12px 0 0; word-wrap: break-word; }
        .signature-right { text-align: left; }
        .signature-heading { height: 44px; }
        .signature-space { height: 76px; }
        .signature-line { letter-spacing: 1px; }
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
            <div class="title">NOTA LELANG - BERITA ACARA</div>
        </div>

        <table class="meta">
            <tr><td class="meta-label">Nomor Nota</td><td class="meta-sep">:</td><td>{{ $nomorNota }}</td></tr>
            <tr><td class="meta-label">Tanggal</td><td class="meta-sep">:</td><td>{{ $tanggalNota }}</td></tr>
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

        <table class="info">
            <tr><td class="info-label">Dasar Hukum</td><td class="info-sep">:</td><td>Pasal 7 Akad Rahn Nomor {{ $nomorAkad }} tanggal {{ $tanggalAkad }}</td></tr>
        </table>

        <div class="section-title">Barang Jaminan</div>
        <table class="info">
            <tr><td class="info-label">Jenis Barang</td><td class="info-sep">:</td><td>{{ $barangPertama->nama_barang ?? $barangPertama->kategori ?? '-' }}</td></tr>
            <tr><td class="info-label">Merk/type</td><td class="info-sep">:</td><td>{{ $barangPertama->merk_type ?? '-' }}</td></tr>
            <tr><td class="info-label">Nomor Seri / Polisi</td><td class="info-sep">:</td><td>{{ $barangPertama->nomor_seri ?? '-' }}</td></tr>
        </table>

        <table class="info">
            <tr><td class="info-label">Hasil Penjualan</td><td class="info-sep">:</td><td>Rp {{ number_format($hargaLelang, 0, ',', '.') }}</td></tr>
        </table>

        <div class="section-title">Penggunaan Dana :</div>
        <table class="items">
            <thead>
                <tr><th>Uraian</th><th class="amount">Jumlah</th></tr>
            </thead>
            <tbody>
                <tr><td>Pelunasan Pokok Pinjaman (Qard)</td><td class="amount">Rp {{ number_format($pokokPinjaman, 0, ',', '.') }}</td></tr>
                <tr><td>Pelunasan Biaya Penitipan (Ujrah)</td><td class="amount">Rp {{ number_format($ujrah, 0, ',', '.') }}</td></tr>
                <tr><td>Biaya Pelaksanaan Lelang</td><td class="amount">Rp {{ number_format($biayaLelang, 0, ',', '.') }}</td></tr>
                <tr><td class="bold">Total Penggunaan</td><td class="amount bold">Rp {{ number_format($totalPenggunaan, 0, ',', '.') }}</td></tr>
                <tr><td class="bold">Sisa (Kelebihan / Kekurangan)</td><td class="amount bold">{{ $kelebihan > 0 ? 'Rp ' : ($kekurangan > 0 ? '(Rp ' : 'Rp ') }}{{ $sisaLabel }}{{ $kekurangan > 0 ? ')' : '' }}</td></tr>
            </tbody>
        </table>

        <div class="conclusion">
            <div class="conclusion-title">Kesimpulan :</div>
            <div class="conclusion-row">1. Kelebihan : Akan dikembalikan ke Nasabah.</div>
            <div class="conclusion-row">2. Kekurangan : Tidak dapat ditagih sesuai prinsip Qard (Pasal 7.6)</div>
        </div>

        <table class="signature">
            <tr>
                <td>
                    <div class="signature-heading">Saksi,</div>
                    <div class="signature-space"></div>
                    <div class="signature-line">............................</div>
                </td>
                <td class="signature-right">
                    <div class="signature-heading">
                        Hormat kami,<br>
                        HARMANS GADAI SYARIAH<br>
                        Penerima,
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-line">............................</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
