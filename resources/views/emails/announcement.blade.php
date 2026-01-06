@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong>{{ $recipientName }}</strong>,
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 8px; border: 1px solid #fcd34d;">
                <tr>
                    <td style="padding: 20px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">📢 Pengumuman</p>
                        <p style="margin: 0 0 12px; font-size: 16px; color: #1e293b; font-weight: 600;">{{ $title }}</p>
                        <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">{{ $content }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Untuk informasi lebih lanjut, silakan hubungi perpustakaan atau kunjungi website kami.
            </p>
        </td>
    </tr>
    
    <tr>
        <td>
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Wassalamu'alaikum,<br>
                <span style="color: #1e40af; font-weight: 500;">Tim Perpustakaan UNIDA</span>
            </p>
        </td>
    </tr>
</table>
@endsection
