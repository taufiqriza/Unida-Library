<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Cek Plagiasi - {{ $check->certificate_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
            size: A4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1e3a8a;
            background: #ffffff;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .certificate {
            width: 100%;
            max-width: 540px;
            margin: 0 auto;
            padding: 15px;
            border: 3px solid #2563eb;
            border-radius: 15px;
            position: relative;
            background: #ffffff;
        }
        
        .inner-border {
            border: 1px solid #93c5fd;
            border-radius: 15px;
            overflow: hidden;
        }
        
        /* Header with Blue Background */
        .header {
            text-align: center;
            padding: 15px 25px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }
        
        .logo-row {
            margin-bottom: 8px;
            display: block;
        }
        
        .logo {
            height: 45px;
            vertical-align: middle;
            margin: 0 8px;
        }
        
        .institution {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin: 6px 0 2px;
            letter-spacing: 1px;
        }
        
        .sub-institution {
            font-size: 10px;
            color: #3b82f6;
            font-weight: 500;
        }
        
        /* Content Body */
        .content-body {
            padding: 15px 25px 20px;
        }
        
        /* Title */
        .title {
            text-align: center;
            margin: 0 0 12px;
        }
        
        .title h1 {
            font-size: 22px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 5px;
            letter-spacing: 3px;
        }
        
        .title-sub {
            font-size: 11px;
            color: #3b82f6;
            font-weight: 500;
        }
        
        .cert-number {
            text-align: center;
            margin: 10px 0 15px;
        }
        
        .cert-badge {
            display: inline-block;
            padding: 5px 20px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            font-size: 9px;
            color: #2563eb;
            font-weight: bold;
        }
        
        /* Recipient */
        .recipient {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .recipient .label {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .recipient .name {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3px;
        }
        
        .recipient .nim {
            font-size: 11px;
            color: #475569;
        }
        
        /* Document Box */
        .document-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .doc-meta {
            font-size: 9px;
            color: #64748b;
        }
        
        /* Score Section */
        .score-section {
            text-align: center;
            margin: 15px 0;
        }
        
        .score-container {
            display: inline-block;
        }
        
        .score-box {
            display: inline-block;
            padding: 12px 30px;
            border: 3px solid {{ $isPassed ? '#16a34a' : '#d97706' }};
            border-radius: 12px;
            background: {{ $isPassed ? '#f0fdf4' : '#fffbeb' }};
        }
        
        .score-value {
            font-size: 32px;
            font-weight: bold;
            color: {{ $isPassed ? '#15803d' : '#b45309' }};
            line-height: 1;
        }
        
        .score-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 4px;
        }
        
        .status-badge {
            margin-top: 10px;
        }
        
        .status-text {
            display: inline-block;
            font-size: 11px;
            font-weight: bold;
            color: {{ $isPassed ? '#15803d' : '#b45309' }};
            padding: 5px 15px;
            background: {{ $isPassed ? '#dcfce7' : '#fef3c7' }};
            border-radius: 15px;
        }
        
        /* iThenticate Info Box */
        .ithenticate-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 15px;
            margin: 15px 0;
            text-align: center;
        }
        
        .ithenticate-title {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .ithenticate-text {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }
        
        .ithenticate-text strong {
            color: #2563eb;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
        }
        
        .footer-row {
            width: 100%;
        }
        
        .footer-left {
            float: left;
            width: 28%;
            text-align: center;
        }
        
        .footer-center {
            float: left;
            width: 44%;
            text-align: center;
        }
        
        .footer-right {
            float: left;
            width: 28%;
            text-align: center;
        }
        
        .qr-code {
            width: 55px;
            height: 55px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px;
            background: white;
        }
        
        .qr-label {
            font-size: 7px;
            color: #94a3b8;
            margin-top: 3px;
        }
        
        .signature-section {
            margin-top: 3px;
        }
        
        .signature-date {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .signature-qr {
            width: 45px;
            height: 45px;
            border-radius: 6px;
            margin-bottom: 4px;
        }
        
        .signature-name {
            font-size: 9px;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 1px solid #1e3a8a;
            padding-top: 4px;
            display: inline-block;
        }
        
        .signature-title {
            font-size: 7px;
            color: #3b82f6;
            margin-top: 1px;
        }
        
        .provider-section {
            margin-top: 8px;
        }
        
        .provider-logo {
            height: 22px;
            margin-bottom: 4px;
        }
        
        .provider-box {
            display: inline-block;
            padding: 5px 10px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 6px;
            font-size: 7px;
            color: white;
            font-weight: bold;
        }
        
        .provider-label {
            font-size: 7px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        
        /* Verify Footer */
        .verify-footer {
            clear: both;
            text-align: center;
            margin: 0 25px;
            padding-top: 10px;
            border-top: 1px dashed #bfdbfe;
            font-size: 7px;
            color: #94a3b8;
        }
        
        .verify-url {
            color: #2563eb;
            font-weight: bold;
        }
        
        /* Automated Check Notice */
        .auto-notice {
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            margin: 6px 25px 12px;
            font-style: italic;
        }
        
        /* Clear float */
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border">
            
            <!-- Header with Logos -->
            <div class="header">
                <div class="logo-row">
                    @if($institutionLogo)
                    <img src="{{ $institutionLogo }}" class="logo" alt="Logo Perpustakaan">
                    @endif
                    @if(isset($logoIthenticate) && $logoIthenticate)
                    <img src="{{ $logoIthenticate }}" class="logo" alt="iThenticate">
                    @endif
                </div>
                <div class="institution">{{ $institutionName }}</div>
                <div class="sub-institution">Universitas Darussalam Gontor • Ponorogo, Jawa Timur</div>
            </div>
            
            <!-- Content Body -->
            <div class="content-body">
            
            <!-- Title -->
            <div class="title">
                <h1>SERTIFIKAT</h1>
                <div class="title-sub">Hasil Pemeriksaan Originalitas Dokumen</div>
            </div>
            
            <div class="cert-number">
                <span class="cert-badge">{{ $check->certificate_number }}</span>
            </div>
            
            <!-- Recipient -->
            <div class="recipient">
                <div class="label">Diberikan kepada:</div>
                <div class="name">{{ strtoupper($member->name) }}</div>
                <div class="nim">NIM: {{ $member->member_id }}@if($member->memberType) • {{ $member->memberType->name }}@endif</div>
            </div>
            
            <!-- Document Info -->
            <div class="document-box">
                <div class="doc-title">"{{ $check->document_title }}"</div>
                <div class="doc-meta">
                    {{ $check->original_filename }} • {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                    @if($check->word_count) • {{ number_format($check->word_count) }} kata @endif
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
                            ✓ LOLOS - Memenuhi Standar Originalitas
                            @else
                            ⚠ PERLU REVISI - Melebihi Batas Toleransi
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- iThenticate Info -->
            <div class="ithenticate-box">
                <div class="ithenticate-title">INFORMASI PEMERIKSAAN</div>
                <div class="ithenticate-text">
                    Dokumen ini telah melalui proses pemeriksaan plagiarisme <strong>secara otomatis</strong> menggunakan 
                    sistem <strong>iThenticate by Turnitin</strong> — standar internasional untuk deteksi kesamaan akademik.
                    {{ $isPassed ? 'Tingkat similarity berada di bawah' : 'Tingkat similarity melebihi' }} 
                    batas toleransi institusi (<strong>≤{{ $passThreshold }}%</strong>).
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer clearfix">
                <div class="footer-row">
                    <!-- QR Verification -->
                    <div class="footer-left">
                        <img src="{{ $qrCode }}" class="qr-code" alt="QR Verify">
                        <div class="qr-label">Scan untuk verifikasi</div>
                    </div>
                    
                    <!-- Signature with QR -->
                    <div class="footer-center">
                        <div class="signature-section">
                            <div class="signature-date">Ponorogo, {{ $issuedDate }}</div>
                            @if(isset($signatureQr) && $signatureQr)
                            <img src="{{ $signatureQr }}" class="signature-qr" alt="Digital Signature">
                            @endif
                            @if($headLibrarian)
                            <div class="signature-name">{{ $headLibrarian }}</div>
                            <div class="signature-title">Kepala Perpustakaan</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Provider -->
                    <div class="footer-right">
                        <div class="provider-section">
                            <div class="provider-label">Powered by</div>
                            @if(isset($logoIthenticate) && $logoIthenticate)
                            <img src="{{ $logoIthenticate }}" class="provider-logo" alt="iThenticate">
                            @else
                            <div class="provider-box">iThenticate</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            </div> <!-- close content-body -->
            
            <!-- Verify Footer -->
            <div class="verify-footer">
                Verifikasi keaslian: <span class="verify-url">{{ $verifyUrl }}</span>
            </div>
            
            <div class="auto-notice">
                Sertifikat ini dihasilkan secara otomatis oleh sistem dan sah tanpa tanda tangan basah.
            </div>
            
        </div>
    </div>
</body>
</html>
