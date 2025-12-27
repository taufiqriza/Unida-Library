<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan Statistik Perpustakaan')</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #000; line-height: 1.4; }
        
        .header { width: 100%; border-bottom: 3px solid #1e40af; padding-bottom: 8px; margin-bottom: 12px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; }
        .header-text { text-align: center; vertical-align: middle; }
        .institution-name { font-size: 14px; font-weight: bold; color: #1e40af; text-transform: uppercase; }
        .sub-institution { font-size: 9px; color: #000; margin-top: 2px; }
        
        .report-title { background-color: #1e40af; color: #fff; padding: 8px 12px; margin: 10px 0; }
        .report-title h1 { font-size: 12px; font-weight: bold; margin-bottom: 2px; }
        .report-title p { font-size: 8px; }
        
        .meta-box { background-color: #f3f4f6; border: 1px solid #d1d5db; padding: 8px; margin-bottom: 12px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { font-size: 8px; padding: 2px 4px; color: #000; }
        .meta-label { color: #4b5563; }
        .meta-value { font-weight: bold; }
        
        .section { margin-bottom: 15px; }
        .section-title { font-size: 10px; font-weight: bold; color: #000; border-bottom: 2px solid #d1d5db; padding-bottom: 4px; margin-bottom: 8px; }
        
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .stats-table td { width: 25%; padding: 4px; vertical-align: top; }
        .stat-box { background-color: #f3f4f6; border: 1px solid #d1d5db; padding: 8px; text-align: center; }
        .stat-value { font-size: 16px; font-weight: bold; color: #000; }
        .stat-label { font-size: 7px; color: #000; margin-top: 2px; text-transform: uppercase; }
        
        .data-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8px; }
        .data-table th { background-color: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-weight: bold; font-size: 8px; }
        .data-table td { padding: 5px 8px; border-bottom: 1px solid #d1d5db; color: #000; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        .data-table .number { text-align: right; font-weight: bold; }
        .data-table .total-row { background-color: #e5e7eb; font-weight: bold; }
        
        .alert-box { padding: 8px 10px; margin: 8px 0; border-left: 3px solid; color: #000; }
        .alert-box.danger { background-color: #fee2e2; border-color: #ef4444; }
        .alert-box.success { background-color: #d1fae5; border-color: #10b981; }
        .alert-box.info { background-color: #dbeafe; border-color: #3b82f6; }
        
        .summary-box { background-color: #f0f9ff; border: 1px solid #93c5fd; padding: 10px; margin: 10px 0; }
        .summary-title { font-weight: bold; color: #000; margin-bottom: 4px; font-size: 9px; }
        .summary-text { font-size: 8px; line-height: 1.5; color: #000; }
        
        .badge { display: inline-block; padding: 1px 6px; font-size: 7px; font-weight: bold; }
        .badge-success { background-color: #d1fae5; color: #047857; }
        .badge-danger { background-color: #fee2e2; color: #dc2626; }
        .badge-primary { background-color: #dbeafe; color: #1e40af; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 5px; }
        .two-col td:first-child { padding-left: 0; }
        .two-col td:last-child { padding-right: 0; }
        
        .page-break { page-break-after: always; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 7px; color: #6b7280; border-top: 1px solid #d1d5db; padding-top: 5px; }
        .footer-table { width: 100%; }
        
        .signature-table { width: 100%; margin-top: 30px; }
        .signature-table td { width: 50%; text-align: center; padding: 10px; vertical-align: top; }
        .signature-line { border-top: 1px solid #000; width: 70%; margin: 50px auto 0; padding-top: 4px; }
        .signature-name { font-weight: bold; font-size: 9px; color: #000; }
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
                <td style="width: 60px;"></td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Dicetak: {{ $generatedAt }} oleh {{ $generatedBy }}</td>
                <td style="text-align: right;">Dokumen digenerate otomatis oleh Sistem Perpustakaan</td>
            </tr>
        </table>
    </div>
</body>
</html>
