<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <!-- Font Google: Inter (UI), Montserrat (Heading), Amiri (Arabic Elegant) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-gold: #c5a059;
            --bg-canvas: #f1f5f9;
            --success: #10b981;
            --text-muted: #64748b;
            /* Ukuran A4 standar */
            --a4-width: 210mm;
            --a4-height: 297mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-canvas);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container Sertifikat (A4) */
        .certificate-container {
            width: var(--a4-width);
            height: var(--a4-height);
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 15mm;
            display: flex;
            flex-direction: column;
        }

        /* Border Dekoratif Modern */
        .decorative-border {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            z-index: 1;
            pointer-events: none;
        }

        .corner-accent {
            position: absolute;
            width: 100px;
            height: 100px;
            border: 4px solid var(--accent-gold);
            z-index: 2;
        }

        .top-left {
            top: 10mm;
            left: 10mm;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 30px;
        }

        .bottom-right {
            bottom: 10mm;
            right: 10mm;
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 30px;
        }

        /* Background Shapes */
        .shape {
            position: absolute;
            background: linear-gradient(135deg, rgba(197, 160, 89, 0.05) 0%, rgba(15, 23, 42, 0.05) 100%);
            border-radius: 50%;
            z-index: 0;
        }

        /* Content Wrapper */
        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        /* Header Section */
        .header {
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .uni-logo-placeholder {
            width: 70px;
            height: 70px;
            background: var(--primary-dark);
            margin: 0 auto 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
        }

        .institution-name {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 18px;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        .sub-institution {
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Main Title Area */
        .title-block {
            margin-bottom: 30px;
        }

        .cert-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 42px;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
        }

        .cert-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 10px;
            display: block;
        }

        .cert-id {
            display: inline-block;
            background: var(--primary-dark);
            color: white;
            padding: 4px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 15px;
        }

        /* Recipient Section */
        .recipient-block {
            margin-bottom: 35px;
        }

        .intro-text {
            font-style: italic;
            color: var(--text-muted);
            font-size: 14px;
        }

        .recipient-name {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 10px 0;
            padding: 0 40px;
            position: relative;
            display: inline-block;
        }

        .recipient-name::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
        }

        .recipient-meta {
            font-size: 16px;
            color: var(--primary-dark);
            margin-top: 15px;
            font-weight: 500;
        }

        /* Result Card (The "Powerful" part) */
        .result-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 25px;
            margin: 0 auto 30px;
            width: 90%;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .similarity-gauge {
            width: 130px;
            height: 130px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #f8fafc;
            border: 8px solid {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .percentage {
            font-size: 38px;
            font-weight: 800;
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .document-info {
            flex: 1;
            text-align: right;
            padding-right: 5px;
        }

        /* Gaya Khusus Teks Arab */
        .arabic-title {
            font-family: 'Amiri', serif;
            font-size: 24px;
            color: var(--primary-dark);
            margin-bottom: 12px;
            line-height: 1.5;
            direction: rtl;
            /* Memastikan urutan kanan ke kiri benar */
            text-align: right;
        }

        .doc-file-info {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            color: var(--text-muted);
            text-align: right;
        }

        /* Verification Status */
        .status-badge {
            background: {{ $isPassed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 25px;
            border: 1px solid {{ $isPassed ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }};
        }

        .info-text {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 520px;
            margin: 0 auto;
        }

        /* Footer / Signatures */
        .footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 40px 10px;
        }

        .signature-box {
            text-align: left;
        }

        .location-date {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 50px;
        }

        .signer-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary-dark);
            margin-bottom: 2px;
        }

        .signer-title {
            font-size: 12px;
            color: var(--accent-gold);
            font-weight: 600;
        }

        .verification-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .qr-placeholder {
            width: 85px;
            height: 85px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px;
            margin-bottom: 8px;
        }

        .qr-placeholder img {
            width: 100%;
            height: 100%;
        }

        .verify-url {
            font-size: 8px;
            /* Diperkecil dari 9px */
            color: var(--text-muted);
            max-width: 250px;
            /* Diperlebar agar tidak wrap ke bawah */
            word-break: break-all;
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

        <div class="shape" style="width: 500px; height: 500px; top: -250px; left: -250px;"></div>
        <div class="shape"
            style="width: 400px; height: 400px; bottom: -200px; right: -200px; transform: rotate(45deg);"></div>

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
                    <!-- Teks Arab yang sudah diperbaiki -->
                    <div class="arabic-title">
                        {{ $check->document_title }}
                    </div>
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
                    <div class="qr-placeholder">
                        <img src="{{ $qrCode }}" alt="QR Code Verification">
                    </div>
                    <div class="verify-url">{{ $verifyUrl }}</div>
                    <div
                        style="font-size: 8px; color: #94a3b8; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px;">
                        Verifikasi Digital Sah Tanpa Tanda Tangan Basah
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
