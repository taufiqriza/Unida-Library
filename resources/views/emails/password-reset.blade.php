@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong>{{ $name }}</strong>,
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Anda menerima permintaan reset password untuk akun UNIDA Library. Gunakan kode berikut:
            </p>
        </td>
    </tr>
    
    {{-- Reset Code Box --}}
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 20px 32px;">
                        <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Kode Reset Password</p>
                        <p style="margin: 0; color: #1e40af; font-size: 32px; font-weight: 700; letter-spacing: 6px; font-family: monospace;">{{ $code }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 6px;">
                <tr>
                    <td style="padding: 10px 14px;">
                        <p style="margin: 0; color: #854d0e; font-size: 12px;">⏱️ Kode berlaku selama <strong>30 menit</strong></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0; color: #64748b; font-size: 12px; line-height: 1.5;">
                Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-top: 20px;">
            <p style="margin: 0; color: #64748b; font-size: 13px;">
                Wassalamu'alaikum,<br>
                <span style="color: #1e40af; font-weight: 500;">Tim Perpustakaan UNIDA</span>
            </p>
        </td>
    </tr>
</table>
@endsection
