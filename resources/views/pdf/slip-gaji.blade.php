<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 24px;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }

        .header h2 {
            font-size: 14px;
            margin: 4px 0 0 0;
            font-weight: normal;
        }

        .title {
            margin-top: 12px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
            background: #166534;
            color: #fff;
            border-radius: 6px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            margin-bottom: 16px;
        }

        .meta td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .card-header {
            background: #f3f4f6;
            padding: 10px 12px;
            font-weight: bold;
        }

        .card-body {
            padding: 10px 12px;
        }

        table.detail {
            width: 100%;
            border-collapse: collapse;
        }

        table.detail td {
            padding: 7px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        table.detail tr:last-child td {
            border-bottom: none;
        }

        .label {
            width: 70%;
        }

        .value {
            width: 30%;
            text-align: right;
            font-weight: bold;
        }

        .total {
            font-weight: bold;
            color: #166534;
        }

        .danger {
            color: #b91c1c;
        }

        .green-box {
            margin-top: 14px;
            background: #dcfce7;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }

        .green-box .amount {
            font-size: 18px;
            font-weight: bold;
            margin-top: 6px;
        }

        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #6b7280;
        }

        .muted {
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>KEMENTERIAN AGAMA</h1>
        <h2>KANTOR WILAYAH PROVINSI LAMPUNG</h2>
    </div>

    <div class="title">
        SLIP GAJI PEGAWAI - {{ $slip->bulan }} {{ $slip->tahun }}
    </div>

    <table class="meta">
        <tr>
            <td width="25%"><strong>Nama</strong></td>
            <td width="35%">: {{ $rincian['pegawai']['nama'] ?? '-' }}</td>
            <td width="20%"><strong>Bulan</strong></td>
            <td width="20%">: {{ $slip->bulan }}</td>
        </tr>
        <tr>
            <td><strong>NIP</strong></td>
            <td>: {{ $rincian['pegawai']['nip'] ?? '-' }}</td>
            <td><strong>Tahun</strong></td>
            <td>: {{ $slip->tahun }}</td>
        </tr>
        <tr>
            <td><strong>Golongan</strong></td>
            <td>: {{ $rincian['pegawai']['golongan'] ?? '-' }}</td>
            <td><strong>Tanggal Terbit</strong></td>
            <td>: {{ optional($slip->tanggal_terbit)->format('d/m/Y') ?? now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>: {{ $rincian['pegawai']['jabatan'] ?? '-' }}</td>
            <td><strong>Unit Kerja</strong></td>
            <td>: {{ $slip->pegawai->unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Rekening</strong></td>
            <td>: {{ $rincian['pegawai']['rekening'] ?? '-' }}</td>
            <td><strong>Bank</strong></td>
            <td>: {{ $rincian['pegawai']['bank'] ?? '-' }}</td>
        </tr>
    </table>

    <div class="card">
        <div class="card-header">Rincian Penghasilan</div>
        <div class="card-body">
            <table class="detail">
                @forelse(($rincian['pendapatan'] ?? []) as $nama => $nominal)
                    <tr>
                        <td class="label">{{ $nama }}</td>
                        <td class="value">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="muted">Tidak ada data penghasilan.</td>
                    </tr>
                @endforelse

                <tr>
                    <td class="label total">Total Penghasilan</td>
                    <td class="value total">
                        Rp {{ number_format($rincian['total_pendapatan'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Rincian Potongan</div>
        <div class="card-body">
            <table class="detail">
                @forelse(($rincian['potongan'] ?? []) as $nama => $nominal)
                    <tr>
                        <td class="label">{{ $nama }}</td>
                        <td class="value danger">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="muted">Tidak ada data potongan.</td>
                    </tr>
                @endforelse

                <tr>
                    <td class="label total danger">Total Potongan</td>
                    <td class="value total danger">
                        Rp {{ number_format($rincian['total_potongan'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="green-box">
        <div>GAJI BERSIH</div>
        <div class="amount">
            Rp {{ number_format($rincian['gaji_bersih'] ?? 0, 0, ',', '.') }}
        </div>
    </div>

    <div class="footer">
        Dokumen ini diterbitkan secara elektronik oleh
        <b>Sistem Dashboard Slip Gaji Pegawai Kanwil Kementerian Agama Provinsi Lampung.</b>
        <br>
        Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>