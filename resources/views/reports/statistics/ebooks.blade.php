<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Koleksi E-Book Digital</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #000; line-height: 1.4; }
        .header { width: 100%; border-bottom: 3px solid #7c3aed; padding-bottom: 8px; margin-bottom: 12px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; }
        .header-text { text-align: center; vertical-align: middle; }
        .institution-name { font-size: 14px; font-weight: bold; color: #7c3aed; text-transform: uppercase; }
        .sub-institution { font-size: 9px; color: #000; margin-top: 2px; }
        .report-title { background-color: #7c3aed; color: #fff; padding: 8px 12px; margin: 10px 0; }
        .report-title h1 { font-size: 12px; font-weight: bold; margin-bottom: 2px; }
        .report-title p { font-size: 8px; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 10px; font-weight: bold; color: #000; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .stats-table td { width: 25%; padding: 4px; vertical-align: top; }
        .stat-box { background-color: #f3f4f6; border: 1px solid #d1d5db; padding: 10px; text-align: center; }
        .stat-box.purple { background-color: #f3e8ff; border-color: #c084fc; }
        .stat-box.blue { background-color: #dbeafe; border-color: #60a5fa; }
        .stat-box.green { background-color: #d1fae5; border-color: #34d399; }
        .stat-box.amber { background-color: #fef3c7; border-color: #fbbf24; }
        .stat-value { font-size: 18px; font-weight: bold; color: #000; }
        .stat-label { font-size: 7px; color: #4b5563; margin-top: 2px; text-transform: uppercase; }
        .data-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8px; }
        .data-table th { background-color: #7c3aed; color: #fff; padding: 6px 8px; text-align: left; font-weight: bold; }
        .data-table td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        .data-table .number { text-align: right; font-weight: bold; }
        .data-table .total-row { background-color: #f3e8ff; font-weight: bold; }
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 5px; }
        .two-col td:first-child { padding-left: 0; }
        .two-col td:last-child { padding-right: 0; }
        .summary-box { background-color: #f3e8ff; border: 1px solid #c084fc; padding: 10px; margin: 10px 0; }
        .summary-title { font-weight: bold; color: #7c3aed; margin-bottom: 4px; font-size: 9px; }
        .summary-text { font-size: 8px; line-height: 1.5; }
        .source-badge { display: inline-block; padding: 2px 8px; font-size: 7px; font-weight: bold; margin: 2px; }
        .source-local { background-color: #dbeafe; color: #1e40af; }
        .source-gdrive { background-color: #fef3c7; color: #b45309; }
        .source-kubuku { background-color: #d1fae5; color: #047857; }
        .source-shamela { background-color: #fce7f3; color: #be185d; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 7px; color: #6b7280; border-top: 1px solid #d1d5db; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">@if($logoBase64)<img src="{{ $logoBase64 }}" alt="Logo">@endif</td>
                <td class="header-text">
                    <div class="institution-name">Perpustakaan Universitas Darussalam Gontor</div>
                    <div class="sub-institution">Jl. Raya Siman Km. 6, Ponorogo, Jawa Timur 63471</div>
                </td>
                <td style="width: 60px;"></td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h1>LAPORAN KOLEKSI E-BOOK DIGITAL</h1>
        <p>Dicetak: {{ $generatedAt }} oleh {{ $generatedBy }}</p>
    </div>

    <div class="section">
        <div class="section-title">RINGKASAN KOLEKSI E-BOOK</div>
        <table class="stats-table">
            <tr>
                <td><div class="stat-box purple"><div class="stat-value">{{ number_format($totalEbooks) }}</div><div class="stat-label">Total E-Book</div></div></td>
                <td><div class="stat-box blue"><div class="stat-value">{{ number_format($shamelaCount) }}</div><div class="stat-label">Maktabah Syamilah</div></div></td>
                <td><div class="stat-box green"><div class="stat-value">{{ number_format($totalViews) }}</div><div class="stat-label">Total Dilihat</div></div></td>
                <td><div class="stat-box amber"><div class="stat-value">{{ number_format($totalDownloads) }}</div><div class="stat-label">Total Diunduh</div></div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DISTRIBUSI BERDASARKAN SUMBER</div>
        <table class="data-table">
            <tr>
                <th style="width:10%">No</th>
                <th style="width:50%">Sumber E-Book</th>
                <th style="width:20%">Jumlah</th>
                <th style="width:20%">%</th>
            </tr>
            @php 
                $sources = [
                    'local' => ['name' => 'Koleksi Lokal (Upload)', 'class' => 'source-local'],
                    'google_drive' => ['name' => 'Google Drive', 'class' => 'source-gdrive'],
                    'kubuku' => ['name' => 'Kubuku Digital', 'class' => 'source-kubuku'],
                ];
                $totalSource = array_sum($bySource);
                $no = 1;
            @endphp
            @foreach($bySource as $source => $count)
            <tr>
                <td>{{ $no++ }}</td>
                <td><span class="source-badge {{ $sources[$source]['class'] ?? 'source-local' }}">{{ $sources[$source]['name'] ?? ucfirst($source) }}</span></td>
                <td class="number">{{ number_format($count) }}</td>
                <td class="number">{{ $totalSource > 0 ? round(($count / $totalSource) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            @if($shamelaCount > 0)
            <tr>
                <td>{{ $no++ }}</td>
                <td><span class="source-badge source-shamela">Maktabah Syamilah</span></td>
                <td class="number">{{ number_format($shamelaCount) }}</td>
                <td class="number">-</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL KOLEKSI DIGITAL</strong></td>
                <td class="number">{{ number_format($totalSource + $shamelaCount) }}</td>
                <td class="number">100%</td>
            </tr>
        </table>
        <p style="font-size:7px;color:#6b7280;margin-top:4px;">* Open Library API tersedia sebagai sumber eksternal untuk pencarian</p>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">BERDASARKAN KATEGORI</div>
                    <table class="data-table">
                        <tr><th>No</th><th>Kategori</th><th>Jumlah</th></tr>
                        @php $no = 1; @endphp
                        @forelse($byCategory as $cat => $count)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $cat }}</td>
                            <td class="number">{{ number_format($count) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center">-</td></tr>
                        @endforelse
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">BERDASARKAN TAHUN TERBIT</div>
                    <table class="data-table">
                        <tr><th>No</th><th>Tahun</th><th>Jumlah</th></tr>
                        @php $no = 1; @endphp
                        @forelse($byYear as $year => $count)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $year }}</td>
                            <td class="number">{{ number_format($count) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center">-</td></tr>
                        @endforelse
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="summary-box">
        <div class="summary-title">RINGKASAN EKSEKUTIF</div>
        <div class="summary-text">
            Perpustakaan UNIDA Gontor memiliki <strong>{{ number_format($totalEbooks) }} koleksi e-book</strong> dari berbagai sumber:
            koleksi lokal yang diupload langsung, integrasi dengan Google Drive, dan layanan Kubuku Digital.
            Selain itu, tersedia <strong>{{ number_format($shamelaCount) }} kitab</strong> dari Maktabah Syamilah untuk kajian keislaman.
            Koleksi digital telah diakses sebanyak <strong>{{ number_format($totalViews) }} kali</strong> 
            dengan <strong>{{ number_format($totalDownloads) }} unduhan</strong>.
            Pencarian juga terintegrasi dengan Open Library API untuk akses jutaan buku digital internasional.
        </div>
    </div>

    <div class="footer">
        <table style="width:100%">
            <tr>
                <td>Dicetak: {{ $generatedAt }} oleh {{ $generatedBy }}</td>
                <td style="text-align:right">Dokumen digenerate otomatis oleh Sistem Perpustakaan</td>
            </tr>
        </table>
    </div>
</body>
</html>
