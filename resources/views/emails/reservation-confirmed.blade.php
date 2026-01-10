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
                Reservasi Anda telah dikonfirmasi. Berikut adalah detail reservasi:
            </p>
        </td>
    </tr>
    
    {{-- Reservation Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; border: 1px solid #bfdbfe;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">✓ RESERVASI DIKONFIRMASI</p>
                        <p style="margin: 0 0 12px; font-size: 16px; color: #1e293b; font-weight: 600;">{{ $roomName }}</p>
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <tr>
                                <td style="padding: 12px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding: 4px 0; color: #475569; font-size: 13px;">
                                                📅 <strong>Tanggal:</strong> {{ $reservationDate }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 4px 0; color: #475569; font-size: 13px;">
                                                🕐 <strong>Waktu:</strong> {{ $startTime }} - {{ $endTime }}
                                            </td>
                                        </tr>
                                        @if(isset($purpose))
                                        <tr>
                                            <td style="padding: 4px 0; color: #475569; font-size: 13px;">
                                                📝 <strong>Keperluan:</strong> {{ $purpose }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Confirmation Badge --}}
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <span style="display: inline-block; padding: 6px 16px; background-color: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; border-radius: 20px; border: 1px solid #bbf7d0;">
                No. Reservasi: {{ $reservationNumber }}
            </span>
        </td>
    </tr>

    {{-- Button --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 6px;">
                        <a href="{{ $detailUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Detail Reservasi →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Mohon hadir tepat waktu. Jika ada perubahan, silakan hubungi bagian layanan perpustakaan.
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
