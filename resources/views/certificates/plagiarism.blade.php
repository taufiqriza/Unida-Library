<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page {
            size: 210mm 297mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            color: #0f172a;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 15mm;
            position: relative;
        }

        /* Border Dekoratif */
        .border-outer {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 1pt solid #e2e8f0;
            border-radius: 8mm;
        }

        /* Corner Accents */
        .corner-tl {
            position: absolute;
            top: 10mm;
            left: 10mm;
            width: 25mm;
            height: 25mm;
            border-top: 3pt solid #c5a059;
            border-left: 3pt solid #c5a059;
            border-top-left-radius: 8mm;
        }

        .corner-br {
            position: absolute;
            bottom: 10mm;
            right: 10mm;
            width: 25mm;
            height: 25mm;
            border-bottom: 3pt solid #c5a059;
            border-right: 3pt solid #c5a059;
            border-bottom-right-radius: 8mm;
        }

        /* Content */
        .content {
            position: relative;
            padding: 8mm 12mm;
            text-align: center;
        }

        /* Header */
        .header {
            margin-bottom: 8mm;
        }

        .logo-box {
            width: 18mm;
            height: 18mm;
            margin: 0 auto 4mm;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
        }

        .inst-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            color: #0f172a;
            margin-bottom: 2mm;
        }

        .inst-sub {
            font-size: 9pt;
            font-weight: bold;
            color: #c5a059;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }

        /* Title */
        .title-block {
            margin-bottom: 8mm;
        }

        .cert-title {
            font-size: 32pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 3pt;
            margin-bottom: 2mm;
        }

        .cert-subtitle {
            font-size: 10pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 3pt;
            margin-bottom: 4mm;
        }

        .cert-id {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            padding: 2mm 5mm;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 10mm;
        }

        /* Recipient */
        .recipient-block {
            margin-bottom: 8mm;
        }

        .intro-text {
            font-size: 10pt;
            font-style: italic;
            color: #64748b;
            margin-bottom: 3mm;
        }

        .recipient-name {
            font-size: 22pt;
            font-weight: bold;
            color: #0f172a;
            padding-bottom: 2mm;
            border-bottom: 2pt solid #c5a059;
            display: inline-block;
            margin-bottom: 3mm;
        }

        .recipient-meta {
            font-size: 11pt;
            color: #0f172a;
            font-weight: 500;
        }

        /* Result Card */
        .result-card {
            border: 1pt solid #f1f5f9;
            border-radius: 6mm;
            padding: 5mm;
            margin: 0 8mm 6mm 8mm;
        }

        .result-table {
            width: 100%;
        }

        .gauge-cell {
            width: 35mm;
            vertical-align: middle;
            text-align: center;
        }

        .gauge-circle {
            width: 32mm;
            height: 32mm;
            border: 6pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            padding-top: 7mm;
        }

        .percentage {
            font-size: 28pt;
            font-weight: bold;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 7pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }

        .doc-cell {
            vertical-align: middle;
            text-align: right;
            padding-left: 5mm;
        }

        .doc-title-ar {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.6;
            margin-bottom: 3mm;
            direction: rtl;
            text-align: right;
        }

        .doc-title-en {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.5;
            margin-bottom: 3mm;
            text-align: right;
        }

        .doc-meta {
            font-size: 8pt;
            color: #64748b;
            text-align: right;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            background-color: {{ $isPassed ? '#dcfce7' : '#fef3c7' }};
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border: 1pt solid {{ $isPassed ? '#bbf7d0' : '#fde68a' }};
            padding: 2mm 5mm;
            border-radius: 3mm;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 5mm;
        }

        /* Info Text */
        .info-text {
            font-size: 9pt;
            color: #64748b;
            line-height: 1.6;
            max-width: 140mm;
            margin: 0 auto 8mm;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 5mm;
        }

        .footer-left {
            width: 50%;
            text-align: left;
            vertical-align: bottom;
            padding-left: 10mm;
        }

        .footer-right {
            width: 50%;
            text-align: right;
            vertical-align: bottom;
            padding-right: 10mm;
        }

        .location-date {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 12mm;
        }

        .signer-name {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 1mm;
        }

        .signer-title {
            font-size: 9pt;
            font-weight: bold;
            color: #c5a059;
        }

        .qr-box {
            width: 22mm;
            height: 22mm;
            border: 1pt solid #e2e8f0;
            border-radius: 3mm;
            padding: 1mm;
            display: inline-block;
            margin-bottom: 2mm;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
        }

        .verify-url {
            font-size: 6pt;
            color: #64748b;
            word-break: break-all;
        }

        .verify-note {
            font-size: 6pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Decorative Elements -->
        <div class="border-outer"></div>
        <div class="corner-tl"></div>
        <div class="corner-br"></div>

        <div class="content">
            <!-- Header -->
            <div class="header">
                @if($institutionLogo)
                <div class="logo-box">
                    <img src="{{ $institutionLogo }}" alt="Logo">
                </div>
                @endif
                <div class="inst-name">{{ $institutionName }}</div>
                <div class="inst-sub">Perpustakaan &amp; Unida Library</div>
            </div>

            <!-- Title -->
            <div class="title-block">
                <div class="cert-title">Sertifikat</div>
                <div class="cert-subtitle">Originalitas Dokumen Akademik</div>
                <span class="cert-id">No: {{ $check->certificate_number }}</span>
            </div>

            <!-- Recipient -->
            <div class="recipient-block">
                <div class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</div>
                <div class="recipient-name">{{ strtoupper($member->name) }}</div>
                <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>
            </div>

            <!-- Result Card -->
            <div class="result-card">
                <table class="result-table">
                    <tr>
                        <td class="gauge-cell">
                            <div class="gauge-circle">
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
                                Diperiksa pada: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Status -->
            <div class="status-badge">
                @if($isPassed)
                ✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
                @else
                ⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI
                @endif
            </div>

            <!-- Info -->
            <div class="info-text">
                Dokumen ini telah melalui proses pemindaian menggunakan teknologi iThenticate by Turnitin
                dengan standar internasional. Tingkat kemiripan {{ $isPassed ? 'berada di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
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
                        <div class="qr-box">
                            <img src="{{ $qrCode }}" alt="QR">
                        </div><br>
                        <div class="verify-url">{{ $verifyUrl }}</div>
                        <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
