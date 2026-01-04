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
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        /* Background Pattern */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.03;
            background-image: 
                radial-gradient(circle at 25% 25%, var(--accent-gold) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, var(--primary-dark) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
        }

        /* Border Dekoratif */
        .decorative-border {
            position: absolute;
            top: 30px;
            left: 30px;
            right: 30px;
            bottom: 30px;
            border: 3px solid transparent;
            border-radius: 25px;
            background: linear-gradient(135deg, var(--accent-gold), var(--primary-dark)) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: subtract;
            mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            mask-composite: subtract;
            z-index: 2;
        }

        .corner-accent {
            position: absolute;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-gold), #d4af37);
            z-index: 3;
            border-radius: 8px;
        }

        .top-left {
            top: 30px;
            left: 30px;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .bottom-right {
            bottom: 30px;
            right: 30px;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }

        /* Content Area */
        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 50px 50px 40px;
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        /* Header */
        .header {
            margin-bottom: 25px;
        }

        .uni-logo {
            width: 70px;
            height: 70px;
            background: white;
            margin: 0 auto 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 3px solid var(--accent-gold);
        }

        .uni-logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .institution-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sub-institution {
            font-size: 14px;
            font-weight: 600;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Title */
        .title-section {
            margin-bottom: 25px;
        }

        .cert-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 12px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cert-subtitle {
            font-size: 16px;
            color: var(--text-muted);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .cert-id {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-dark), #1e293b);
            color: white;
            padding: 6px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.3);
        }

        /* Recipient */
        .recipient-section {
            margin-bottom: 25px;
        }

        .intro-text {
            font-style: italic;
            color: var(--text-muted);
            font-size: 16px;
            margin-bottom: 12px;
        }

        .recipient-name {
            font-size: 34px;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 12px 0;
            position: relative;
            display: inline-block;
            padding: 0 40px;
        }

        .recipient-name::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
        }

        .recipient-meta {
            font-size: 18px;
            color: var(--primary-dark);
            font-weight: 500;
            margin-top: 15px;
        }

        /* Result Card */
        .result-section {
            margin-bottom: 20px;
        }

        .result-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            padding: 30px;
            margin: 0 auto;
            width: 88%;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .similarity-gauge {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border: 8px solid {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .percentage {
            font-size: 42px;
            font-weight: 800;
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            line-height: 1;
        }

        .gauge-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .document-info {
            flex: 1;
            text-align: right;
        }

        .arabic-title {
            font-family: 'Amiri', serif;
            font-size: 18px;
            color: var(--primary-dark);
            margin-bottom: 15px;
            line-height: 1.3;
            direction: rtl;
            text-align: right;
            font-weight: 700;
        }

        .doc-file-info {
            font-size: 12px;
            color: var(--text-muted);
            text-align: right;
            line-height: 1.4;
        }

        /* Status */
        .status-section {
            margin-bottom: 20px;
        }

        .status-badge {
            background: {{ $isPassed ? 'linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05))' : 'linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.05))' }};
            color: {{ $isPassed ? 'var(--success)' : '#f59e0b' }};
            padding: 10px 25px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            display: inline-block;
            border: 2px solid {{ $isPassed ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)' }};
            margin-bottom: 15px;
            box-shadow: 0 4px 15px {{ $isPassed ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }};
        }

        .info-text {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 550px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 30px;
        }

        .signature-section {
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .location-date {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .qr-kepala {
            width: 70px;
            height: 70px;
            background: white;
            border: 2px solid var(--accent-gold);
            border-radius: 10px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(197, 160, 89, 0.3);
            margin-bottom: 15px;
        }

        .qr-kepala img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .signer-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary-dark);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .signer-title {
            font-size: 12px;
            color: var(--accent-gold);
            font-weight: 600;
        }

        .verification-section {
            text-align: right;
        }

        .qr-container {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            justify-content: flex-end;
        }

        .qr-box {
            width: 80px;
            height: 80px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .verify-url {
            font-size: 9px;
            color: var(--text-muted);
            word-break: break-all;
            text-align: right;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .verify-note {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: right;
        }

        /* Print Styles - Ensure colors match preview */
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
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .corner-accent {
                background: #c5a059 !important;
            }
            
            .sub-institution {
                color: #c5a059 !important;
            }
            
            .signer-title {
                color: #c5a059 !important;
            }
            
            .qr-kepala {
                border-color: #c5a059 !important;
            }
        }

        /* Canvas rendering styles - for html2canvas */
        .certificate-container * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
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
        <!-- Background Pattern -->
        <div class="bg-pattern"></div>
        
        <!-- Border Dekoratif -->
        <div class="decorative-border"></div>
        <div class="corner-accent top-left"></div>
        <div class="corner-accent bottom-right"></div>

        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="uni-logo">
                    <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'color: var(--primary-dark); font-size: 24px; font-weight: 800;\'>U</div>';">
                </div>
                <div class="institution-name">Universitas Darussalam Gontor</div>
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
                    @if(isset($qrHeadLibrarian))
                    <div class="qr-kepala">
                        <img src="{{ $qrHeadLibrarian }}" alt="QR Kepala">
                    </div>
                    @endif
                    <div class="signer-name">{{ $headLibrarian }}</div>
                    <div class="signer-title">Kepala Perpustakaan</div>
                </div>

                <div class="verification-section">
                    <div class="qr-box">
                        <img src="{{ $qrCode }}" alt="QR Verification">
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
                backgroundColor: '#ffffff',
                logging: false,
                imageTimeout: 0,
                removeContainer: false,
                foreignObjectRendering: true,
                ignoreElements: function(element) {
                    return element.classList.contains('print-actions');
                }
            }).then(canvas => {
                // Show actions again
                actions.style.display = 'flex';
                
                // Create download link
                const link = document.createElement('a');
                link.download = 'Sertifikat-Plagiasi-{{ $check->certificate_number }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
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
