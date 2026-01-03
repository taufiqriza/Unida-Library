<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 11pt;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 12mm;
            position: relative;
        }

        .border-outer {
            border: 3pt solid #5b21b6;
            height: 100%;
            padding: 3mm;
        }

        .border-inner {
            border: 1pt solid #a78bfa;
            height: 100%;
            position: relative;
        }

        /* Header */
        .header {
            background-color: #5b21b6;
            color: #ffffff;
            text-align: center;
            padding: 18pt 20pt;
        }

        .logo {
            height: 55pt;
            margin-bottom: 10pt;
        }

        .inst-name {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2pt;
            margin-bottom: 4pt;
        }

        .inst-sub {
            font-size: 9pt;
            letter-spacing: 1pt;
        }

        /* Content */
        .content {
            padding: 25pt 35pt;
            text-align: center;
        }

        /* Title */
        .cert-title {
            font-size: 28pt;
            font-weight: bold;
            color: #5b21b6;
            letter-spacing: 6pt;
            margin-bottom: 6pt;
        }

        .cert-subtitle {
            font-size: 10pt;
            color: #666;
            letter-spacing: 2pt;
            margin-bottom: 18pt;
            padding-bottom: 15pt;
            border-bottom: 1pt solid #e5e5e5;
        }

        /* Certificate Number */
        .cert-number-box {
            margin-bottom: 20pt;
        }

        .cert-number {
            display: inline-block;
            background-color: #5b21b6;
            color: #ffffff;
            padding: 6pt 25pt;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 2pt;
        }

        /* Recipient */
        .recipient-label {
            font-size: 10pt;
            color: #888;
            margin-bottom: 8pt;
        }

        .recipient-name {
            font-size: 22pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 6pt;
        }

        .recipient-id {
            font-size: 10pt;
            color: #666;
            margin-bottom: 25pt;
        }

        /* Document Box */
        .doc-box {
            background-color: #f8f5ff;
            border: 1pt solid #e9e3ff;
            padding: 15pt 20pt;
            margin: 0 20pt 25pt 20pt;
            text-align: center;
        }

        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            line-height: 1.6;
            margin-bottom: 8pt;
        }

        .doc-meta {
            font-size: 8pt;
            color: #888;
        }

        /* Score Section */
        .score-section {
            margin: 25pt 0;
        }

        .score-box {
            display: inline-block;
            border: 3pt solid {{ $isPassed ? '#059669' : '#d97706' }};
            padding: 15pt 45pt;
            text-align: center;
        }

        .score-value {
            font-size: 42pt;
            font-weight: bold;
            color: {{ $isPassed ? '#059669' : '#d97706' }};
            line-height: 1;
        }

        .score-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2pt;
            margin-top: 6pt;
        }

        .status-box {
            margin-top: 15pt;
        }

        .status-text {
            display: inline-block;
            background-color: {{ $isPassed ? '#059669' : '#d97706' }};
            color: #ffffff;
            padding: 8pt 25pt;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 1pt;
        }

        /* Info Text */
        .info-text {
            font-size: 9pt;
            color: #666;
            line-height: 1.7;
            margin: 20pt 30pt;
            padding: 12pt 15pt;
            background-color: #fafafa;
            border-left: 3pt solid #5b21b6;
            text-align: left;
        }

        /* Footer Grid */
        .footer-section {
            margin-top: 25pt;
            padding-top: 20pt;
            border-top: 1pt solid #e5e5e5;
        }

        .footer-table {
            width: 100%;
        }

        .footer-col {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10pt;
        }

        .qr-img {
            width: 60pt;
            height: 60pt;
        }

        .qr-label {
            font-size: 7pt;
            color: #999;
            margin-top: 5pt;
        }

        .sig-date {
            font-size: 9pt;
            color: #666;
            margin-bottom: 8pt;
        }

        .sig-qr {
            width: 50pt;
            height: 50pt;
            margin-bottom: 8pt;
        }

        .sig-line {
            border-top: 1pt solid #333;
            width: 120pt;
            margin: 0 auto;
            padding-top: 5pt;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
        }

        .sig-title {
            font-size: 8pt;
            color: #5b21b6;
            margin-top: 2pt;
        }

        .provider-label {
            font-size: 7pt;
            color: #999;
            margin-bottom: 6pt;
        }

        .provider-badge {
            display: inline-block;
            background-color: #5b21b6;
            color: #ffffff;
            padding: 6pt 15pt;
            font-size: 9pt;
            font-weight: bold;
        }

        /* Bottom Bar */
        .bottom-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f5f5f5;
            padding: 10pt 20pt;
            text-align: center;
            font-size: 7pt;
            color: #888;
        }

        .verify-url {
            color: #5b21b6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="border-outer">
            <div class="border-inner">

                <!-- Header -->
                <div class="header">
                    @if($institutionLogo)
                    <img src="{{ $institutionLogo }}" class="logo" alt="Logo"><br>
                    @endif
                    <div class="inst-name">{{ strtoupper($institutionName) }}</div>
                    <div class="inst-sub">UNIVERSITAS DARUSSALAM GONTOR - PONOROGO - JAWA TIMUR</div>
                </div>

                <!-- Content -->
                <div class="content">

                    <!-- Title -->
                    <div class="cert-title">SERTIFIKAT</div>
                    <div class="cert-subtitle">HASIL PEMERIKSAAN ORIGINALITAS DOKUMEN</div>

                    <!-- Number -->
                    <div class="cert-number-box">
                        <span class="cert-number">{{ $check->certificate_number }}</span>
                    </div>

                    <!-- Recipient -->
                    <div class="recipient-label">Diberikan kepada:</div>
                    <div class="recipient-name">{{ strtoupper($member->name) }}</div>
                    <div class="recipient-id">NIM: {{ $member->member_id }}@if($member->memberType) - {{ $member->memberType->name }}@endif</div>

                    <!-- Document -->
                    <div class="doc-box">
                        <div class="doc-title">"{{ $check->document_title }}"</div>
                        <div class="doc-meta">
                            {{ $check->original_filename }} | 
                            {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                            @if($check->word_count) | {{ number_format($check->word_count) }} kata @endif
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="score-section">
                        <div class="score-box">
                            <div class="score-value">{{ number_format($check->similarity_score, 0) }}%</div>
                            <div class="score-label">SIMILARITY INDEX</div>
                        </div>
                        <div class="status-box">
                            <span class="status-text">
                                @if($isPassed)
                                LOLOS - MEMENUHI STANDAR ORIGINALITAS
                                @else
                                PERLU REVISI - MELEBIHI BATAS TOLERANSI
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="info-text">
                        Dokumen telah diperiksa menggunakan <strong>iThenticate by Turnitin</strong> - 
                        standar internasional untuk deteksi kesamaan akademik. 
                        Tingkat similarity {{ $isPassed ? 'di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
                    </div>

                    <!-- Footer -->
                    <div class="footer-section">
                        <table class="footer-table">
                            <tr>
                                <td class="footer-col">
                                    <img src="{{ $qrCode }}" class="qr-img" alt="QR"><br>
                                    <div class="qr-label">Scan untuk verifikasi</div>
                                </td>
                                <td class="footer-col">
                                    <div class="sig-date">Ponorogo, {{ $issuedDate }}</div>
                                    @if($signatureQr)
                                    <img src="{{ $signatureQr }}" class="sig-qr" alt="Sig"><br>
                                    @endif
                                    <div class="sig-line">
                                        <div class="sig-name">{{ $headLibrarian }}</div>
                                        <div class="sig-title">Kepala Perpustakaan</div>
                                    </div>
                                </td>
                                <td class="footer-col">
                                    <div class="provider-label">Powered by</div>
                                    <div class="provider-badge">iThenticate</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

                <!-- Bottom -->
                <div class="bottom-bar">
                    Verifikasi: <span class="verify-url">{{ $verifyUrl }}</span> | 
                    Sertifikat ini dihasilkan secara otomatis dan sah tanpa tanda tangan basah
                </div>

            </div>
        </div>
    </div>
</body>
</html>
