<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-gold: #c5a059;
            --bg-canvas: #f1f5f9;
            --success: #10b981;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-canvas);
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        /* Container Sertifikat (A4) */
        .certificate-container {
            width: 100%;
            background: white;
            position: relative;
            overflow: hidden;
            padding: 12mm;
            display: flex;
            flex-direction: column;
            min-height: calc(297mm - 30mm);
        }

        /* Border Dekoratif Modern */
        .decorative-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 1pt solid #e2e8f0;
            border-radius: 25pt;
            z-index: 1;
            pointer-events: none;
        }

        .corner-accent {
            position: absolute;
            width: 80pt;
            height: 80pt;
            border: 3pt solid var(--accent-gold);
            z-index: 2;
        }

        .top-left {
            top: 8mm;
            left: 8mm;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 25pt;
        }

        .bottom-right {
            bottom: 8mm;
            right: 8mm;
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 25pt;
        }

        /* Content Wrapper */
        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            text-align: center;
            padding: 15pt;
        }

        /* Header Section */
        .header {
            margin-top: 20pt;
            margin-bottom: 25pt;
        }

        .uni-logo-placeholder {
            width: 55pt;
            height: 55pt;
            background: var(--primary-dark);
            margin: 0 auto 10pt;
            border-radius: 12pt;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
        }

        .institution-name {
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 2pt;
            color: var(--primary-dark);
            margin-bottom: 3pt;
            font-weight: bold;
        }

        .sub-institution {
            font-size: 9pt;
            font-weight: 600;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 0.8pt;
        }

        /* Main Title Area */
        .title-block {
            margin-bottom: 20pt;
        }

        .cert-label {
            font-size: 32pt;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
        }

        .cert-subtitle {
            font-size: 11pt;
            color: var(--text-muted);
            letter-spacing: 3pt;
            text-transform: uppercase;
            margin-top: 8pt;
            display: block;
        }

        .cert-id {
            display: inline-block;
            background: var(--primary-dark);
            color: white;
            padding: 3pt 12pt;
            border-radius: 50pt;
            font-size: 8pt;
            font-weight: 600;
            margin-top: 10pt;
        }

        /* Recipient Section */
        .recipient-block {
            margin-bottom: 20pt;
        }

        .intro-text {
            font-style: italic;
            color: var(--text-muted);
            font-size: 10pt;
        }

        .recipient-name {
            font-size: 24pt;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 8pt 0;
            padding: 0 30pt;
            position: relative;
            display: inline-block;
        }

        .recipient-name::after {
            content: "";
            position: absolute;
            bottom: -3pt;
            left: 0;
            right: 0;
            height: 1.5pt;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
        }

        .recipient-meta {
            font-size: 12pt;
            color: var(--primary-dark);
            margin-top: 10pt;
            font-weight: 500;
        }

        /* Result Card */
        .result-card {
            background: #ffffff;
            border: 1pt solid #f1f5f9;
            border-radius: 18pt;
            padding: 15pt;
            margin: 0 auto 20pt;
            width: 90%;
            box-shadow: 0 8pt 20pt -4pt rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15pt;
        }

        .similarity-gauge {
            width: 100pt;
            height: 100pt;
            flex-shrink: 0;
            border-radius: 50pt;
            background: #f8fafc;
            border: 6pt solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2pt 4pt rgba(0, 0, 0, 0.05);
        }

        .percentage {
            font-size: 28pt;
            font-weight: 800;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 7pt;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 3pt;
        }

        .document-info {
            flex: 1;
            text-align: right;
            padding-right: 4pt;
        }

        /* Gaya Khusus Teks Arab */
        .arabic-title {
            font-size: 18pt;
            color: var(--primary-dark);
            margin-bottom: 8pt;
            line-height: 1.4;
            direction: rtl;
            text-align: right;
            font-weight: bold;
        }

        .doc-file-info {
            font-size: 8pt;
            color: var(--text-muted);
            text-align: right;
            line-height: 1.3;
        }

        /* Verification Status */
        .status-badge {
            background: {{ $isPassed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            padding: 6pt 15pt;
            border-radius: 10pt;
            font-size: 10pt;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 15pt;
            border: 1pt solid {{ $isPassed ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }};
        }

        .info-text {
            font-size: 8pt;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 400pt;
            margin: 0 auto;
        }

        /* Footer / Signatures */
        .footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 25pt 8pt;
        }

        .signature-box {
            text-align: left;
            width: 45%;
        }

        .location-date {
            font-size: 10pt;
            color: var(--text-muted);
            margin-bottom: 35pt;
        }

        .signer-name {
            font-weight: 700;
            font-size: 11pt;
            color: var(--primary-dark);
            margin-bottom: 2pt;
            line-height: 1.1;
        }

        .signer-title {
            font-size: 9pt;
            color: var(--accent-gold);
            font-weight: 600;
        }

        .verification-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            width: 45%;
        }

        .qr-container {
            display: flex;
            gap: 8pt;
            margin-bottom: 6pt;
        }

        .qr-placeholder {
            width: 60pt;
            height: 60pt;
            background: #fff;
            border: 1pt solid #e2e8f0;
            border-radius: 8pt;
            padding: 4pt;
        }

        .qr-head {
            border-color: var(--accent-gold);
        }

        .qr-placeholder img {
            width: 100%;
            height: 100%;
        }

        .verify-url {
            font-size: 6pt;
            color: var(--text-muted);
            max-width: 200pt;
            word-break: break-all;
            text-align: right;
            line-height: 1.2;
        }

        .verify-note {
            font-size: 6pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-top: 3pt;
            text-align: right;
        }

        /* Print Settings */
        @media print {
            body {
                background: none;
            }

            .certificate-container {
                box-shadow: none;
                margin: 0;
            }

            .decorative-border {
                border-color: #cbd5e1;
            }
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Elemen Latar Belakang -->
        <div class="decorative-border"></div>
        <div class="corner-accent top-left"></div>
        <div class="bottom-right corner-accent"></div>

        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="uni-logo-placeholder">U</div>
                <h2 class="institution-name">{{ $institutionName }}</h2>
                <div class="sub-institution">Perpustakaan &amp; Unida Library</div>
            </div>

            <!-- Judul -->
            <div class="title-block">
                <h1 class="cert-label">Sertifikat</h1>
                <span class="cert-subtitle">Originalitas Dokumen Akademik</span>
                <div class="cert-id">No: {{ $check->certificate_number }}</div>
            </div>

            <!-- Penerima -->
            <div class="recipient-block">
                <span class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</span><br>
                <div class="recipient-name">{{ strtoupper($member->name) }}</div><br>
                <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>
            </div>

            <!-- Kartu Hasil (Similarity Score) -->
            <div class="result-card">
                <div class="similarity-gauge">
                    <span class="percentage">{{ number_format($check->similarity_score, 0) }}%</span>
                    <span class="gauge-label">Similarity</span>
                </div>
                <div class="document-info">
                    @if($hasArabicTitle ?? false)
                    <div class="arabic-title">{{ $check->document_title }}</div>
                    @else
                    <div class="arabic-title">{{ $check->document_title }}</div>
                    @endif
                    <div class="doc-file-info">
                        File: {{ $check->original_filename }}<br>
                        Diperiksa pada: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="status-badge">
                @if($isPassed)✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
                @else⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI @endif
            </div>

            <p class="info-text">
                Dokumen ini telah melalui proses pemindaian menggunakan teknologi iThenticate by Turnitin
                dengan standar internasional. Tingkat kemiripan {{ $isPassed ? 'berada di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
            </p>

            <!-- Footer & Tanda Tangan -->
            <div class="footer">
                <div class="signature-box">
                    <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
                    <div class="signer-name">{{ $headLibrarian }}</div>
                    <div class="signer-title">Kepala Perpustakaan</div>
                </div>

                <div class="verification-box">
                    <div class="qr-container">
                        <div class="qr-placeholder qr-head">
                            <img src="{{ $qrHeadLibrarian }}" alt="QR Kepala">
                        </div>
                        <div class="qr-placeholder">
                            <img src="{{ $qrCode }}" alt="QR Verification">
                        </div>
                    </div>
                    <div class="verify-url">{{ $verifyUrl }}</div>
                    <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
