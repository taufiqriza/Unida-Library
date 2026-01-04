<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <style>
        @page { 
            size: A4 portrait; 
            margin: 18mm; 
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #0f172a;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Container utama */
        .certificate-container {
            width: 100%;
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 15mm;
            background: white;
        }

        /* Border dekoratif */
        .decorative-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 1px solid #c5a059;
            border-radius: 15px;
            pointer-events: none;
        }

        .corner-accent {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #c5a059;
        }

        .top-left {
            top: 8mm;
            left: 8mm;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 15px;
        }

        .bottom-right {
            bottom: 8mm;
            right: 8mm;
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 15px;
        }

        /* Header */
        .header-container { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        
        .logo { 
            width: 50px; 
            height: 50px; 
            margin-bottom: 8px; 
            background: #0f172a;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .institution-name { 
            font-size: 16px; 
            font-weight: bold; 
            color: #0f172a; 
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .institution-sub { 
            font-size: 9px; 
            color: #c5a059;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Title */
        .main-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .cert-id {
            text-align: center;
            background: #0f172a;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        /* Info Card */
        .info-card {
            background-color: #f7fafc;
            border-radius: 12px;
            padding: 15px;
            margin: 0 auto 15px auto;
            width: 95%;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .intro-text {
            font-style: italic;
            color: #64748b;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .recipient-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 8px 0;
            padding-bottom: 3px;
            border-bottom: 2px solid #c5a059;
            display: inline-block;
        }

        .recipient-meta {
            font-size: 12px;
            color: #0f172a;
            font-weight: 500;
        }

        /* Result Card */
        .result-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 15px;
            padding: 15px;
            margin: 0 auto 15px;
            width: 95%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .result-table { 
            width: 100%; 
            border-collapse: collapse;
        }
        
        .result-table td { 
            vertical-align: middle; 
            padding: 0;
        }

        .gauge-cell { 
            width: 100px; 
            text-align: center; 
        }

        .similarity-gauge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f8fafc;
            border: 6px solid {{ $isPassed ? '#10b981' : '#f59e0b' }};
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .percentage {
            font-size: 24px;
            font-weight: bold;
            color: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 7px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-top: 2px;
        }

        .doc-cell { 
            text-align: right; 
            padding-left: 15px; 
        }

        .arabic-title {
            font-size: 16px;
            color: #0f172a;
            margin-bottom: 8px;
            line-height: 1.4;
            direction: rtl;
            text-align: right;
            font-weight: bold;
        }

        .doc-file-info {
            font-size: 8px;
            color: #64748b;
            text-align: right;
            line-height: 1.3;
        }

        /* Status */
        .status-card {
            background-color: {{ $isPassed ? '#f0fdf4' : '#fefce8' }};
            border: 1px solid {{ $isPassed ? '#bbf7d0' : '#fde047' }};
            border-radius: 10px;
            padding: 10px;
            margin: 0 auto 15px;
            width: 95%;
            text-align: center;
        }

        .status-badge {
            background: {{ $isPassed ? '#10b981' : '#f59e0b' }};
            color: white;
            padding: 6px 15px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 8px;
        }

        .info-text {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 9px;
        }

        .signature-area {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .location-date {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 30px;
        }

        .signer-name {
            font-weight: bold;
            font-size: 11px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .signer-title {
            font-size: 8px;
            color: #c5a059;
            font-weight: bold;
        }

        .qr-code {
            text-align: center;
            margin-bottom: 5px;
        }

        .qr-code img {
            width: 50px;
            height: 50px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px;
        }

        .verify-url {
            font-size: 6px;
            color: #64748b;
            word-break: break-all;
            line-height: 1.2;
        }

        .verify-note {
            font-size: 6px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        /* Print optimizations */
        @media print {
            body { background: none; }
            .certificate-container { box-shadow: none; }
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Border dekoratif -->
        <div class="decorative-border"></div>
        <div class="corner-accent top-left"></div>
        <div class="corner-accent bottom-right"></div>

        <!-- Header -->
        <div class="header-container">
            <div class="logo">U</div>
            <h2 class="institution-name">{{ $institutionName }}</h2>
            <div class="institution-sub">Perpustakaan &amp; Unida Library</div>
        </div>

        <!-- Title -->
        <h1 class="main-title">Sertifikat</h1>
        <div class="cert-subtitle">Originalitas Dokumen Akademik</div>
        <div class="cert-id">No: {{ $check->certificate_number }}</div>

        <!-- Recipient Info -->
        <div class="info-card">
            <div class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</div>
            <div class="recipient-name">{{ strtoupper($member->name) }}</div>
            <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>
        </div>

        <!-- Result Card -->
        <div class="result-card">
            <table class="result-table">
                <tr>
                    <td class="gauge-cell">
                        <div class="similarity-gauge">
                            <div class="percentage">{{ number_format($check->similarity_score, 0) }}%</div>
                            <div class="gauge-label">Similarity</div>
                        </div>
                    </td>
                    <td class="doc-cell">
                        <div class="arabic-title">{{ $check->document_title }}</div>
                        <div class="doc-file-info">
                            File: {{ $check->original_filename }}<br>
                            Diperiksa: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Status -->
        <div class="status-card">
            <div class="status-badge">
                @if($isPassed)✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
                @else⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI @endif
            </div>
            <div class="info-text">
                Dokumen telah diperiksa menggunakan iThenticate by Turnitin dengan standar internasional. 
                Tingkat kemiripan {{ $isPassed ? 'berada di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="footer">
            <table class="signature-area">
                <tr>
                    <td class="signature-box">
                        <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
                        @if(isset($qrHeadLibrarian))
                        <div class="qr-code">
                            <img src="{{ $qrHeadLibrarian }}" alt="QR Kepala">
                        </div>
                        @endif
                        <div class="signer-name">{{ $headLibrarian }}</div>
                        <div class="signer-title">Kepala Perpustakaan</div>
                    </td>
                    <td class="signature-box">
                        <div class="qr-code">
                            <img src="{{ $qrCode }}" alt="QR Verification">
                        </div>
                        <div class="verify-url">{{ $verifyUrl }}</div>
                        <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
