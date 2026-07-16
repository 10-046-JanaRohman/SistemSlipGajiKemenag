<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Gaji Pegawai</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
            font-size: 8pt;
            color: #000;
        }

        body {
            position: relative;
            line-height: 1.25;
        }

        .sheet {
            position: relative;
            width: 93.4%;
            min-height: 190mm;
            margin: 0 auto;
            padding-top: 18.2mm;
            overflow: hidden;
        }

        .top-line {
            display: table;
            width: 100%;
            margin-bottom: 10mm;
        }

        .top-left,
        .top-center {
            display: table-cell;
            vertical-align: top;
        }

        .top-left {
            width: 37%;
            font-size: 7.5pt;
            padding-top: 4.5mm;
        }

        .top-center {
            width: 63%;
            text-align: left;
        }

        .title {
            font-size: 8pt;
            font-weight: normal;
            margin: 0;
        }

        .subtitle {
            font-size: 7.5pt;
            margin: 0 0 1px;
        }

        .meta-right {
            text-align: right;
            font-size: 6.8px;
            margin-bottom: 1mm;
        }

        .payment {
            font-size: 7pt;
            margin: 0 0 0.8mm;
        }

        .table-zone {
            position: relative;
        }

        .slip-table {
            width: 100%;
            border: 1pt solid #000;
            border-collapse: collapse;
            table-layout: fixed;
            position: relative;
            z-index: 2;
        }

        .slip-table th,
        .slip-table td {
            border: 1pt solid #000;
            padding: 0.6pt 0.9pt;
            vertical-align: top;
            word-wrap: break-word;
        }

        .slip-table thead th {
            text-align: center;
            font-weight: normal;
            line-height: 1.15;
        }

        .slip-table .index-row th {
            font-size: 6pt;
            padding: 0.4pt 0.8pt;
            height: 3.8mm;
        }

        .slip-table .group-row th {
            height: 3.8mm;
        }

        .slip-table .head-group {
            font-size: 6pt;
            padding-top: 0.4mm;
            padding-bottom: 0.4mm;
        }

        .slip-table .head-bridge {
            padding: 0;
            border-bottom: none !important;
        }

        .slip-table .head-side {
            height: 23.8mm;
            border-top: none !important;
            font-size: 6pt;
            padding-top: 0.7mm;
            padding-bottom: 0.7mm;
        }

        .slip-table .head-sub {
            font-size: 6pt;
            padding-top: 0.7mm;
            padding-bottom: 0.7mm;
            height: 23.8mm;
        }

        .slip-table tbody td {
            font-size: 6pt;
            line-height: 1.16;
            padding-top: 0.8mm;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .muted { color: #333; }
        .bold { font-weight: bold; }
        .tiny { font-size: 5.5pt; }
        .stack div { margin-bottom: 1.25mm; }
        .stack div:last-child { margin-bottom: 0; }

        .cell-box {
            min-height: 59.7mm;
            display: block;
        }

        .cell-box.short {
            min-height: 59.7mm;
        }

        .cell-box.tall {
            min-height: 59.7mm;
        }

        .amount-stack div {
            min-height: 3.6mm;
            text-align: right;
            padding-right: 0.4mm;
        }

        .amount-stack .total {
            font-weight: bold;
        }

        .cell-note {
            margin-top: 1mm;
            font-size: 5.2px;
        }

        .watermark {
            position: fixed;
            left: 50%;
            top: 100mm;
            transform: translate(-50%, -50%) rotate(-18deg);
            font-size: 44pt;
            font-weight: bold;
            color: #d3d3d3;
            letter-spacing: 1.2pt;
            line-height: 1.05;
            text-align: center;
            z-index: 1;
            pointer-events: none;
            white-space: nowrap;
        }

        .signature-area {
            position: relative;
            width: 100%;
            border-top: 1pt solid #000;
            margin-top: 1.2mm;
            min-height: 35mm;
        }

        .signature-box {
            position: absolute;
            left: 42%;
            top: 0;
            width: 24%;
            text-align: center;
            font-size: 8pt;
            z-index: 2;
        }

        .signature-box .lead {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }

        .signature-box .role {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 10.5mm;
        }

        .signature-box .name {
            display: inline-block;
            min-width: 42mm;
            padding-bottom: 0.6mm;
            border-bottom: 1.2pt solid #000;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 0.8mm;
        }

        .signature-box .nip {
            display: inline-block;
            min-width: 42mm;
            padding-bottom: 0.6mm;
            border-bottom: 1.2pt solid #000;
            font-size: 8pt;
            font-weight: bold;
        }

        .stamp-placeholder {
            position: absolute;
            left: 41.5%;
            top: -5mm;
            width: 35mm;
            height: 31mm;
            border: 1px dashed #b7b7b7;
            border-radius: 50%;
            color: #b7b7b7;
            text-align: center;
            font-size: 6pt;
            line-height: 1.25;
            box-sizing: border-box;
            opacity: 0.7;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: pre-line;
            text-transform: uppercase;
            z-index: 4;
        }

        .footer-note {
            display: none;
        }

        .no-border {
            border: none !important;
        }

        .value-right {
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $pegawai = $slip->pegawai ?? null;
    $detail = data_get($slip, 'detail_gaji', []) ?: [];
    $rPegawai = data_get($rincian, 'pegawai', []);
    $p = data_get($rincian, 'pendapatan', []);
    $pt = data_get($rincian, 'potongan', []);
    $pdfSettings = [];

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
            $pdfSettings = \App\Models\AppSetting::query()
                ->whereIn('key', ['pdf_bendahara_nama', 'pdf_bendahara_nip'])
                ->pluck('value', 'key')
                ->toArray();
        }
    } catch (\Throwable $e) {
        $pdfSettings = [];
    }

    $normalizeText = function ($value, $default = '-') {
        if ($value === null) {
            return $default;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value === '' ? $default : $value;
    };

    $parseNumber = function ($value) {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace(['Rp', 'rp', ' '], '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    };

    $money = function ($value) use ($parseNumber) {
        $number = $parseNumber($value);

        if ($number === null) {
            return '-';
        }

        return number_format($number, 0, ',', '.');
    };

    $pick = function (array $paths, $default = null) use ($slip, $detail, $rincian, $pegawai, $rPegawai) {
        foreach ($paths as $path) {
            $sources = [$detail, $slip, $rincian, $pegawai, $rPegawai];

            foreach ($sources as $source) {
                if ($source === null) {
                    continue;
                }

                $value = data_get($source, $path);

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return $default;
    };

    $nama = $normalizeText($pick([
        'pegawai.nama',
        'nmpeg',
        'nama',
        'pegawai_nama',
    ]));

    $nip = $normalizeText($pick([
        'pegawai.nip',
        'nip',
        'pegawai_nip',
    ]));

    $npwp = $normalizeText($pick([
        'pegawai.npwp',
        'npwp',
    ]));

    $golongan = $normalizeText($pick([
        'pegawai.golongan',
        'kdgol',
        'golongan',
    ]));

    $statusPegawai = $normalizeText($pick([
        'pegawai.status_pegawai',
        'status_pegawai',
        'kdduduk',
    ]));

    $statusKawin = $normalizeText($pick([
        'pegawai.status_kawin',
        'kdkawin',
        'status_kawin',
    ]));

    $statusKawin = strtoupper((string) $statusKawin);
    if ($statusKawin !== '-') {
        $statusKawin = str_starts_with($statusKawin, 'K') ? 'K' : (str_starts_with($statusKawin, 'T') ? 'TK' : $statusKawin);
    }

    $tanggalLahirRaw = $pick([
        'pegawai.tanggal_lahir',
        'tanggal_lahir',
    ]);

    $tempatLahir = $normalizeText($pick([
        'pegawai.tempat_lahir',
        'tempat_lahir',
    ]), '');

    $tanggalLahir = '-';
    if (!empty($tanggalLahirRaw)) {
        try {
            $tanggalLahir = \Illuminate\Support\Carbon::parse($tanggalLahirRaw)->format('d-m-Y');
        } catch (\Throwable $e) {
            $tanggalLahir = (string) $tanggalLahirRaw;
        }
    }

    $identitasLahir = trim(($tempatLahir !== '' ? $tempatLahir . ' / ' : '') . $tanggalLahir);
    if ($identitasLahir === '') {
        $identitasLahir = '-';
    }

    // Header mengikuti nilai asli dari Excel; field pegawai hanya fallback.
    $satker = $normalizeText($pick([
        'kdsatker',
        'satker',
        'pegawai.keterangan_satuan_kerja',
        'pegawai.satker_1',
        'pegawai.grup_satuan_kerja',
    ], 'KANWIL KEMENTERIAN AGAMA PROP. LAMPUNG'));

    $namaBank = $normalizeText($pick([
        'nmbanksp',
        'nmbankspan',
        'nm_bank',
        'nama_bank',
        'pegawai.nama_bank',
    ], 'BANK RAKYAT INDONESIA'));

    $nomorGaji = $normalizeText($pick([
        'nogaji',
        'nomor_gaji',
    ], str_pad((string) $slip->id, 6, '0', STR_PAD_LEFT)));

    $nomorUrut = $normalizeText($pick([
        'no',
    ], '1'));

    $nomorBulan = (int) ($slip->bulan ?? $pick(['bulan'], 0));
    $namaBulan = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
        5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
        9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
    ];
    $bulan = $namaBulan[$nomorBulan] ?? strtoupper((string) ($slip->bulan ?? '-'));
    $tahun = $normalizeText($slip->tahun ?? $pick(['thngj', 'tahun']), '-');
    $jenisPembayaran = strtoupper((string) $pick(['pembayaran', 'keterangan_pembayaran'], ''));
    $tipeSup = str_pad(trim((string) $pick(['tipesup'], '')), 2, '0', STR_PAD_LEFT);
    if ($jenisPembayaran === '') {
        $jenisPembayaran = match ($tipeSup) {
            '03' => 'GAJI SUSULAN CPNS BULAN',
            '02' => 'GAJI SUSULAN BULAN',
            default => 'GAJI BULAN',
        };
    }
    $pembayaran = $jenisPembayaran . ' ' . $bulan . ' ' . $tahun;

    $bendaharaNama = $normalizeText($pick([
        'bendahara_nama',
        'pegawai.bendahara_nama',
    ], $pdfSettings['pdf_bendahara_nama'] ?? 'Nama Bendahara'));

    $bendaharaNip = $normalizeText($pick([
        'bendahara_nip',
        'pegawai.bendahara_nip',
    ], $pdfSettings['pdf_bendahara_nip'] ?? 'NIP Bendahara'));

    $gpokok = $pick(['gjpokok', 'gpokok', 'gaji_pokok'], 0);
    $tjistri = $pick(['tjistri'], 0);
    $tjanak = $pick(['tjanak'], 0);
    $tjupns = $pick(['tjupns'], 0);
    $tjstruk = $pick(['tjstruk'], 0);
    $tjfungsi = $pick(['tjfungs', 'tjfungsi'], 0);
    $tjdaerah = $pick(['tjdaerah'], 0);
    $tjpencil = $pick(['tjpencil'], 0);
    $tjlain = $pick(['tjlain'], 0);
    $tjkompen = $pick(['tjkompen'], 0);
    $pembul = $pick(['pembul'], 0);
    $tjberas = $pick(['tjberas'], 0);
    $tjpph = $pick(['tjpph'], 0);

    $potpfkbul = $pick(['potpfkbul'], 0);
    $potpfk2 = $pick(['potpfk2'], 0);
    $potpfk10 = $pick(['potpfk10'], 0);
    $potpph = $pick(['potpph'], 0);
    $potswrum = $pick(['potswrum'], 0);
    $potkelbtj = $pick(['potkelbtj'], 0);
    $potlain = $pick(['potlain'], 0);
    $pottabrum = $pick(['pottabrum'], 0);
    $bpjs = $pick(['bpjs'], 0);
    $bpjs2 = $pick(['bpjs2'], 0);

    $totalPendapatan = $pick(['total_pendapatan'], null);
    if ($totalPendapatan === null) {
        $totalPendapatan = (float) $gpokok + (float) $tjistri + (float) $tjanak + (float) $tjupns + (float) $tjstruk + (float) $tjfungsi + (float) $tjdaerah + (float) $tjpencil + (float) $tjlain + (float) $tjkompen + (float) $pembul + (float) $tjberas + (float) $tjpph;
    }

    $totalPotongan = $pick(['total_potongan'], null);
    if ($totalPotongan === null) {
        $totalPotongan = (float) $potpfkbul + (float) $potpfk2 + (float) $potpfk10 + (float) $potpph + (float) $potswrum + (float) $potkelbtj + (float) $potlain + (float) $pottabrum + (float) $bpjs + (float) $bpjs2;
    }

    $gajiBersih = $pick(['bersih', 'bersih_rinci', 'gaji_bersih'], null);
    if ($gajiBersih === null) {
        $gajiBersih = (float) $totalPendapatan - (float) $totalPotongan;
    }
    if ((float) $gajiBersih === 0.0 && ((float) $totalPendapatan - (float) $totalPotongan) > 0) {
        $gajiBersih = (float) $totalPendapatan - (float) $totalPotongan;
    }

    $potIwp = (float) $potpfkbul + (float) $potpfk2 + (float) $potpfk10;
    $potBpjs = (float) $bpjs + (float) $bpjs2;
    $potLain = (float) $potswrum + (float) $potkelbtj + (float) $potlain + (float) $pottabrum;

    $watermarkText = "HANYA UNTUK\nKEPERLUAN BPJS";
@endphp

<div class="sheet">
    <div class="top-line">
        <div class="top-left">KEMENTERIAN AGAMA</div>
        <div class="top-center">
            <div class="title">DAFTAR GAJI PEGAWAI</div>
            <div class="subtitle">KANWIL KEMENTERIAN AGAMA PROP. LAMPUNG</div>
            <div class="subtitle">{{ $namaBank }}</div>
            <div class="subtitle">SATKER / NOMOR GAJI : {{ $satker }} / {{ $nomorGaji }}</div>
        </div>
    </div>

    <div class="payment">PEMBAYARAN : {{ $pembayaran }}</div>

    <div class="table-zone">
        <div class="watermark">{!! nl2br(e($watermarkText)) !!}</div>

        <table class="slip-table">
        <colgroup>
            <col style="width:2.34%">
            <col style="width:11.29%">
            <col style="width:2.88%">
            <col style="width:7.44%">
            <col style="width:6.25%">
            <col style="width:6.76%">
            <col style="width:5.40%">
            <col style="width:4.82%">
            <col style="width:7.12%">
            <col style="width:4.89%">
            <col style="width:4.96%">
            <col style="width:6.47%">
            <col style="width:6.25%">
            <col style="width:6.47%">
            <col style="width:7.77%">
            <col style="width:8.71%">
        </colgroup>
        <thead>
            <tr class="group-row">
                <th class="head-bridge"></th>
                <th class="head-bridge"></th>
                <th class="head-bridge"></th>
                <th colspan="6" class="head-group">PENGHASILAN</th>
                <th colspan="5" class="head-group">POTONGAN</th>
                <th class="head-bridge"></th>
                <th class="head-bridge"></th>
            </tr>
            <tr>
                <th class="head-side">NO.<br>URT</th>
                <th class="head-side">NAMA<br>TANGGAL LAHIR<br>NIP<br>STATUS PEGAWAI<br>GOLONGAN</th>
                <th class="head-side">STA.<br>KAWIN<br>JML/<br>ANAK<br>JIWA</th>
                <th class="head-sub">GAJI.<br>POKOK</th>
                <th class="head-sub">TUN. UMUM<br>TAMB. UMUM<br>TUNJ. PAPUA<br>TW.TERCIL</th>
                <th class="head-sub">TUNJ. JABATAN<br>STRUKTURAL<br>FUNGSIONAL<br>LAIN-LAIN<br>PEMBULATAN</th>
                <th class="head-sub">TUNJ.<br>BERAS</th>
                <th class="head-sub">TUNJ.<br>KHUSUS<br>PAJAK</th>
                <th class="head-sub">JUMLAH<br>PENGH.<br>KOTOR</th>
                <th class="head-sub">POT.<br>BERAS</th>
                <th class="head-sub">IWP<br>BPJS<br>BPJS LAIN</th>
                <th class="head-sub">PAJAK<br>PENGH.<br>SILAN</th>
                <th class="head-sub">SEWA RMH<br>TUNGGAKAN<br>UTANG LEBIH<br>POT. LAIN<br>TAPERUM</th>
                <th class="head-sub">JUMLAH<br>POTONGAN</th>
                <th class="head-side">JUMLAH<br>BERSIH<br>YANG<br>DIBAYARKAN</th>
                <th class="head-side">TANDA TANGAN</th>
            </tr>
            <tr class="index-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>15</th>
                <th>16</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">
                    <div class="cell-box short">
                        <div class="bold">{{ $nomorUrut }}.</div>
                        <div class="tiny muted">{{ $nomorGaji }}</div>
                    </div>
                </td>

                <td class="left">
                    <div class="cell-box tall stack">
                        <div class="bold">{{ $nama }}</div>
                        <div>LHR. {{ $identitasLahir }}</div>
                        <div>NIP. {{ $nip }}</div>
                        <div>{{ $statusPegawai ?: 'PEGAWAI' }}</div>
                        <div>{{ $golongan }}</div>
                        <div>NPWP. {{ $npwp }}</div>
                    </div>
                </td>

                <td class="center">
                    <div class="cell-box short stack">
                        <div>{{ $statusKawin }}</div>
                        <div>JML.</div>
                        <div>-</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box tall amount-stack">
                        <div>{{ $money($gpokok) }}</div>
                        <div>{{ $money((float) $tjistri + (float) $tjanak) }}</div>
                        <div>{{ $money($tjistri) }}</div>
                        <div>{{ $money($tjanak) }}</div>
                        <div>-</div>
                        <div class="total">{{ $money((float) $gpokok + (float) $tjistri + (float) $tjanak) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box tall amount-stack">
                        <div>{{ $money($tjupns) }}</div>
                        <div>{{ $money(0) }}</div>
                        <div>{{ $money(0) }}</div>
                        <div>{{ $money($tjpencil) }}</div>
                        <div>&nbsp;</div>
                        <div class="total">{{ $money((float) $tjupns + (float) $tjpencil) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box tall amount-stack">
                        <div>{{ $money($tjstruk) }}</div>
                        <div>{{ $money($tjkompen) }}</div>
                        <div>{{ $money($tjfungsi) }}</div>
                        <div>{{ $money($tjlain) }}</div>
                        <div>{{ $money($pembul) }}</div>
                        <div class="total">{{ $money((float) $tjstruk + (float) $tjkompen + (float) $tjfungsi + (float) $tjlain + (float) $pembul) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div>{{ $money($tjberas) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div>{{ $money($tjpph) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div class="total">{{ $money($totalPendapatan) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div>{{ $money($potpfkbul) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box tall amount-stack">
                        <div>{{ $money($potpfk2) }}</div>
                        <div>{{ $money($bpjs) }}</div>
                        <div>{{ $money($bpjs2) }}</div>
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div>{{ $money($potpph) }}</div>
                        <div>{{ $money(0) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box tall amount-stack">
                        <div>{{ $money($potswrum) }}</div>
                        <div>{{ $money($potkelbtj) }}</div>
                        <div>{{ $money($potlain) }}</div>
                        <div>{{ $money(0) }}</div>
                        <div>{{ $money($pottabrum) }}</div>
                        <div class="total">{{ $money($potLain) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div class="total">{{ $money($totalPotongan) }}</div>
                    </div>
                </td>

                <td class="right">
                    <div class="cell-box short amount-stack">
                        <div class="total">{{ $money($gajiBersih) }}</div>
                    </div>
                </td>

                <td class="left">
                    <div class="cell-box tall stack">
                        <div class="tiny">{{ $nomorUrut }}. ................................</div>
                        <div class="tiny">....................................</div>
                        <div class="tiny">....................................</div>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

    <div class="signature-area">
        <div class="stamp-placeholder">PLACEHOLDER
STEMPEL DIGITAL</div>
        <div class="signature-box">
            <div class="lead">Mengetahui</div>
            <div class="role">Bendahara Gaji</div>
            <div class="name">{{ $bendaharaNama }}</div>
            <div class="nip">NIP. {{ $bendaharaNip }}</div>
        </div>
    </div>

    <div class="footer-note">
        Dokumen ini dicetak secara elektronik oleh Sistem Dashboard Slip Gaji Kanwil Kementerian Agama Provinsi Lampung.
    </div>
</div>
</body>
</html>
