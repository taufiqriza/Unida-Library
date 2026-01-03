<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $check->certificate_number }}</title>
    <style>
        @page { margin: 12mm; size: A4; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e3a8a; line-height: 1.4; }
        .cert { max-width: 540px; margin: 0 auto; padding: 12px; border: 2px solid #2563eb; border-radius: 12px; }
        .inner { border: 1px solid #93c5fd; border-radius: 10px; overflow: hidden; }
        .header { text-align: center; padding: 12px 20px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
        .logo { height: 40px; margin-bottom: 6px; }
        .inst { font-size: 14px; font-weight: bold; letter-spacing: 1px; }
        .sub-inst { font-size: 9px; opacity: 0.9; }
        .body { padding: 15px 20px; }
        .title { text-align: center; margin-bottom: 10px; }
        .title h1 { font-size: 20px; color: #1e40af; letter-spacing: 2px; margin-bottom: 3px; }
        .title-sub { font-size: 10px; color: #3b82f6; }
        .cert-num { text-align: center; margin: 8px 0 12px; }
        .badge { display: inline-block; padding: 4px 16px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 15px; font-size: 8px; color: #2563eb; font-weight: bold; }
        .recipient { text-align: center; margin-bottom: 12px; }
        .recipient .label { font-size: 9px; color: #64748b; margin-bottom: 4px; }
        .recipient .name { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .recipient .nim { font-size: 10px; color: #475569; }
        .doc-box { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px; text-align: center; }
        .doc-title { font-size: 10px; font-weight: bold; color: #1e3a8a; line-height: 1.5; }
        .doc-meta { font-size: 8px; color: #64748b; margin-top: 4px; }
        .score-section { text-align: center; margin: 12px 0; }
        .score-box { display: inline-block; padding: 10px 25px; border: 2px solid {{ $isPassed ? '#16a34a' : '#d97706' }}; border-radius: 10px; background: {{ $isPassed ? '#f0fdf4' : '#fffbeb' }}; }
        .score-val { font-size: 28px; font-weight: bold; color: {{ $isPassed ? '#15803d' : '#b45309' }}; }
        .score-lbl { font-size: 7px; color: #64748b; text-transform: uppercase; }
        .status { margin-top: 8px; }
        .status-txt { display: inline-block; font-size: 10px; font-weight: bold; color: {{ $isPassed ? '#15803d' : '#b45309' }}; padding: 4px 12px; background: {{ $isPassed ? '#dcfce7' : '#fef3c7' }}; border-radius: 12px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; margin: 12px 0; text-align: center; font-size: 9px; color: #475569; line-height: 1.5; }
        .info-box strong { color: #2563eb; }
        .footer { margin-top: 12px; }
        .footer-row { width: 100%; }
        .footer-row:after { content: ""; display: table; clear: both; }
        .f-left { float: left; width: 30%; text-align: center; }
        .f-center { float: left; width: 40%; text-align: center; }
        .f-right { float: left; width: 30%; text-align: center; }
        .qr { width: 50px; height: 50px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; background: white; }
        .qr-lbl { font-size: 6px; color: #94a3b8; margin-top: 2px; }
        .sig-date { font-size: 8px; color: #64748b; margin-bottom: 4px; }
        .sig-qr { width: 40px; height: 40px; margin-bottom: 3px; }
        .sig-name { font-size: 8px; font-weight: bold; color: #1e3a8a; border-top: 1px solid #1e3a8a; padding-top: 3px; display: inline-block; }
        .sig-title { font-size: 6px; color: #3b82f6; }
        .prov-lbl { font-size: 6px; color: #94a3b8; margin-bottom: 2px; }
        .prov-box { display: inline-block; padding: 4px 8px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 4px; font-size: 7px; color: white; font-weight: bold; }
        .verify { text-align: center; margin: 0 20px; padding-top: 8px; border-top: 1px dashed #bfdbfe; font-size: 6px; color: #94a3b8; }
        .verify-url { color: #2563eb; font-weight: bold; }
        .auto-note { text-align: center; font-size: 6px; color: #94a3b8; margin: 5px 20px 10px; font-style: italic; }
        /* Arabic support */
        .rtl { direction: rtl; text-align: center; }
    </style>
</head>
<body>
    <div class="cert">
        <div class="inner">
            <div class="header">
                @if($institutionLogo)
                <img src="{{ $institutionLogo }}" class="logo" alt="Logo">
                @endif
                <div class="inst">{{ $institutionName }}</div>
                <div class="sub-inst">Universitas Darussalam Gontor • Ponorogo, Jawa Timur</div>
            </div>
            
            <div class="body">
                <div class="title">
                    <h1>SERTIFIKAT</h1>
                    <div class="title-sub">Hasil Pemeriksaan Originalitas Dokumen</div>
                </div>
                
                <div class="cert-num">
                    <span class="badge">{{ $check->certificate_number }}</span>
                </div>
                
                <div class="recipient">
                    <div class="label">Diberikan kepada:</div>
                    <div class="name">{{ strtoupper($member->name) }}</div>
                    <div class="nim">NIM: {{ $member->member_id }}@if($member->memberType) • {{ $member->memberType->name }}@endif</div>
                </div>
                
                <div class="doc-box">
                    <div class="doc-title @if($hasArabicTitle ?? false) rtl @endif">"{{ $check->document_title }}"</div>
                    <div class="doc-meta">
                        {{ $check->original_filename }} • {{ $check->completed_at ? $check->completed_at->translatedFormat('d F Y, H:i') : $issuedDate }} WIB
                        @if($check->word_count) • {{ number_format($check->word_count) }} kata @endif
                    </div>
                </div>
                
                <div class="score-section">
                    <div class="score-box">
                        <div class="score-val">{{ number_format($check->similarity_score, 0) }}%</div>
                        <div class="score-lbl">Similarity Index</div>
                    </div>
                    <div class="status">
                        <span class="status-txt">
                            @if($isPassed) ✓ LOLOS - Memenuhi Standar Originalitas
                            @else ⚠ PERLU REVISI - Melebihi Batas Toleransi @endif
                        </span>
                    </div>
                </div>
                
                <div class="info-box">
                    Dokumen telah diperiksa menggunakan <strong>iThenticate by Turnitin</strong>.
                    {{ $isPassed ? 'Similarity di bawah' : 'Similarity melebihi' }} batas toleransi (<strong>≤{{ $passThreshold }}%</strong>).
                </div>
                
                <div class="footer">
                    <div class="footer-row">
                        <div class="f-left">
                            <img src="{{ $qrCode }}" class="qr" alt="QR">
                            <div class="qr-lbl">Scan verifikasi</div>
                        </div>
                        <div class="f-center">
                            <div class="sig-date">Ponorogo, {{ $issuedDate }}</div>
                            @if($signatureQr)<img src="{{ $signatureQr }}" class="sig-qr" alt="Sig">@endif
                            <div class="sig-name">{{ $headLibrarian }}</div>
                            <div class="sig-title">Kepala Perpustakaan</div>
                        </div>
                        <div class="f-right">
                            <div class="prov-lbl">Powered by</div>
                            <div class="prov-box">iThenticate</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="verify">Verifikasi: <span class="verify-url">{{ $verifyUrl }}</span></div>
            <div class="auto-note">Sertifikat ini dihasilkan secara otomatis dan sah tanpa tanda tangan basah.</div>
        </div>
    </div>
</body>
</html>
