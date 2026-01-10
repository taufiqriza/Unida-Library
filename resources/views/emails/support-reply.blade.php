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
                Staff perpustakaan telah membalas pertanyaan Anda. Berikut adalah preview balasannya:
            </p>
        </td>
    </tr>
    
    {{-- Reply Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; border: 1px solid #bfdbfe;">
                <tr>
                    <td style="padding: 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <p style="margin: 0 0 4px; font-size: 10px; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">💬 BALASAN SUPPORT</p>
                                    @if(isset($topic))
                                    <p style="margin: 0 0 12px; font-size: 12px; color: #64748b;">Topik: <strong style="color: #1e293b;">{{ $topic }}</strong></p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <tr>
                                <td style="padding: 14px;">
                                    <p style="margin: 0 0 8px; font-size: 11px; color: #64748b;">
                                        <strong style="color: #1e40af;">{{ $staffName }}</strong> mengatakan:
                                    </p>
                                    <p style="margin: 0; color: #1e293b; font-size: 14px; line-height: 1.5; font-style: italic;">
                                        "{{ $messagePreview }}"
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Button --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 6px;">
                        <a href="{{ $chatUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Balasan Lengkap →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Silakan login ke website perpustakaan untuk melihat balasan lengkap dan melanjutkan percakapan.
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
