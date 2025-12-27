<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Koleksi Perpustakaan</title>
    <style>
        @page { margin: 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #000; line-height: 1.3; }
        
        .header { width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 6px; margin-bottom: 10px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 50px; vertical-align: middle; }
        .header-logo img { width: 40px; height: 40px; }
        .header-text { text-align: center; vertical-align: middle; }
        .institution-name { font-size: 12px; font-weight: bold; color: #1e40af; text-transform: uppercase; }
        .sub-institution { font-size: 8px; color: #000; margin-top: 2px; }
        
        .report-title { background-color: #1e40af; color: #fff; padding: 6px 10px; margin: 8px 0; }
        .report-title h1 { font-size: 11px; font-weight: bold; margin-bottom: 2px; }
        .report-title p { font-size: 7px; }
        
        .summary { background-color: #f3f4f6; border: 1px solid #d1d5db; padding: 6px 10px; margin-bottom: 10px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { font-size: 8px; padding: 2px 8px; }
        .summary-label { color: #4b5563; }
        .summary-value { font-weight: bold; }
        
        .data-table { width: 100%; border-collapse: collapse; font-size: 7px; }
        .data-table th { background-color: #1e40af; color: #fff; padding: 5px 4px; text-align: left; font-weight: bold; }
        .data-table td { padding: 4px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        .data-table .no { width: 4%; text-align: center; }
        .data-table .title { width: 36%; }
        .data-table .copies { width: 6%; text-align: center; }
        .data-table .place { width: 12%; }
        .data-table .publisher { width: 18%; }
        .data-table .isbn { width: 12%; }
        .data-table .callno { width: 12%; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 6px; color: #6b7280; border-top: 1px solid #d1d5db; padding-top: 4px; }
        .footer-table { width: 100%; }
        
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @endif
                </td>
                <td class="header-text">
                    <div class="institution-name">Perpustakaan Universitas Darussalam Gontor</div>
                    <div class="sub-institution">Jl. Raya Siman Km. 6, Ponorogo, Jawa Timur 63471</div>
                </td>
                <td style="width: 50px;"></td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h1>DAFTAR KOLEKSI PERPUSTAKAAN</h1>
        <p>Cabang: {{ $branchName }} | Dicetak: {{ $generatedAt }}</p>
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td style="width:25%"><span class="summary-label">Total Judul:</span> <span class="summary-value">{{ number_format($totalTitles) }}</span></td>
                <td style="width:25%"><span class="summary-label">Total Eksemplar:</span> <span class="summary-value">{{ number_format($totalCopies) }}</span></td>
                <td style="width:25%"><span class="summary-label">Dicetak Oleh:</span> <span class="summary-value">{{ $generatedBy }}</span></td>
                <td style="width:25%"><span class="summary-label">Tanggal:</span> <span class="summary-value">{{ now()->format('d/m/Y') }}</span></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="title">Judul</th>
                <th class="copies">Salin</th>
                <th class="place">Tempat Terbit</th>
                <th class="publisher">Penerbit</th>
                <th class="isbn">ISBN/ISSN</th>
                <th class="callno">No. Panggil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $i => $book)
            <tr>
                <td class="no">{{ $i + 1 }}</td>
                <td class="title">{{ $book['title'] }}</td>
                <td class="copies">{{ $book['copies'] }}</td>
                <td class="place">{{ $book['publish_place'] ?? '-' }}</td>
                <td class="publisher">{{ $book['publisher'] ?? '-' }}</td>
                <td class="isbn">{{ $book['isbn'] ?? '-' }}</td>
                <td class="callno">{{ $book['call_number'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Dicetak: {{ $generatedAt }} | Total: {{ number_format($totalTitles) }} judul, {{ number_format($totalCopies) }} eksemplar</td>
                <td style="text-align: right;">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
