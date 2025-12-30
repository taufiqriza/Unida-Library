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
                Buku yang Anda pinjam akan segera jatuh tempo. Mohon kembalikan tepat waktu.
            </p>
        </td>
    </tr>
    
    {{-- Book Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Buku</p>
                        <p style="margin: 0 0 6px; font-size: 14px; color: #1e293b; font-weight: 500;">{{ $bookTitle }}</p>
                        <p style="margin: 0 0 12px; font-size: 12px; color: #64748b;">{{ $bookAuthor }}</p>
                        
                        <table role="presentation" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 4px;">
                            <tr>
                                <td style="padding: 8px 12px;">
                                    <p style="margin: 0; color: #92400e; font-size: 12px; font-weight: 500;">⏰ Jatuh tempo: {{ $dueDate }}</p>
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
                        <a href="{{ $portalUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Detail →
                        </a>
                    </td>
                </tr>
            </table>
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
