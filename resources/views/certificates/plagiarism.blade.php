<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .certificate {
            width: 100%;
            border: 1pt solid #e2e8f0;
            border-radius: 20pt;
            padding: 8mm;
            position: relative;
        }
        .inner {
            border: 3pt solid #c5a059;
            border-top-left-radius: 20pt;
            border-bottom-right-radius: 20pt;
            border-top-right-radius: 0;
            border-bottom-left-radius: 0;
            padding: 10mm;
        }
        .center { text-align: center; }
        
        /* Header */
        .logo { height: 45pt; margin-bottom: 8pt; }
        .inst-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5pt;
            margin-bottom: 2pt;
        }
        .inst-sub {
            font-size: 8pt;
            font-weight: bold;
            color: #c5a059;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            margin-bottom: 12pt;
        }
        
        /* Title */
        .cert-title {
            font-size: 26pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.8pt;
            margin: 12pt 0 3pt;
        }
        .cert-subtitle {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5pt;
            margin-bottom: 8pt;
        }
        .cert-id {
            background-color: #0f172a;
            color: #ffffff;
            padding: 2pt 10pt;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 12pt;
            margin-bottom: 12pt;
        }
        
        /* Recipient */
        .intro-text {
            font-size: 8pt;
            font-style: italic;
            color: #64748b;
            margin: 10pt 0 4pt;
        }
        .recipient-name {
            font-size: 18pt;
            font-weight: bold;
            display: inline-block;
            padding-bottom: 3pt;
            border-bottom: 1.5pt solid #c5a059;
            margin-bottom: 4pt;
        }
        .recipient-meta {
            font-size: 10pt;
            font-weight: 500;
            margin-bottom: 10pt;
        }
        
        /* Result Card */
        .result-card {
            background-color: #ffffff;
            border: 1pt solid #f1f5f9;
            border-radius: 10pt;
            padding: 8pt;
            margin: 0 auto 8pt;
            width: 95%;
            box-shadow: 0 1pt 2pt rgba(0, 0, 0, 0.05);
        }
        .result-table { width: 100%; border-collapse: collapse; }
        .result-table td { vertical-align: middle; padding: 0; }
        .gauge-cell { width: 70pt; text-align: center; }
        .gauge-box {
            width: 65pt;
            height: 65pt;
            border: 4pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border-radius: 32pt;
            text-align: center;
            padding-top: 14pt;
            background-color: #f8fafc;
            margin: 0 auto;
        }
        .percentage {
            font-size: 22pt;
            font-weight: bold;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            line-height: 1;
        }
        .gauge-label {
            font-size: 6pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 2pt;
        }
        .doc-cell { text-align: right; padding-left: 10pt; }
        .doc-title-ar {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 4pt;
            direction: rtl;
            text-align: right;
        }
        .doc-title-en {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 4pt;
            text-align: right;
        }
        .doc-meta {
            font-size: 7pt;
            color: #64748b;
            text-align: right;
            line-height: 1.2;
        }
        
        /* Status */
        .status-badge {
            background-color: {{ $isPassed ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border: 1pt solid {{ $isPassed ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }};
            padding: 4pt 12pt;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 6pt;
            margin-bottom: 8pt;
        }
        .info-text {
            font-size: 7pt;
            color: #64748b;
            line-height: 1.3;
            max-width: 350pt;
            margin: 0 auto 10pt;
        }
        
        /* Footer */
        .footer-table { width: 100%; border-collapse: collapse; margin-top: 15pt; }
        .footer-table td { vertical-align: top; padding: 0; }
        .footer-left { 
            text-align: left; 
            width: 40%; 
            padding-right: 10pt;
        }
        .footer-right { 
            text-align: right; 
            width: 60%; 
            padding-left: 10pt;
        }
        .location-date {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 20pt;
        }
        .signer-name {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 1pt;
            line-height: 1.1;
            max-width: 120pt;
        }
        .signer-title {
            font-size: 7pt;
            color: #c5a059;
            font-weight: bold;
        }
        .qr-box {
            width: 32pt;
            height: 32pt;
            border: 1pt solid #e2e8f0;
            border-radius: 3pt;
            padding: 1pt;
            display: inline-block;
            margin-bottom: 3pt;
        }
        .qr-box img { width: 100%; height: 100%; }
        .qr-head {
            width: 28pt;
            height: 28pt;
            border: 1pt solid #c5a059;
            border-radius: 3pt;
            padding: 1pt;
            display: inline-block;
            margin-bottom: 3pt;
        }
        .qr-head img { width: 100%; height: 100%; }
        .verify-url {
            font-size: 4pt;
            color: #64748b;
            margin-top: 2pt;
            word-break: break-all;
            line-height: 1.1;
        }
        .verify-note {
            font-size: 4pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
            margin-top: 1pt;
            line-height: 1.1;
        }
    </style>
</head>
<body>
<div class="certificate">
<div class="inner">
<div class="center">

    <!-- Header -->
    @if($institutionLogo)
    <img src="{{ $institutionLogo }}" class="logo"><br>
    @endif
    <div class="inst-name">{{ $institutionName }}</div>
    <div class="inst-sub">Perpustakaan &amp; Unida Library</div>

    <!-- Title -->
    <div class="cert-title">Sertifikat</div>
    <div class="cert-subtitle">Originalitas Dokumen Akademik</div>
    <span class="cert-id">No: {{ $check->certificate_number }}</span>

    <!-- Recipient -->
    <div class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</div>
    <div class="recipient-name">{{ strtoupper($member->name) }}</div><br>
    <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>

</div>

<!-- Result Card -->
<div class="result-card">
<table class="result-table">
<tr>
    <td class="gauge-cell">
        <div class="gauge-box">
            <div class="percentage">{{ number_format($check->similarity_score, 0) }}%</div>
            <div class="gauge-label">Similarity</div>
        </div>
    </td>
    <td class="doc-cell">
        @if($hasArabicTitle ?? false)
        <div class="doc-title-ar">{{ $check->document_title }}</div>
        @else
        <div class="doc-title-en">{{ $check->document_title }}</div>
        @endif
        <div class="doc-meta">
            File: {{ $check->original_filename }}<br>
            Diperiksa: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
        </div>
    </td>
</tr>
</table>
</div>

<div class="center">
    <!-- Status -->
    <span class="status-badge">
        @if($isPassed)✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
        @else⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI @endif
    </span>

    <!-- Info -->
    <div class="info-text">
        Dokumen telah diperiksa menggunakan iThenticate by Turnitin dengan standar internasional.
        Tingkat kemiripan {{ $isPassed ? 'di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
    </div>
</div>

<!-- Footer -->
<table class="footer-table">
<tr>
    <td class="footer-left">
        <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
        <div class="qr-head"><img src="{{ $qrHeadLibrarian }}" alt="QR Kepala"></div>
        <div class="signer-name">{{ $headLibrarian }}</div>
        <div class="signer-title">Kepala Perpustakaan</div>
    </td>
    <td class="footer-right">
        <div class="qr-box"><img src="{{ $qrCode }}" alt="QR Verify"></div>
        <div class="verify-url">{{ $verifyUrl }}</div>
        <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
    </td>
</tr>
</table>

</div>
</div>
</body>
</html>
