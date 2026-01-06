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
                Buku yang Anda reservasi sudah tersedia dan siap diambil!
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; border: 1px solid #bfdbfe;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">📚 Reservasi Siap</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500;">{{ $bookTitle }}</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 4px 0; color: #475569; font-size: 13px;">
                                    📍 Lokasi: <strong>{{ $pickupLocation }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #dc2626; font-size: 13px; font-weight: 500;">
                                    ⏰ Ambil sebelum: {{ $pickupDeadline }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.5;">
                Jika tidak diambil sebelum batas waktu, reservasi akan otomatis dibatalkan.
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
