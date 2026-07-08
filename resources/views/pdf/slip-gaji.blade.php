<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        @page { margin: 8mm 5mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #000; margin: 0; padding: 0; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 3px; }
        .header h1 { font-size: 11px; margin: 0; font-weight: bold; letter-spacing: 1px; }
        .header h2 { font-size: 9px; margin: 1px 0 0 0; font-weight: bold; }
        .header .sub { font-size: 8px; margin: 1px 0 0 0; }

        .info-table { width: 100%; border-collapse: collapse; margin: 3px 0; font-size: 6.5px; }
        .info-table td { padding: 1px 2px; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 15%; }
        .info-table .colon { width: 1%; }
        .info-table .value { width: 34%; }

        .main-table { width: 100%; border-collapse: collapse; font-size: 6px; margin-top: 2px; }
        .main-table th { background: #e5e7eb; border: 1px solid #000; padding: 1.5px 1px; text-align: center; font-weight: bold; vertical-align: middle; }
        .main-table td { border: 1px solid #000; padding: 1px 1.5px; text-align: right; vertical-align: middle; }
        .main-table td.left { text-align: left; }
        .main-table td.center { text-align: center; }
        .main-table .sub-row td { border-top: none; font-size: 5.5px; padding: 1px 1.5px; }
        .main-table .total-row td { font-weight: bold; background: #f3f4f6; }

        .rincian { width: 100%; font-size: 6px; margin-top: 3px; border-collapse: collapse; }
        .rincian td { padding: 1px 4px; vertical-align: top; width: 50%; }
        .rincian strong { font-size: 6.5px; }
        .rincian .garis { border-bottom: 1px solid #000; margin: 2px 0; }

        .footer { margin-top: 3px; font-size: 6px; text-align: center; color: #333; }
</style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>DAFTAR GAJI PEGAWAI</h1>
        <h2>KEMENTERIAN AGAMA</h2>
        <h2>KANWIL KEMENTERIAN AGAMA PROP. LAMPUNG</h2>
        <div class="sub">BANK RAKYAT INDONESIA</div>
    </div>

    @php
        $p = $rincian['pendapatan'] ?? [];
        $pt = $rincian['potongan'] ?? [];

        $gjpokok = $p['Gaji Pokok'] ?? 0;
        $tjistri = $p['Tunjangan Istri'] ?? 0;
        $tjanak = $p['Tunjangan Anak'] ?? 0;
        $tjberas = $p['Tunjangan Beras'] ?? 0;
        $tjupns = $p['Tunjangan Umum'] ?? 0;
        $tjjabatan = $p['Tunjangan Jabatan'] ?? 0;
        $tjstruk = $p['Tunjangan Struktural'] ?? 0;
        $tjfungs = $p['Tunjangan Fungsional'] ?? 0;
        $tjkompen = $p['Tunjangan Kompensasi'] ?? 0;
        $tjdaerah = $p['Tunjangan Daerah'] ?? 0;
        $tjpencil = $p['Tunjangan Pencilan'] ?? 0;
        $tjlain = $p['Tunjangan Lainnya'] ?? 0;
        $pembul = $p['Pembulatan'] ?? 0;

        $tunjGabung = $tjupns + $tjjabatan + $tjstruk + $tjfungs + $tjkompen + $tjdaerah + $tjlain;
        $totalP = $rincian['total_pendapatan'] ?? 0;

        $potpph = $pt['PPh Pasal 21'] ?? 0;
        $potiwp = ($pt['Potongan FKP Bulanan'] ?? 0) + ($pt['Potongan FKP II'] ?? 0) + ($pt['Potongan FKP X'] ?? 0);
        $potbpjs = ($pt['BPJS'] ?? 0) + ($pt['BPJS 2'] ?? 0);
        $potLain = ($pt['Potongan SWRUM'] ?? 0) + ($pt['Potongan Kelebihan Tunjangan'] ?? 0) + ($pt['Potongan Lainnya'] ?? 0) + ($pt['Tabungan Perumahan'] ?? 0);

        $totalPot = $rincian['total_potongan'] ?? 0;
        $bersih = $rincian['gaji_bersih'] ?? 0;

        $stk = substr(strtoupper($slip->pegawai->status_kawin ?? ''), 0, 1);
        $stk = $stk === 'K' ? 'K' : ($stk === 'T' ? 'TK' : ($stk ?: '-'));
        $tglLahir = optional($slip->pegawai->tanggal_lahir ?? null)->format('d-m-Y') ?? '-';

        // Data satker dari pegawai
        $satker = $slip->pegawai->keterangan_satuan_kerja ?? $slip->pegawai->satker_1 ?? $slip->pegawai->grup_satuan_kerja ?? 'KEMENAG PROV. LAMPUNG';
        // Nomor urut gaji dari ID slip
        $noGaji = str_pad($slip->id, 6, '0', STR_PAD_LEFT);
        // Tanggal cetak
        $tglCetak = now()->translatedFormat('d F Y H:i');
    @endphp

    <table class="info-table">
        <tr>
            <td class="label">SATKER / NO. GAJI</td>
            <td class="colon">:</td>
            <td class="value">{{ $satker }} / {{ $noGaji }}</td>
            <td class="label">PEMBAYARAN</td>
            <td class="colon">:</td>
            <td class="value">GAJI {{ $slip->bulan }} {{ $slip->tahun }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="3%">NO.</th>
                <th rowspan="2" width="13%">NAMA<br>TGL LAHIR</th>
                <th rowspan="2" width="3%">ST.<br>KAWIN</th>
                <th rowspan="2" width="8%">GAJI<br>POKOK</th>
                <th colspan="3" width="12%">TUNJANGAN</th>
                <th rowspan="2" width="8%">TUNJ.<br>BERAS</th>
                <th rowspan="2" width="7%">TUNJ.<br>KHUSUS</th>
                <th rowspan="2" width="3%">PEM-<br>BULAT</th>
                <th rowspan="2" width="9%">JUMLAH<br>PENGH.</th>
                <th rowspan="2" width="7%">POT.<br>IWP</th>
                <th rowspan="2" width="7%">PAJAK<br>PPh</th>
                <th rowspan="2" width="7%">BPJS /<br>LAIN</th>
                <th rowspan="2" width="7%">JUMLAH<br>POT.</th>
                <th rowspan="2" width="9%">JUMLAH<br>BERSIH</th>
            </tr>
            <tr>
                <th width="4%">ISTRI</th>
                <th width="4%">ANAK</th>
                <th width="4%">UMUM / JAB / FUNGS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1.</td>
                <td class="left">{{ $slip->pegawai->nama ?? $rincian['pegawai']['nama'] ?? '-' }}</td>
                <td class="center">{{ $stk }}</td>
                <td>{{ number_format($gjpokok, 0, ',', '.') }}</td>
                <td>{{ number_format($tjistri, 0, ',', '.') }}</td>
                <td>{{ number_format($tjanak, 0, ',', '.') }}</td>
                <td>{{ number_format($tunjGabung, 0, ',', '.') }}</td>
                <td>{{ number_format($tjberas, 0, ',', '.') }}</td>
                <td>{{ number_format($tjpencil, 0, ',', '.') }}</td>
                <td>{{ number_format($pembul, 0, ',', '.') }}</td>
                <td>{{ number_format($totalP, 0, ',', '.') }}</td>
                <td>{{ number_format($potiwp, 0, ',', '.') }}</td>
                <td>{{ number_format($potpph, 0, ',', '.') }}</td>
                <td>{{ number_format($potbpjs + $potLain, 0, ',', '.') }}</td>
                <td>{{ number_format($totalPot, 0, ',', '.') }}</td>
                <td>{{ number_format($bersih, 0, ',', '.') }}</td>
            </tr>
            <tr class="sub-row">
                <td></td>
                <td colspan="2" class="left">
                    NIP. {{ $slip->pegawai->nip ?? $rincian['pegawai']['nip'] ?? '-' }}<br>
                    LHR. {{ $tglLahir }}
                </td>
                <td colspan="13" class="left">
                    NPWP. {{ $slip->pegawai->npwp ?? '-' }}
                    &nbsp;|&nbsp; Gol. {{ $slip->pegawai->golongan ?? $rincian['pegawai']['golongan'] ?? '-' }}
                    &nbsp;|&nbsp; {{ $slip->pegawai->status_pegawai ?? 'PEGAWAI' }}
                    &nbsp;|&nbsp; Rek: {{ $slip->pegawai->rekening ?? '-' }} {{ $slip->pegawai->nama_bank ?? '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="rincian">
        <tr>
            <td width="50%">
                <strong>RINCIAN PENGHASILAN:</strong><br>
                Gaji Pokok: Rp {{ number_format($gjpokok,0,',','.') }}<br>
                T. Istri: Rp {{ number_format($tjistri,0,',','.') }} | T. Anak: Rp {{ number_format($tjanak,0,',','.') }}<br>
                T. Umum: Rp {{ number_format($tjupns,0,',','.') }} | T. Jabatan: Rp {{ number_format($tjjabatan,0,',','.') }}<br>
                T. Fungsional: Rp {{ number_format($tjfungs,0,',','.') }} | T. Struktural: Rp {{ number_format($tjstruk,0,',','.') }}<br>
                T. Kompensasi: Rp {{ number_format($tjkompen,0,',','.') }} | T. Daerah: Rp {{ number_format($tjdaerah,0,',','.') }}<br>
                T. Pencilan: Rp {{ number_format($tjpencil,0,',','.') }} | T. Lain: Rp {{ number_format($tjlain,0,',','.') }}<br>
                T. Beras: Rp {{ number_format($tjberas,0,',','.') }} | Pembulatan: Rp {{ number_format($pembul,0,',','.') }}
            </td>
            <td width="50%">
                <strong>RINCIAN POTONGAN:</strong><br>
                Pot. IWP (FKP): Rp {{ number_format($potiwp,0,',','.') }}<br>
                PPh Pasal 21: Rp {{ number_format($potpph,0,',','.') }}<br>
                BPJS: Rp {{ number_format($potbpjs,0,',','.') }}<br>
                Pot. Lain: Rp {{ number_format($potLain,0,',','.') }}<br><br>
                <strong>JUMLAH PENGHASILAN:</strong> Rp {{ number_format($totalP,0,',','.') }}<br>
                <strong>JUMLAH POTONGAN:</strong> Rp {{ number_format($totalPot,0,',','.') }}<br>
                <strong style="font-size:9px;">GAJI BERSIH: Rp {{ number_format($bersih,0,',','.') }}</strong>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dicetak secara elektronik oleh <b>Sistem Slip Gaji Kanwil Kemenag Provinsi Lampung</b><br>
        Tanggal cetak: {{ $tglCetak }}
    </div>

</div>
</body>
</html>