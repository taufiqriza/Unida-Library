@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <p style="margin: 0 0 20px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                Halo <strong style="color: #1e293b;">{{ $name }}</strong>,
            </p>
            <p style="margin: 0 0 24px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                Anda menerima permintaan reset password untuk akun UNIDA Library Anda. Gunakan kode berikut:
            </p>
        </td>
    </tr>
    
    {{-- Reset Code Box --}}
    <tr>
        <td align="center" style="padding: 8px 0 24px 0;">
            <table role="presentation" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 12px; border: 1px solid #bfdbfe;">
                <tr>
                    <td style="padding: 24px 40px;">
                        <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                            Kode Reset Password
                        </p>
                        <p style="margin: 0; color: #1e40af; font-size: 36px; font-weight: 700; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                            {{ $code }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 8px; border: 1px solid #fef08a;">
                <tr>
                    <td style="padding: 12px 16px;">
                        <p style="margin: 0; color: #854d0e; font-size: 13px;">
                            ⏱️ Kode ini berlaku selama <strong>30 menit</strong>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-top: 24px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                Jika Anda tidak meminta reset password, abaikan email ini. Password Anda akan tetap aman.
            </p>
        </td>
    </tr>
</table>
@endsection
