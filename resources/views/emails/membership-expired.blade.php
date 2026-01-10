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
                @if($isExpired)
                Keanggotaan perpustakaan Anda telah berakhir. Silakan perpanjang untuk melanjutkan layanan.
                @else
                Keanggotaan perpustakaan Anda akan segera berakhir. Mohon perpanjang sebelum masa aktif habis.
                @endif
            </p>
        </td>
    </tr>
    
    {{-- Membership Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            @if($isExpired)
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
            @else
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 8px; border: 1px solid #fde047;">
            @endif
                <tr>
                    <td style="padding: 16px;">
                        @if($isExpired)
                        <p style="margin: 0 0 4px; font-size: 10px; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">⚠️ KEANGGOTAAN BERAKHIR</p>
                        @else
                        <p style="margin: 0 0 4px; font-size: 10px; color: #a16207; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">⏰ SEGERA BERAKHIR</p>
                        @endif
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 12px;">
                            <tr>
                                <td style="padding: 12px; border-right: 1px solid #e2e8f0;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Member ID</p>
                                    <p style="margin: 0; font-size: 13px; color: #1e293b; font-weight: 600;">{{ $memberId }}</p>
                                </td>
                                <td style="padding: 12px;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Berlaku Hingga</p>
                                    @if($isExpired)
                                    <p style="margin: 0; font-size: 13px; color: #dc2626; font-weight: 600;">{{ $expiryDate }}</p>
                                    @else
                                    <p style="margin: 0; font-size: 13px; color: #a16207; font-weight: 600;">{{ $expiryDate }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        @if($isExpired)
                        <p style="margin: 12px 0 0; padding: 8px 12px; background-color: #fee2e2; border-radius: 4px; color: #dc2626; font-size: 12px;">
                            Layanan peminjaman tidak dapat digunakan hingga diperpanjang.
                        </p>
                        @else
                        <p style="margin: 12px 0 0; padding: 8px 12px; background-color: #fef3c7; border-radius: 4px; color: #92400e; font-size: 12px;">
                            Sisa waktu: <strong>{{ $daysRemaining }} hari</strong>
                        </p>
                        @endif
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
                        <a href="{{ $renewUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Perpanjang Keanggotaan →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Untuk perpanjangan, silakan hubungi bagian keanggotaan perpustakaan atau kunjungi counter layanan.
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
