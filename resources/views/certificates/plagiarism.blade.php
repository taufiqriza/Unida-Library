<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        table { border-collapse: collapse; }
        .main-table {
            width: 100%;
            height: 277mm;
            border: 1pt solid #e2e8f0;
        }
        .main-cell {
            padding: 8mm;
            vertical-align: top;
        }
        /* Corner borders using nested table */
        .corner-table {
            width: 100%;
            height: 100%;
        }
        .corner-tl {
            width: 20mm;
            height: 20mm;
            border-top: 3pt solid #c5a059;
            border-left: 3pt solid #c5a059;
        }
        .corner-tr { width: 20mm; height: 20mm; }
        .corner-bl { width: 20mm; height: 20mm; }
        .corner-br {
            width: 20mm;
            height: 20mm;
            border-bottom: 3pt solid #c5a059;
            border-right: 3pt solid #c5a059;
        }
        .content-cell {
            vertical-align: top;
            text-align: center;
            padding: 0 5mm;
        }
        /* Header */
        .logo { height: 45pt; margin-bottom: 8pt; }
        .inst-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin-bottom: 3pt;
        }
        .inst-sub {
            font-size: 8pt;
            font-weight: bold;
            color: #c5a059;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }
        /* Title */
        .cert-title {
            font-size: 28pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            margin: 15pt 0 5pt;
        }
        .cert-subtitle {
            font-size: 9pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2pt;
            margin-bottom: 10pt;
        }
        .cert-id {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            padding: 3pt 12pt;
            font-size: 8pt;
            font-weight: bold;
        }
        /* Recipient */
        .intro-text {
            font-size: 9pt;
            font-style: italic;
            color: #64748b;
            margin: 15pt 0 5pt;
        }
        .recipient-name {
            font-size: 20pt;
            font-weight: bold;
            border-bottom: 2pt solid #c5a059;
            display: inline-block;
            padding-bottom: 3pt;
            margin-bottom: 5pt;
        }
        .recipient-meta {
            font-size: 10pt;
            margin-bottom: 12pt;
        }
        /* Result */
        .result-table {
            width: 90%;
            margin: 0 auto 10pt;
            border: 1pt solid #e5e7eb;
        }
        .result-table td {
            padding: 8pt;
            vertical-align: middle;
        }
        .gauge-cell {
            width: 80pt;
            text-align: center;
        }
        .gauge-box {
            width: 70pt;
            height: 70pt;
            border: 5pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border-radius: 50%;
            text-align: center;
            padding-top: 15pt;
        }
        .percentage {
            font-size: 24pt;
            font-weight: bold;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
        }
        .gauge-label {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
        }
        .doc-cell {
            text-align: right;
            padding-right: 10pt;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 5pt;
        }
        .doc-meta {
            font-size: 7pt;
            color: #64748b;
        }
        /* Status */
        .status-badge {
            display: inline-block;
            background-color: {{ $isPassed ? '#dcfce7' : '#fef3c7' }};
            color: {{ $isPassed ? '#059669' : '#d97706' }};
            border: 1pt solid {{ $isPassed ? '#86efac' : '#fcd34d' }};
            padding: 4pt 15pt;
            font-size: 9pt;
            font-weight: bold;
            margin: 8pt 0;
        }
        .info-text {
            font-size: 8pt;
            color: #64748b;
            line-height: 1.6;
            margin: 0 auto 15pt;
            max-width: 400pt;
        }
        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 10pt;
        }
        .footer-table td {
            vertical-align: bottom;
            padding: 5pt 15pt;
        }
        .footer-left { text-align: left; width: 50%; }
        .footer-right { text-align: right; width: 50%; }
        .location-date {
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 30pt;
        }
        .signer-name {
            font-size: 10pt;
            font-weight: bold;
        }
        .signer-title {
            font-size: 8pt;
            color: #c5a059;
            font-weight: bold;
        }
        .qr-img {
            width: 55pt;
            height: 55pt;
            border: 1pt solid #e5e7eb;
            padding: 2pt;
        }
        .verify-url {
            font-size: 5pt;
            color: #64748b;
            margin-top: 3pt;
        }
        .verify-note {
            font-size: 5pt;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2pt;
        }
    </style>
</head>
<body>
<table class="main-table">
<tr><td class="main-cell">
<table class="corner-table">
<tr>
    <td class="corner-tl"></td>
    <td class="content-cell" rowspan="3">

        <!-- HEADER -->
        @if($institutionLogo)
        <img src="{{ $institutionLogo }}" class="logo"><br>
        @endif
        <div class="inst-name">{{ $institutionName }}</div>
        <div class="inst-sub">Perpustakaan &amp; Unida Library</div>

        <!-- TITLE -->
        <div class="cert-title">Sertifikat</div>
        <div class="cert-subtitle">Originalitas Dokumen Akademik</div>
        <span class="cert-id">No: {{ $check->certificate_number }}</span>

        <!-- RECIPIENT -->
        <div class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</div>
        <div class="recipient-name">{{ strtoupper($member->name) }}</div>
        <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>

        <!-- RESULT -->
        <table class="result-table">
        <tr>
            <td class="gauge-cell">
                <div class="gauge-box">
                    <div class="percentage">{{ number_format($check->similarity_score, 0) }}%</div>
                    <div class="gauge-label">Similarity</div>
                </div>
            </td>
            <td class="doc-cell">
                <div class="doc-title">{{ $check->document_title }}</div>
                <div class="doc-meta">
                    File: {{ $check->original_filename }}<br>
                    Diperiksa: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                </div>
            </td>
        </tr>
        </table>

        <!-- STATUS -->
        <div class="status-badge">
            @if($isPassed)✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
            @else⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI @endif
        </div>

        <!-- INFO -->
        <div class="info-text">
            Dokumen telah diperiksa menggunakan iThenticate by Turnitin dengan standar internasional.
            Tingkat kemiripan {{ $isPassed ? 'di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
        </div>

        <!-- FOOTER -->
        <table class="footer-table">
        <tr>
            <td class="footer-left">
                <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
                <div class="signer-name">{{ $headLibrarian }}</div>
                <div class="signer-title">Kepala Perpustakaan</div>
            </td>
            <td class="footer-right">
                <img src="{{ $qrCode }}" class="qr-img"><br>
                <div class="verify-url">{{ $verifyUrl }}</div>
                <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
            </td>
        </tr>
        </table>

    </td>
    <td class="corner-tr"></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td class="corner-bl"></td>
    <td class="corner-br"></td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>
