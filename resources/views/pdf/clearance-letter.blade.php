<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Bebas Pustaka - {{ $letter->letter_number }}</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; color: #1f2937; font-size: 11px; line-height: 1.5; }
        .header { width: 100%; margin-bottom: 8px; border-bottom: 3px solid #1e40af; padding-bottom: 8px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 70px; vertical-align: middle; }
        .header-logo img { width: 60px; height: 60px; }
        .header-text { text-align: center; vertical-align: middle; }
        .header-receipt { width: 80px; text-align: right; vertical-align: top; }
        .receipt-badge { background-color: #dbeafe; color: #1e40af; padding: 4px 10px; font-size: 9px; font-weight: bold; border: 1px solid #93c5fd; display: inline-block; }
        .institution-name { font-size: 18px; font-weight: bold; color: #1e40af; margin: 0; text-transform: uppercase; }
        .sub-institution { font-size: 13px; font-weight: bold; color: #1f2937; margin: 2px 0 0 0; }
        .institution-address { font-size: 9px; color: #4b5563; margin-top: 3px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; background-color: #d1fae5; color: #065f46; margin-left: 8px; }
        .main-title { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0 3px 0; color: #111827; text-transform: uppercase; text-decoration: underline; }
        .letter-number { text-align: center; font-size: 11px; color: #4b5563; margin-bottom: 15px; }
        .content-text { margin-bottom: 10px; text-align: justify; }
        .info-card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; margin: 12px 0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 6px; font-size: 11px; color: #374151; vertical-align: top; }
        .info-label { width: 110px; font-weight: 600; }
        .info-sep { width: 12px; }
        .requirements-box { background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px 12px; margin: 12px 0; }
        .requirement-item { margin: 6px 0; padding-left: 4px; font-size: 11px; }
        .req-num { font-weight: bold; margin-right: 6px; color: #059669; }
        .thesis-box { background-color: #eff6ff; border-left: 3px solid #3b82f6; padding: 8px 12px; margin: 12px 0; }
        .thesis-title { font-weight: bold; font-style: italic; color: #1f2937; font-size: 11px; line-height: 1.4; }
        .thesis-meta { font-size: 9px; color: #6b7280; margin-top: 5px; }
        .thesis-badge { display: inline-block; background-color: #dbeafe; color: #1e40af; padding: 1px 6px; font-size: 8px; font-weight: bold; margin-right: 6px; }
        .signature-area { margin-top: 25px; width: 100%; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .signature-box { width: 46%; text-align: center; float: left; }
        .signature-box.right { float: right; }
        .signature-label { font-size: 10px; margin-bottom: 3px; color: #374151; }
        .qr-code { margin-bottom: 3px; }
        .qr-code img { width: 55px; height: 55px; }
        .signature-line { border-top: 1px solid #9ca3af; padding-top: 3px; width: 85%; margin: 0 auto; }
        .signature-name { font-weight: bold; font-size: 11px; color: #111827; }
        .signature-role { font-size: 9px; color: #6b7280; }
        .footer-note { margin-top: 30px; padding-top: 8px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @else
                        <div style="width: 60px; height: 60px; background-color: #1e40af; border-radius: 50%; color: white; text-align: center; line-height: 60px; font-weight: bold; font-size: 22px;">P</div>
                    @endif
                </td>
                <td class="header-text">
                    <h1 class="institution-name">Perpustakaan</h1>
                    <p class="sub-institution">Universitas Darussalam Gontor</p>
                    <p class="institution-address">Jl. Raya Siman KM. 6 Ponorogo 63471 Jawa Timur<br>Email: library@unida.gontor.ac.id | WA: +62 821-1704-9501</p>
                </td>
                <td class="header-receipt">
                    <span class="receipt-badge">Receipt 3</span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="main-title">Surat Keterangan Bebas Pustaka<span class="status-badge">Disetujui</span></h2>
    <p class="letter-number">Nomor: {{ $letter->letter_number }}</p>

    <div class="content-text">
        <p>Yang bertanda tangan di bawah ini, Perpustakaan UNIDA Gontor menerangkan bahwa:</p>
    </div>

    <div class="info-card">
        <table class="info-table">
            <tr>
                <td class="info-label">Nama</td>
                <td class="info-sep">:</td>
                <td><strong>{{ $letter->member->name }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">NIM</td>
                <td class="info-sep">:</td>
                <td><strong>{{ $letter->member->member_id }}</strong></td>
            </tr>
            @if($letter->thesisSubmission)
            <tr>
                <td class="info-label">Program Studi</td>
                <td class="info-sep">:</td>
                <td>{{ $letter->thesisSubmission->department?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Fakultas</td>
                <td class="info-sep">:</td>
                <td>{{ $letter->thesisSubmission->department?->faculty?->name ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="content-text">
        <p>Adalah benar mahasiswa tersebut di atas telah memenuhi persyaratan administrasi perpustakaan:</p>
    </div>

    <div class="requirements-box">
        <div class="requirement-item"><span class="req-num">1.</span><strong>Bebas Peminjaman Buku</strong> — Tidak memiliki tanggungan peminjaman.</div>
        <div class="requirement-item"><span class="req-num">2.</span><strong>Telah Mengunggah Tugas Akhir</strong> — Karya ilmiah telah diunggah ke repositori.</div>
    </div>

    @if($letter->thesisSubmission)
    <div class="content-text"><p>Karya ilmiah yang telah diunggah:</p></div>
    <div class="thesis-box">
        <div class="thesis-title">"{{ $letter->thesisSubmission->title }}"</div>
        <div class="thesis-meta">
            <span class="thesis-badge">{{ ucfirst($letter->thesisSubmission->type) }}</span>
            Tahun {{ $letter->thesisSubmission->year }}
        </div>
    </div>
    @endif

    <div class="content-text">
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div style="text-align: right; margin-bottom: 10px; font-size: 11px;">
        Ponorogo, {{ $letter->approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
    </div>

    <div class="signature-area clearfix">
        <div class="signature-box">
            <p class="signature-label">Mahasiswa Pengaju,</p>
            <div class="qr-code"><img src="{{ $memberSignatureQr }}" alt="Member QR"></div>
            <div class="signature-line">
                <p class="signature-name">{{ $letter->member->name }}</p>
                <p class="signature-role">NIM. {{ $letter->member->member_id }}</p>
            </div>
        </div>
        <div class="signature-box right">
            <p class="signature-label">Admin Perpustakaan,</p>
            <div class="qr-code"><img src="{{ $approverSignatureQr }}" alt="Approver QR"></div>
            <div class="signature-line">
                <p class="signature-name">{{ $letter->approver?->name ?? 'Pustakawan' }}</p>
                @if($letter->approver?->employee_id)
                <p class="signature-role">NIP. {{ $letter->approver->employee_id }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-note">
        <p>Surat ini dicetak secara otomatis dan dapat diverifikasi melalui QR Code di atas.</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
