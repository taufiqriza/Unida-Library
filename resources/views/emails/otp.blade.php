<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 500px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #2563eb; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .content { padding: 30px 0; }
        .otp-box { background: #f3f4f6; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .otp-code { font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #2563eb; }
        .footer { text-align: center; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📚 UNIDA Library</div>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $name }}</strong>,</p>
            <p>Gunakan kode berikut untuk verifikasi email Anda:</p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <p>Kode ini berlaku selama <strong>15 menit</strong>.</p>
            <p>Jika Anda tidak melakukan pendaftaran, abaikan email ini.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Perpustakaan UNIDA Gontor</p>
        </div>
    </div>
</body>
</html>
