<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat {{ $check->certificate_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-gold: #c5a059;
            --success: #10b981;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Certificate Container - Fixed A4 Dimensions */
        .certificate-container {
            width: 794px;  /* A4 width in pixels at 96 DPI */
            height: 1123px; /* A4 height in pixels at 96 DPI */
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-radius: 20px;
        }

        /* Print Actions */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-download {
            background: #667eea;
            color: white;
        }

        .btn-download:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        /* Border Dekoratif */
        .decorative-border {
            position: absolute;
            top: 40px;
            left: 40px;
            right: 40px;
            bottom: 40px;
            border: 2px solid #e2e8f0;
            border-radius: 30px;
            z-index: 1;
        }

        .corner-accent {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 4px solid var(--accent-gold);
            z-index: 2;
        }

        .top-left {
            top: 40px;
            left: 40px;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 30px;
        }

        .bottom-right {
            bottom: 40px;
            right: 40px;
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 30px;
        }

        /* Content Area */
        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 80px 60px 60px;
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        /* Header */
        .header {
            margin-bottom: 50px;
        }

        .uni-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-dark);
            margin: 0 auto 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 800;
        }

        .institution-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }

        .sub-institution {
            font-size: 16px;
            font-weight: 600;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Title */
        .title-section {
            margin-bottom: 50px;
        }

        .cert-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 56px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 15px;
        }

        .cert-subtitle {
            font-size: 18px;
            color: var(--text-muted);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .cert-id {
            display: inline-block;
            background: var(--primary-dark);
            color: white;
            padding: 8px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Recipient */
        .recipient-section {
            margin-bottom: 50px;
        }

        .intro-text {
            font-style: italic;
            color: var(--text-muted);
            font-size: 18px;
            margin-bottom: 15px;
        }

        .recipient-name {
            font-size: 40px;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 15px 0;
            position: relative;
            display: inline-block;
            padding: 0 50px;
        }

        .recipient-name::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
        }

        .recipient-meta {
            font-size: 20px;
            color: var(--primary-dark);
            font-weight: 500;
            margin-top: 20px;
        }

        /* Result Card */
        .result-section {
            margin-bottom: 40px;
        }

        .result-card {
            background: white;
            border: 2px solid #f1f5f9;
            border-radius: 30px;
            padding: 40px;
            margin: 0 auto;
            width: 90%;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .similarity-gauge {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: #f8fafc;
            border: 10px solid {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .percentage {
            font-size: 48px;
            font-weight: 800;
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .document-info {
            flex: 1;
            text-align: right;
        }

        .arabic-title {
            font-family: 'Amiri', serif;
            font-size: 32px;
            color: var(--primary-dark);
            margin-bottom: 20px;
            line-height: 1.6;
            direction: rtl;
            text-align: right;
            font-weight: 700;
        }

        .doc-file-info {
            font-size: 14px;
            color: var(--text-muted);
            text-align: right;
            line-height: 1.5;
        }

        /* Status */
        .status-section {
            margin-bottom: 30px;
        }

        .status-badge {
            background: {{ $isPassed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            padding: 12px 30px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 700;
            display: inline-block;
            border: 2px solid {{ $isPassed ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }};
            margin-bottom: 20px;
        }

        .info-text {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 40px;
        }

        .signature-section {
            text-align: left;
        }

        .location-date {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 60px;
        }

        .signer-name {
            font-weight: 700;
            font-size: 20px;
            color: var(--primary-dark);
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .signer-title {
            font-size: 14px;
            color: var(--accent-gold);
            font-weight: 600;
        }

        .verification-section {
            text-align: right;
        }

        .qr-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            justify-content: flex-end;
        }

        .qr-box {
            width: 100px;
            height: 100px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-head {
            border-color: var(--accent-gold);
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .verify-url {
            font-size: 10px;
            color: var(--text-muted);
            word-break: break-all;
            text-align: right;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .verify-note {
            font-size: 10px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: right;
        }

        /* Print Styles */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            
            .print-actions {
                display: none;
            }
            
            .certificate-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions -->
    <div class="print-actions">
        <button class="btn btn-download" onclick="downloadAsPNG()">
            <i class="fas fa-download"></i>
            Download PNG
        </button>
    </div>

    <div class="certificate-container" id="certificate">
        <!-- Border Dekoratif -->
        <div class="decorative-border"></div>
        <div class="corner-accent top-left"></div>
        <div class="corner-accent bottom-right"></div>

        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="uni-logo">U</div>
                <div class="institution-name">{{ $institutionName }}</div>
                <div class="sub-institution">Perpustakaan &amp; Unida Library</div>
            </div>

            <!-- Title -->
            <div class="title-section">
                <h1 class="cert-title">Sertifikat</h1>
                <div class="cert-subtitle">Originalitas Dokumen Akademik</div>
                <div class="cert-id">No: {{ $check->certificate_number }}</div>
            </div>

            <!-- Recipient -->
            <div class="recipient-section">
                <div class="intro-text">Sertifikat ini diberikan dengan hormat kepada:</div>
                <div class="recipient-name">{{ strtoupper($member->name) }}</div>
                <div class="recipient-meta">NIM: {{ $member->member_id }} @if($member->memberType)— {{ $member->memberType->name }}@endif</div>
            </div>

            <!-- Result -->
            <div class="result-section">
                <div class="result-card">
                    <div class="similarity-gauge">
                        <div class="percentage">{{ number_format($check->similarity_score, 0) }}%</div>
                        <div class="gauge-label">Similarity</div>
                    </div>
                    <div class="document-info">
                        <div class="arabic-title">{{ $check->document_title }}</div>
                        <div class="doc-file-info">
                            File: {{ $check->original_filename }}<br>
                            Diperiksa pada: {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="status-section">
                <div class="status-badge">
                    @if($isPassed)✓ LOLOS MEMENUHI STANDAR ORIGINALITAS
                    @else⚠ PERLU REVISI - MELEBIHI BATAS TOLERANSI @endif
                </div>
                <div class="info-text">
                    Dokumen ini telah melalui proses pemindaian menggunakan teknologi iThenticate by Turnitin
                    dengan standar internasional. Tingkat kemiripan {{ $isPassed ? 'berada di bawah' : 'melebihi' }} batas toleransi institusi ({{ $passThreshold }}%).
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="signature-section">
                    <div class="location-date">Ponorogo, {{ $issuedDate }}</div>
                    <div class="signer-name">{{ $headLibrarian }}</div>
                    <div class="signer-title">Kepala Perpustakaan</div>
                </div>

                <div class="verification-section">
                    <div class="qr-container">
                        @if(isset($qrHeadLibrarian))
                        <div class="qr-box qr-head">
                            <img src="{{ $qrHeadLibrarian }}" alt="QR Kepala">
                        </div>
                        @endif
                        <div class="qr-box">
                            <img src="{{ $qrCode }}" alt="QR Verification">
                        </div>
                    </div>
                    <div class="verify-url">{{ $verifyUrl }}</div>
                    <div class="verify-note">Verifikasi Digital Sah Tanpa Tanda Tangan Basah</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadAsPNG() {
            const certificate = document.getElementById('certificate');
            const actions = document.querySelector('.print-actions');
            
            // Hide actions during capture
            actions.style.display = 'none';
            
            html2canvas(certificate, {
                width: 794,
                height: 1123,
                scale: 2, // High quality
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                // Show actions again
                actions.style.display = 'flex';
                
                // Create download link
                const link = document.createElement('a');
                link.download = 'Sertifikat-Plagiasi-{{ $check->certificate_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(error => {
                actions.style.display = 'flex';
                console.error('Error generating PNG:', error);
                alert('Error generating PNG. Please try again.');
            });
        }

        // Auto-download on load (optional)
        // window.addEventListener('load', () => {
        //     setTimeout(downloadAsPNG, 1000);
        // });
    </script>
</body>
</html>
