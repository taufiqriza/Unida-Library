<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat - {{ $check->certificate_number }}</title>
    <style>
        @page { margin: 10mm; size: A4 portrait; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9pt; 
            color: #1a1a2e; 
            line-height: 1.4;
            background: #fff;
        }

        .certificate {
            width: 190mm;
            margin: 0 auto;
            border: 2pt solid #667eea;
            border-radius: 8pt;
            overflow: hidden;
            background: #fff;
        }

        /* Header - Gradient Purple */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15pt 20pt;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .logo { height: 50pt; margin-bottom: 8pt; }

        .institution { 
            font-size: 14pt; 
            font-weight: bold; 
            letter-spacing: 1pt;
            margin-bottom: 2pt;
        }

        .sub-institution { 
            font-size: 8pt; 
            opacity: 0.9;
            letter-spacing: 0.5pt;
        }

        /* Body Content */
        .body {
            padding: 20pt 25pt;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin-bottom: 15pt;
            padding-bottom: 12pt;
            border-bottom: 1pt solid #e8e8e8;
        }

        .title-main {
            font-size: 22pt;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 4pt;
            margin-bottom: 4pt;
        }

        .title-sub {
            font-size: 9pt;
            color: #666;
            letter-spacing: 1pt;
        }

        .cert-number {
            display: inline-block;
            margin-top: 10pt;
            padding: 5pt 18pt;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 15pt;
            letter-spacing: 1pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Recipient Section */
        .recipient-section {
            text-align: center;
            margin: 18pt 0;
        }

        .recipient-label {
            font-size: 9pt;
            color: #888;
            margin-bottom: 6pt;
        }

        .recipient-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 4pt;
        }

        .recipient-id {
            font-size: 9pt;
            color: #666;
        }

        /* Document Box */
        .document-box {
            background: linear-gradient(180deg, #f8f9ff 0%, #fff 100%);
            border: 1pt solid #e0e4f5;
            border-radius: 8pt;
            padding: 12pt 15pt;
            margin: 15pt 0;
            text-align: center;
        }

        .doc-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1a1a2e;
            line-height: 1.5;
            margin-bottom: 6pt;
        }

        .doc-title-rtl {
            direction: rtl;
            unicode-bidi: bidi-override;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .doc-meta {
            font-size: 7pt;
            color: #888;
        }

        /* Score Section */
        .score-section {
            text-align: center;
            margin: 20pt 0;
        }

        .score-container {
            display: inline-block;
        }

        .score-box {
            display: inline-block;
            padding: 12pt 35pt;
            border: 3pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            border-radius: 12pt;
            background: {{ $isPassed ? '#ecfdf5' : '#fffbeb' }};
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .score-value {
            font-size: 36pt;
            font-weight: bold;
            color: {{ $isPassed ? '#059669' : '#d97706' }};
            line-height: 1;
        }

        .score-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin-top: 4pt;
        }

        .status-badge {
            margin-top: 12pt;
        }

        .status-text {
            display: inline-block;
            padding: 6pt 20pt;
            background: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            color: white;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 20pt;
            letter-spacing: 0.5pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Info Box */
        .info-box {
            background: #f8f9fa;
            border-left: 3pt solid #667eea;
            padding: 10pt 12pt;
            margin: 15pt 0;
            font-size: 8pt;
            color: #555;
            line-height: 1.6;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .info-box strong { color: #667eea; }

        /* Footer Section */
        .footer-section {
            margin-top: 20pt;
            padding-top: 15pt;
            border-top: 1pt solid #e8e8e8;
        }

        .footer-grid {
            width: 100%;
        }

        .footer-grid:after {
            content: "";
            display: table;
            clear: both;
        }

        .footer-col {
            float: left;
            text-align: center;
        }

        .footer-left { width: 28%; }
        .footer-center { width: 44%; }
        .footer-right { width: 28%; }

        .qr-box {
            width: 55pt;
            height: 55pt;
            border: 1pt solid #e0e0e0;
            border-radius: 6pt;
            padding: 3pt;
            background: white;
            margin: 0 auto;
        }

        .qr-box img { width: 100%; height: 100%; }

        .qr-label {
            font-size: 6pt;
            color: #999;
            margin-top: 4pt;
        }

        .signature-section {
            padding-top: 5pt;
        }

        .sig-location {
            font-size: 8pt;
            color: #666;
            margin-bottom: 6pt;
        }

        .sig-qr {
            width: 45pt;
            height: 45pt;
            margin: 0 auto 6pt;
        }

        .sig-name {
            font-size: 9pt;
            font-weight: bold;
            color: #1a1a2e;
            border-top: 1pt solid #1a1a2e;
            padding-top: 4pt;
            display: inline-block;
            min-width: 100pt;
        }

        .sig-title {
            font-size: 7pt;
            color: #667eea;
            margin-top: 2pt;
        }

        .provider-section {
            padding-top: 10pt;
        }

        .provider-label {
            font-size: 6pt;
            color: #999;
            margin-bottom: 4pt;
        }

        .provider-badge {
            display: inline-block;
            padding: 5pt 12pt;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 6pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Verify Footer */
        .verify-footer {
            text-align: center;
            padding: 10pt 20pt;
            background: #f8f9fa;
            font-size: 7pt;
            color: #888;
            border-top: 1pt solid #e8e8e8;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .verify-url {
            color: #667eea;
            font-weight: bold;
        }

        .auto-note {
            margin-top: 4pt;
            font-size: 6pt;
            font-style: italic;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Header -->
        <div class="header">
            @if($institutionLogo)
            <img src="{{ $institutionLogo }}" class="logo" alt="Logo">
            @endif
            <div class="institution">{{ strtoupper($institutionName) }}</div>
            <div class="sub-institution">Universitas Darussalam Gontor • Ponorogo, Jawa Timur, Indonesia</div>
        </div>

        <!-- Body -->
        <div class="body">
            <!-- Title -->
            <div class="title-section">
                <div class="title-main">SERTIFIKAT</div>
                <div class="title-sub">HASIL PEMERIKSAAN ORIGINALITAS DOKUMEN</div>
                <div class="cert-number">{{ $check->certificate_number }}</div>
            </div>

            <!-- Recipient -->
            <div class="recipient-section">
                <div class="recipient-label">Diberikan kepada:</div>
                <div class="recipient-name">{{ strtoupper($member->name) }}</div>
                <div class="recipient-id">
                    NIM: {{ $member->member_id }}
                    @if($member->memberType) &bull; {{ $member->memberType->name }} @endif
                </div>
            </div>

            <!-- Document -->
            <div class="document-box">
                <div class="doc-title @if($hasArabicTitle ?? false) doc-title-rtl @endif">
                    &ldquo;{{ $check->document_title }}&rdquo;
                </div>
                <div class="doc-meta">
                    {{ $check->original_filename }}
                    &bull; {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                    @if($check->word_count) &bull; {{ number_format($check->word_count) }} kata @endif
                </div>
            </div>

            <!-- Score -->
            <div class="score-section">
                <div class="score-container">
                    <div class="score-box">
                        <div class="score-value">{{ number_format($check->similarity_score, 0) }}%</div>
                        <div class="score-label">Similarity Index</div>
                    </div>
                    <div class="status-badge">
                        <span class="status-text">
                            @if($isPassed)
                                &#10003; LOLOS &mdash; Memenuhi Standar Originalitas
                            @else
                                &#9888; PERLU REVISI &mdash; Melebihi Batas Toleransi
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="info-box">
                Dokumen ini telah melalui pemeriksaan plagiarisme menggunakan <strong>iThenticate by Turnitin</strong> &mdash; 
                standar internasional untuk deteksi kesamaan akademik. 
                Tingkat similarity {{ $isPassed ? 'berada di bawah' : 'melebihi' }} batas toleransi institusi 
                (<strong>&le;{{ $passThreshold }}%</strong>).
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <div class="footer-grid">
                    <!-- QR Verification -->
                    <div class="footer-col footer-left">
                        <div class="qr-box">
                            <img src="{{ $qrCode }}" alt="QR">
                        </div>
                        <div class="qr-label">Scan untuk verifikasi</div>
                    </div>

                    <!-- Signature -->
                    <div class="footer-col footer-center">
                        <div class="signature-section">
                            <div class="sig-location">Ponorogo, {{ $issuedDate }}</div>
                            @if($signatureQr)
                            <div class="sig-qr">
                                <img src="{{ $signatureQr }}" alt="Signature" style="width:100%;height:100%">
                            </div>
                            @endif
                            <div class="sig-name">{{ $headLibrarian }}</div>
                            <div class="sig-title">Kepala Perpustakaan</div>
                        </div>
                    </div>

                    <!-- Provider -->
                    <div class="footer-col footer-right">
                        <div class="provider-section">
                            <div class="provider-label">Powered by</div>
                            <div class="provider-badge">iThenticate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verify Footer -->
        <div class="verify-footer">
            Verifikasi keaslian sertifikat: <span class="verify-url">{{ $verifyUrl }}</span>
            <div class="auto-note">Sertifikat ini dihasilkan secara otomatis oleh sistem dan sah tanpa tanda tangan basah.</div>
        </div>
    </div>
</body>
</html>
