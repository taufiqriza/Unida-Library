<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Cek Plagiasi - {{ $check->certificate_number }}</title>
    <style>
        @page {
            margin: 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1e3a8a;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .certificate {
            width: 100%;
            max-width: 545px;
            margin: 0 auto;
            padding: 20px;
            border: 4px solid #2563eb;
            border-radius: 20px;
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
            padding: 20px 30px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }
        
        .logo-row {
            margin-bottom: 10px;
            display: block;
        }
        
        .logo {
            height: 50px;
            vertical-align: middle;
            margin: 0 10px;
        }
        
        .institution {
            font-size: 16px;
            font-weight: bold;
            color: white;
            margin: 8px 0 3px;
        }
        
        .sub-institution {
            font-size: 10px;
            color: #bfdbfe;
        }
        
        /* Content Body */
        .content-body {
            padding: 20px 30px 25px;
        }
        
        /* Title */
        .title {
            text-align: center;
            margin: 0 0 15px;
        }
        
        .title h1 {
            font-size: 26px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 8px;
            letter-spacing: 4px;
        }
        
        .title-sub {
            font-size: 12px;
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
            margin: 20px 0;
        }
        
        .score-container {
            display: inline-block;
        }
        
        .score-box {
            display: inline-block;
            padding: 15px 35px;
            border: 3px solid {{ $isPassed ? '#16a34a' : '#d97706' }};
            border-radius: 15px;
            background: {{ $isPassed ? '#f0fdf4' : '#fffbeb' }};
        }
        
        .score-value {
            font-size: 38px;
            font-weight: bold;
            color: {{ $isPassed ? '#15803d' : '#b45309' }};
            line-height: 1;
        }
        
        .score-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .status-badge {
            margin-top: 12px;
        }
        
        .status-text {
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            color: {{ $isPassed ? '#15803d' : '#b45309' }};
            padding: 6px 20px;
            background: {{ $isPassed ? '#dcfce7' : '#fef3c7' }};
            border-radius: 20px;
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
            margin-top: 20px;
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
            width: 65px;
            height: 65px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 3px;
            background: white;
        }
        
        .qr-label {
            font-size: 7px;
            color: #94a3b8;
            margin-top: 4px;
        }
        
        .signature-section {
            margin-top: 5px;
        }
        
        .signature-date {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 8px;
        }
        
        .signature-qr {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        
        .signature-name {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 1px solid #1e3a8a;
            padding-top: 5px;
            display: inline-block;
        }
        
        .signature-title {
            font-size: 8px;
            color: #3b82f6;
            margin-top: 2px;
        }
        
        .provider-section {
            margin-top: 10px;
        }
        
        .provider-logo {
            height: 25px;
            margin-bottom: 5px;
        }
        
        .provider-box {
            display: inline-block;
            padding: 6px 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 8px;
            font-size: 8px;
            color: white;
            font-weight: bold;
        }
        
        .provider-label {
            font-size: 7px;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        
        /* Verify Footer */
        .verify-footer {
            clear: both;
            text-align: center;
            margin: 0 30px;
            padding-top: 12px;
            border-top: 1px dashed #bfdbfe;
            font-size: 8px;
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
            margin: 8px 30px 15px;
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
