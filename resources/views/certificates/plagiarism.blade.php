<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $check->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #1a1a1a;
            color: #333;
            width: 595px;
            height: 842px;
            position: relative;
        }
        .certificate {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
        }
        
        /* Gold Border Effect */
        .gold-border {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 3px solid #c9a227;
            background: linear-gradient(180deg, #f5f5f5 0%, #ffffff 50%, #f5f5f5 100%);
        }
        
        /* Corner Decorations */
        .corner-tl, .corner-tr, .corner-bl, .corner-br {
            position: absolute;
            width: 80px;
            height: 80px;
            background: repeating-linear-gradient(
                45deg,
                #c9a227,
                #c9a227 2px,
                transparent 2px,
                transparent 8px
            );
        }
        .corner-tl { top: 15px; left: 15px; }
        .corner-tr { top: 15px; right: 15px; transform: rotate(90deg); }
        .corner-bl { bottom: 15px; left: 15px; transform: rotate(-90deg); }
        .corner-br { bottom: 15px; right: 15px; transform: rotate(180deg); }
        
        /* Content Container */
        .content {
            position: relative;
            padding: 35px 45px;
            height: 100%;
        }
        
        /* Logos Header */
        .logos-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .logo-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
            border: 2px solid #c9a227;
        }
        .logo-circle img {
            max-width: 40px;
            max-height: 40px;
            object-fit: contain;
        }
        .logo-placeholder {
            font-size: 8px;
            color: #999;
            text-align: center;
        }
        
        /* Certificate Title */
        .title-certificate {
            text-align: center;
            margin-bottom: 8px;
        }
        .title-text {
            font-size: 42px;
            font-weight: bold;
            color: #1a1a1a;
            text-shadow: 2px 2px 0 #c9a227, 3px 3px 0 #9a7b1a;
            letter-spacing: 4px;
            font-style: italic;
        }
        
        /* Gold Banner */
        .gold-banner {
            background: linear-gradient(90deg, #c9a227 0%, #f0d55c 50%, #c9a227 100%);
            padding: 8px 25px;
            margin: 0 auto 20px;
            text-align: center;
            position: relative;
            max-width: 350px;
        }
        .gold-banner::before, .gold-banner::after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
        }
        .gold-banner::before {
            left: -15px;
            border-right: 15px solid #c9a227;
            border-top: 18px solid transparent;
            border-bottom: 18px solid transparent;
        }
        .gold-banner::after {
            right: -15px;
            border-left: 15px solid #c9a227;
            border-top: 18px solid transparent;
            border-bottom: 18px solid transparent;
        }
        .gold-banner-text {
            color: #1a1a1a;
            font-size: 14px;
            font-weight: bold;
            font-style: italic;
        }
        
        /* Presented To */
        .presented-to {
            text-align: center;
            margin-bottom: 10px;
        }
        .presented-text {
            font-size: 16px;
            font-style: italic;
            color: #333;
        }
        
        /* Recipient Name */
        .recipient-name {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 3px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        /* NIM */
        .nim-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .nim-label {
            font-size: 14px;
            color: #666;
        }
        .nim-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        /* Description */
        .description {
            text-align: center;
            font-size: 11px;
            line-height: 1.6;
            color: #444;
            margin: 0 20px 15px;
            padding: 10px 15px;
            background: rgba(201, 162, 39, 0.08);
            border-left: 3px solid #c9a227;
            border-right: 3px solid #c9a227;
        }
        
        /* Similarity Score */
        .score-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .score-box {
            display: inline-block;
            padding: 10px 30px;
            border: 3px solid {{ $isPassed ? '#059669' : '#d97706' }};
            border-radius: 10px;
            background: {{ $isPassed ? 'rgba(5, 150, 105, 0.1)' : 'rgba(217, 119, 6, 0.1)' }};
        }
        .score-value {
            font-size: 36px;
            font-weight: bold;
            color: {{ $isPassed ? '#059669' : '#d97706' }};
        }
        .score-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .score-status {
            font-size: 14px;
            font-weight: bold;
            color: {{ $isPassed ? '#059669' : '#d97706' }};
            margin-top: 5px;
        }
        
        /* Location Date */
        .location-date {
            text-align: center;
            font-size: 12px;
            color: #333;
            font-style: italic;
            margin-bottom: 10px;
        }
        
        /* QR and Signature Section */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 10px;
        }
        
        .qr-signature {
            text-align: center;
        }
        .qr-code {
            width: 70px;
            height: 70px;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .signature-title {
            font-size: 9px;
            color: #c9a227;
            font-weight: bold;
        }
        
        /* Download QR */
        .download-qr-section {
            text-align: center;
        }
        .download-qr {
            width: 80px;
            height: 80px;
            border: 2px solid #0ea5e9;
            padding: 3px;
            background: #fff;
        }
        .download-label {
            font-size: 7px;
            color: #0ea5e9;
            margin-top: 3px;
        }
        
        /* Provider Logos */
        .provider-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }
        .provider-logo {
            height: 20px;
            object-fit: contain;
        }
        .provider-text {
            font-size: 12px;
            color: #0d9488;
            font-weight: bold;
        }
        
        /* Accreditation Badge */
        .accreditation-badge {
            position: absolute;
            bottom: 40px;
            right: 50px;
            width: 70px;
            height: 70px;
        }
        .accreditation-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* Certificate Number */
        .certificate-number {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            color: #999;
        }
        
        /* Verification Text */
        .verify-text {
            font-size: 8px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Gold Border -->
        <div class="gold-border">
            <!-- Corner Decorations -->
            <div class="corner-tl"></div>
            <div class="corner-tr"></div>
            <div class="corner-bl"></div>
            <div class="corner-br"></div>
            
            <div class="content">
                <!-- Logos Header -->
                <div class="logos-header">
                    <div class="logo-circle">
                        @if(isset($logoGontor) && $logoGontor)
                        <img src="{{ $logoGontor }}" alt="Gontor">
                        @else
                        <span class="logo-placeholder">GONTOR</span>
                        @endif
                    </div>
                    <div class="logo-circle">
                        @if(isset($logoUnida) && $logoUnida)
                        <img src="{{ $logoUnida }}" alt="UNIDA">
                        @else
                        <span class="logo-placeholder">UNIDA</span>
                        @endif
                    </div>
                    <div class="logo-circle">
                        @if(isset($logoAkreditasi) && $logoAkreditasi)
                        <img src="{{ $logoAkreditasi }}" alt="Akreditasi">
                        @else
                        <span class="logo-placeholder">AKRED</span>
                        @endif
                    </div>
                    <div class="logo-circle">
                        @if($institutionLogo)
                        <img src="{{ $institutionLogo }}" alt="Library">
                        @else
                        <span class="logo-placeholder">LIBRARY</span>
                        @endif
                    </div>
                </div>
                
                <!-- Certificate Title -->
                <div class="title-certificate">
                    <span class="title-text">CERTIFICATE</span>
                </div>
                
                <!-- Gold Banner -->
                <div class="gold-banner">
                    <span class="gold-banner-text">— Thesis Plagiarism Screening —</span>
                </div>
                
                <!-- Presented To -->
                <div class="presented-to">
                    <span class="presented-text">This certificate is presented to:</span>
                </div>
                
                <!-- Recipient Name -->
                <div class="recipient-name">{{ strtoupper($member->name) }}</div>
                
                <!-- NIM -->
                <div class="nim-section">
                    <span class="nim-label">NIM : </span>
                    <span class="nim-value">{{ $member->member_id }}</span>
                </div>
                
                <!-- Description -->
                <div class="description">
                    The thesis of the respective student has undergone a comprehensive plagiarism assessment using
                    {{ $check->provider_label }} and has been officially certified as 
                    <strong>{{ $isPassed ? 'PLAGIARISM-FREE' : 'REQUIRING REVIEW' }}</strong> by the
                    {{ $institutionName }} of University of Darussalam Gontor, 
                    {{ $isPassed ? 'meeting' : 'not meeting' }} the prescribed similarity tolerance
                    threshold of ≤ {{ $passThreshold }}%, ensuring compliance with academic integrity and ethical research standards.
                </div>
                
                <!-- Similarity Score -->
                <div class="score-section">
                    <div class="score-box">
                        <div class="score-value">{{ number_format($check->similarity_score, 0) }}%</div>
                        <div class="score-label">Similarity Index</div>
                        <div class="score-status">
                            @if($isPassed)
                            ✓ PASSED
                            @else
                            ⚠ NEEDS REVIEW
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Location Date -->
                <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
                
                <!-- Footer Section: QR + Signature + Download -->
                <div class="footer-section">
                    <!-- Left: Verification QR -->
                    <div class="qr-signature">
                        <img src="{{ $qrCode }}" class="qr-code" alt="QR Verify">
                        <p class="verify-text">Scan to verify</p>
                    </div>
                    
                    <!-- Center: Signature -->
                    <div class="qr-signature">
                        @if(isset($signatureQr) && $signatureQr)
                        <img src="{{ $signatureQr }}" class="qr-code" alt="Signature QR">
                        @endif
                        @if($headLibrarian)
                        <p class="signature-name">{{ $headLibrarian }}</p>
                        <p class="signature-title">Head of Library</p>
                        @endif
                    </div>
                    
                    <!-- Right: Download QR -->
                    <div class="download-qr-section">
                        @if(isset($downloadQr) && $downloadQr)
                        <img src="{{ $downloadQr }}" class="download-qr" alt="Download QR">
                        <p class="download-label">Download the Plagiarism Report File</p>
                        @endif
                    </div>
                </div>
                
                <!-- Provider Logos -->
                <div class="provider-logos">
                    @if(isset($logoIthenticate) && $logoIthenticate)
                    <img src="{{ $logoIthenticate }}" class="provider-logo" alt="iThenticate">
                    @else
                    <span class="provider-text">✓ iThenticate</span>
                    @endif
                    
                    @if(isset($logoTurnitin) && $logoTurnitin)
                    <img src="{{ $logoTurnitin }}" class="provider-logo" alt="Turnitin">
                    @else
                    <span class="provider-text">Powered by turnitin</span>
                    @endif
                </div>
                
                <!-- Accreditation Badge -->
                @if(isset($badgeAccreditation) && $badgeAccreditation)
                <div class="accreditation-badge">
                    <img src="{{ $badgeAccreditation }}" alt="Accreditation Badge">
                </div>
                @endif
                
                <!-- Certificate Number -->
                <div class="certificate-number">{{ $check->certificate_number }}</div>
            </div>
        </div>
    </div>
</body>
</html>
