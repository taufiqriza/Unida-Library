<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .certificate {
            width: 100%;
            border: 1pt solid #e2e8f0;
            border-radius: 20pt;
            padding: 5mm;
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
        .logo { height: 50pt; margin-bottom: 10pt; }
        .inst-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            margin-bottom: 3pt;
        }
        .inst-sub {
            font-size: 9pt;
            font-weight: bold;
            color: #c5a059;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }
        
        /* Title */
        .cert-title {
            font-size: 32pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3pt;
            margin: 20pt 0 5pt;
        }
        .cert-subtitle {
            font-size: 10pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 3pt;
            margin-bottom: 12pt;
        }
        .cert-id {
            background-color: #0f172a;
            color: #ffffff;
            padding: 4pt 15pt;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 20pt;
        }
        
        /* Recipient */
        .intro-text {
            font-size: 10pt;
            font-style: italic;
            color: #64748b;
            margin: 20pt 0 8pt;
        }
        .recipient-name {
            font-size: 24pt;
            font-weight: bold;
            display: inline-block;
            padding-bottom: 5pt;
            border-bottom: 2pt solid #c5a059;
            margin-bottom: 8pt;
        }
        .recipient-meta {
            font-size: 12pt;
            font-weight: 500;
            margin-bottom: 20pt;
        }
        
        /* Result Card */
        .result-card {
            background-color: #ffffff;
            border: 1pt solid #f1f5f9;
            border-radius: 15pt;
            padding: 15pt;
            margin: 0 auto 15pt;
            width: 95%;
        }
        .result-table { width: 100%; }
        .result-table td { vertical-align: middle; }
        .gauge-cell { width: 100pt; text-align: center; }
        .gauge-box {
            width: 90pt;
            height: 90pt;
            border: 6pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border-radius: 50pt;
            text-align: center;
            padding-top: 22pt;
            background-color: #f8fafc;
        }
        .percentage {
            font-size: 30pt;
            font-weight: bold;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
        }
        .gauge-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .doc-cell { text-align: right; padding-left: 15pt; }
        .doc-title-ar {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1.8;
            margin-bottom: 8pt;
            direction: rtl;
            text-align: right;
        }
        .doc-title-en {
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 8pt;
            text-align: right;
        }
        .doc-meta {
            font-size: 8pt;
            color: #64748b;
            text-align: right;
        }
        
        /* Status */
        .status-badge {
            background-color: {{ $isPassed ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border: 1pt solid {{ $isPassed ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }};
            padding: 6pt 18pt;
            font-size: 10pt;
            font-weight: bold;
            border-radius: 10pt;
            margin-bottom: 15pt;
        }
        .info-text {
            font-size: 9pt;
            color: #64748b;
            line-height: 1.7;
            max-width: 380pt;
            margin: 0 auto 20pt;
        }
        
        /* Footer */
        .footer-table { width: 100%; }
        .footer-table td { vertical-align: bottom; }
        .footer-left { text-align: left; width: 50%; padding-left: 20pt; }
        .footer-right { text-align: right; width: 50%; padding-right: 20pt; }
        .location-date {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 35pt;
        }
        .signer-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2pt;
        }
        .signer-title {
            font-size: 9pt;
            color: #c5a059;
            font-weight: bold;
        }
        .qr-box {
            width: 60pt;
            height: 60pt;
            border: 1pt solid #e2e8f0;
            border-radius: 8pt;
            padding: 4pt;
            display: inline-block;
        }
        .qr-box img { width: 100%; height: 100%; }
        .verify-url {
            font-size: 6pt;
            color: #64748b;
            margin-top: 5pt;
            word-break: break-all;
        }
        .verify-note {
            font-size: 6pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-top: 3pt;
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
        <div class="signer-name">{{ $headLibrarian }}</div>
        <div class="signer-title">Kepala Perpustakaan</div>
    </td>
    <td class="footer-right">
        <div class="qr-box"><img src="{{ $qrCode }}" alt="QR"></div>
        <div class="verify-url">{{ $verifyUrl }}</div>
        <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
    </td>
</tr>
</table>

</div>
</div>
</body>
</html>
