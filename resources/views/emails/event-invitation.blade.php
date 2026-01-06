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
                Kami mengundang Anda untuk menghadiri kegiatan berikut:
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; border: 1px solid #bfdbfe;">
                <tr>
                    <td style="padding: 20px;">
                        <p style="margin: 0 0 12px; font-size: 16px; color: #1e40af; font-weight: 600;">{{ $eventTitle }}</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 6px 0; color: #475569; font-size: 13px;">
                                    📅 <strong>Tanggal:</strong> {{ $eventDate }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: #475569; font-size: 13px;">
                                    🕐 <strong>Waktu:</strong> {{ $eventTime }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: #475569; font-size: 13px;">
                                    📍 <strong>Lokasi:</strong> {{ $eventLocation }}
                                </td>
                            </tr>
                        </table>
                        @if(isset($eventDescription))
                        <p style="margin: 12px 0 0; color: #64748b; font-size: 13px; line-height: 1.5; border-top: 1px solid #bfdbfe; padding-top: 12px;">
                            {{ $eventDescription }}
                        </p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if(isset($registerUrl))
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 6px;">
                        <a href="{{ $registerUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Daftar Sekarang →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    
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
