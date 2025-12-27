<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Koleksi E-Thesis</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #000; line-height: 1.4; }
        .header { width: 100%; border-bottom: 3px solid #059669; padding-bottom: 8px; margin-bottom: 12px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; }
        .header-text { text-align: center; vertical-align: middle; }
        .institution-name { font-size: 14px; font-weight: bold; color: #059669; text-transform: uppercase; }
        .sub-institution { font-size: 9px; color: #000; margin-top: 2px; }
        .report-title { background-color: #059669; color: #fff; padding: 8px 12px; margin: 10px 0; }
        .report-title h1 { font-size: 12px; font-weight: bold; margin-bottom: 2px; }
        .report-title p { font-size: 8px; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 10px; font-weight: bold; color: #000; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .stats-table td { width: 25%; padding: 4px; vertical-align: top; }
        .stat-box { background-color: #f3f4f6; border: 1px solid #d1d5db; padding: 10px; text-align: center; }
        .stat-box.green { background-color: #d1fae5; border-color: #34d399; }
        .stat-box.blue { background-color: #dbeafe; border-color: #60a5fa; }
        .stat-box.purple { background-color: #f3e8ff; border-color: #c084fc; }
        .stat-box.amber { background-color: #fef3c7; border-color: #fbbf24; }
        .stat-value { font-size: 18px; font-weight: bold; color: #000; }
        .stat-label { font-size: 7px; color: #4b5563; margin-top: 2px; text-transform: uppercase; }
        .data-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8px; }
        .data-table th { background-color: #059669; color: #fff; padding: 6px 8px; text-align: left; font-weight: bold; }
        .data-table td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        .data-table .number { text-align: right; font-weight: bold; }
        .data-table .total-row { background-color: #d1fae5; font-weight: bold; }
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 5px; }
        .two-col td:first-child { padding-left: 0; }
        .two-col td:last-child { padding-right: 0; }
        .summary-box { background-color: #d1fae5; border: 1px solid #34d399; padding: 10px; margin: 10px 0; }
        .summary-title { font-weight: bold; color: #059669; margin-bottom: 4px; font-size: 9px; }
        .summary-text { font-size: 8px; line-height: 1.5; }
        .source-badge { display: inline-block; padding: 2px 8px; font-size: 7px; font-weight: bold; margin: 2px; }
        .source-local { background-color: #dbeafe; color: #1e40af; }
        .source-repo { background-color: #fce7f3; color: #be185d; }
        .type-badge { display: inline-block; padding: 2px 8px; font-size: 7px; font-weight: bold; }
        .type-skripsi { background-color: #dbeafe; color: #1e40af; }
        .type-tesis { background-color: #f3e8ff; color: #7c3aed; }
        .type-disertasi { background-color: #fef3c7; color: #b45309; }
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
        <h1>LAPORAN KOLEKSI E-THESIS / TUGAS AKHIR</h1>
        <p>Dicetak: {{ $generatedAt }} oleh {{ $generatedBy }}</p>
    </div>

    <div class="section">
        <div class="section-title">RINGKASAN KOLEKSI E-THESIS</div>
        <table class="stats-table">
            <tr>
                <td><div class="stat-box green"><div class="stat-value">{{ number_format($totalEthesis) }}</div><div class="stat-label">Total E-Thesis</div></div></td>
                <td><div class="stat-box blue"><div class="stat-value">{{ number_format($fulltextPublic) }}</div><div class="stat-label">Fulltext Tersedia</div></div></td>
                <td><div class="stat-box purple"><div class="stat-value">{{ number_format($totalViews) }}</div><div class="stat-label">Total Dilihat</div></div></td>
                <td><div class="stat-box amber"><div class="stat-value">{{ number_format($totalDownloads) }}</div><div class="stat-label">Total Diunduh</div></div></td>
            </tr>
        </table>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">BERDASARKAN SUMBER DATA</div>
                    <table class="data-table">
                        <tr><th>No</th><th>Sumber</th><th>Jumlah</th><th>%</th></tr>
                        @php 
                            $sources = [
                                'local' => ['name' => 'Sistem Lokal', 'class' => 'source-local'],
                                'repo' => ['name' => 'EPrints Repository', 'class' => 'source-repo'],
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
                        <tr class="total-row">
                            <td colspan="2"><strong>TOTAL</strong></td>
                            <td class="number">{{ number_format($totalSource) }}</td>
                            <td class="number">100%</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">BERDASARKAN JENIS</div>
                    <table class="data-table">
                        <tr><th>No</th><th>Jenis</th><th>Jumlah</th><th>%</th></tr>
                        @php 
                            $types = ['skripsi' => 'Skripsi', 'tesis' => 'Tesis', 'disertasi' => 'Disertasi'];
                            $totalType = array_sum($byType);
                            $no = 1;
                        @endphp
                        @foreach($byType as $type => $count)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td><span class="type-badge type-{{ $type }}">{{ $types[$type] ?? ucfirst($type) }}</span></td>
                            <td class="number">{{ number_format($count) }}</td>
                            <td class="number">{{ $totalType > 0 ? round(($count / $totalType) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2"><strong>TOTAL</strong></td>
                            <td class="number">{{ number_format($totalType) }}</td>
                            <td class="number">100%</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">DISTRIBUSI PER PROGRAM STUDI</div>
        <table class="data-table">
            <tr>
                <th style="width:8%">No</th>
                <th style="width:52%">Program Studi</th>
                <th style="width:20%">Jumlah</th>
                <th style="width:20%">%</th>
            </tr>
            @php $totalDept = array_sum($byDepartment); $no = 1; @endphp
            @forelse($byDepartment as $dept => $count)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $dept }}</td>
                <td class="number">{{ number_format($count) }}</td>
                <td class="number">{{ $totalDept > 0 ? round(($count / $totalDept) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
            @if($totalDept > 0)
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="number">{{ number_format($totalDept) }}</td>
                <td class="number">100%</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">DISTRIBUSI PER TAHUN WISUDA</div>
        <table class="data-table">
            <tr>
                <th style="width:8%">No</th>
                <th style="width:30%">Tahun</th>
                <th style="width:30%">Jumlah</th>
                <th style="width:32%">%</th>
            </tr>
            @php $totalYear = array_sum($byYear); $no = 1; @endphp
            @forelse($byYear as $year => $count)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $year }}</td>
                <td class="number">{{ number_format($count) }}</td>
                <td class="number">{{ $totalYear > 0 ? round(($count / $totalYear) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
            @if($totalYear > 0)
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="number">{{ number_format($totalYear) }}</td>
                <td class="number">100%</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-title">RINGKASAN EKSEKUTIF</div>
        <div class="summary-text">
            Repositori E-Thesis UNIDA Gontor menyimpan <strong>{{ number_format($totalEthesis) }} karya ilmiah</strong> mahasiswa
            yang terdiri dari skripsi, tesis, dan disertasi. Data bersumber dari 
            <strong>Sistem Lokal</strong> (upload langsung) dan <strong>EPrints Repository</strong> (sinkronisasi otomatis).
            Sebanyak <strong>{{ number_format($fulltextPublic) }} karya ({{ $totalEthesis > 0 ? round(($fulltextPublic / $totalEthesis) * 100, 1) : 0 }}%)</strong> 
            menyediakan fulltext untuk diakses publik.
            Total akses: <strong>{{ number_format($totalViews) }} views</strong> dan <strong>{{ number_format($totalDownloads) }} downloads</strong>.
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
