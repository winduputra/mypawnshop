<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Perpanjangan - {{ $perpanjangan->no_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.6; color: #1a1a1a; padding: 30px 40px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 2px; margin-bottom: 3px; }
        .header h2 { font-size: 14px; font-weight: bold; margin-bottom: 3px; color: #4f46e5; }
        .header p { font-size: 10px; color: #555; }
        .nota-number { text-align: right; font-size: 10px; margin-bottom: 10px; }
        .nota-number strong { font-size: 12px; color: #333; }
        .section-title { font-weight: bold; font-size: 12px; margin: 15px 0 8px; padding: 5px 10px; background: #f0f0f0; border-left: 4px solid #4f46e5; }
        .info table { width: 100%; margin-left: 20px; }
        .info td { padding: 4px 5px; vertical-align: top; }
        .info td:first-child { width: 200px; font-weight: bold; }
        .lunas-badge { text-align: center; margin: 20px 0; padding: 15px; border: 3px solid #4f46e5; border-radius: 10px; background: #eef2ff; }
        .lunas-badge h2 { font-size: 20px; color: #4f46e5; }
        .lunas-badge.overdue { border-color: #d97706; background: #fffbeb; }
        .lunas-badge.overdue h2 { color: #d97706; }
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
        .sign-dto { font-size: 9px; color: #666; font-style: italic; }
        .highlight-box { background: #f0fdf4; border: 1px solid #86efac; padding: 8px 12px; border-radius: 5px; margin: 8px 0; }
        .highlight-box.warning { background: #fffbeb; border-color: #fcd34d; }
        .overdue-note { background: #fef2f2; border: 1px solid #fca5a5; padding: 8px 12px; border-radius: 5px; margin: 8px 0; font-size: 10px; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>HARMANS GADAI SYARIAH</h1>
        <h2>NOTA PERPANJANGAN TENOR</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Indonesia | Telp: (021) 123-4567</p>
    </div>

    {{-- Nota Number --}}
    <div class="nota-number">
        <strong>No. Nota: {{ $perpanjangan->no_nota }}</strong><br>
        Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

    <div class="lunas-badge {{ $perpanjangan->is_overdue_extension ? 'overdue' : '' }}">
        @if($perpanjangan->is_overdue_extension)
        <h2>PERPANJANGAN OVERDUE</h2>
        <p style="font-size: 12px; color: #92400e;">Tenor diperpanjang {{ $perpanjangan->tambahan_tenor_hari }} hari (denda: bayar 2x ijarah, pengurangan 10 hari).</p>
        @else
        <h2>PERPANJANGAN BERHASIL</h2>
        <p style="font-size: 12px; color: #4338ca;">Tenor transaksi telah diperpanjang {{ $perpanjangan->tambahan_tenor_hari }} hari.</p>
        @endif
    </div>

    {{-- WAJIB: No Register Akad --}}
    <div class="section-title">DATA AKAD PINJAMAN</div>
    <div class="info">
        <table>
            <tr><td>No. Register Akad</td><td>: <strong>{{ $transaksi->no_register_akad ?? '-' }}</strong></td></tr>
            <tr><td>No. Kontrak</td><td>: {{ $transaksi->no_transaksi }}</td></tr>
            <tr><td>Tanggal Akad</td><td>: {{ $transaksi->tanggal_transaksi }}</td></tr>
        </table>
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

    {{-- WAJIB: Pokok Pinjaman & Sisa Pokok Pinjaman --}}
    <div class="section-title">INFORMASI PINJAMAN</div>
    <div class="info">
        <table>
            <tr><td>Pokok Pinjaman</td><td>: <strong>Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Sisa Pokok Pinjaman</td><td>: <strong>Rp {{ number_format($transaksi->sisa_pinjaman, 0, ',', '.') }}</strong></td></tr>
        </table>
    </div>

    {{-- WAJIB: Jatuh Tempo --}}
    <div class="section-title">RINCIAN PERPANJANGAN</div>
    <div class="info">
        <table>
            <tr><td>Tanggal Pembayaran</td><td>: {{ $perpanjangan->tanggal_perpanjangan }}</td></tr>
            <tr><td>Jatuh Tempo Sebelumnya</td><td>: {{ \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)->subDays($perpanjangan->tambahan_tenor_hari)->format('d F Y') }}</td></tr>
            <tr><td><strong>Jatuh Tempo Baru</strong></td><td>: <strong>{{ \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)->format('d F Y') }}</strong></td></tr>
            <tr><td>Tambahan Tenor</td><td>: +{{ $perpanjangan->tambahan_tenor_hari }} hari</td></tr>
            <tr><td>Biaya Ijarah Dibayar</td><td>: <strong>Rp {{ number_format($perpanjangan->ujrah_dibayar, 0, ',', '.') }}</strong>
                @if($perpanjangan->is_overdue_extension)
                    (2x ijarah)
                @else
                    (1x ijarah)
                @endif
            </td></tr>
            <tr><td>Status Pembayaran</td><td>: <strong style="color: #16a34a;">LUNAS</strong></td></tr>
            @if($perpanjangan->catatan)
            <tr><td>Catatan</td><td>: {{ $perpanjangan->catatan }}</td></tr>
            @endif
        </table>
    </div>

    @if($perpanjangan->is_overdue_extension)
    <div class="overdue-note">
        <strong>Keterangan Overdue:</strong> Nasabah melewati jatuh tempo. Sesuai ketentuan, wajib membayar 2x biaya ijarah dan tenor hanya ditambah 50 hari (bukan 60 hari). Denda berupa pengurangan 10 hari masa tenor.
    </div>
    @endif

    {{-- WAJIB: Tanda Tangan Kasir (Otomatis dto) --}}
    <div class="signature-section">
        <table>
            <tr>
                <td>
                    <p>Kasir</p>
                    <div class="sign-line"></div>
                    <p class="sign-dto">dto (ditandatangani oleh)</p>
                    <p class="sign-name">{{ $perpanjangan->user->name ?? Auth::user()->name }}</p>
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
        <p>Simpan nota ini sebagai bukti perpanjangan jatuh tempo yang sah.</p>
        <p>No. Nota: {{ $perpanjangan->no_nota }} | No. Register Akad: {{ $transaksi->no_register_akad ?? '-' }}</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
