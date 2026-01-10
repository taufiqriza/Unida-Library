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
                Kabar baik! Permintaan buku yang Anda ajukan telah disetujui dan akan segera diproses.
            </p>
        </td>
    </tr>
    
    {{-- Book Request Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">✓ PERMINTAAN DISETUJUI</p>
                        <p style="margin: 0 0 6px; font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $bookTitle }}</p>
                        @if(isset($bookAuthor))
                        <p style="margin: 0 0 12px; font-size: 12px; color: #64748b;">Penulis: {{ $bookAuthor }}</p>
                        @endif
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #dcfce7;">
                            <tr>
                                <td style="padding: 12px;">
                                    <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Estimasi Pengadaan</p>
                                    <p style="margin: 0; font-size: 13px; color: #166534; font-weight: 500;">{{ $estimatedDate ?? 'Akan diinformasikan kemudian' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Info Box --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border-radius: 6px; border: 1px solid #93c5fd;">
                <tr>
                    <td style="padding: 12px 14px;">
                        <p style="margin: 0; font-size: 12px; color: #1e40af; line-height: 1.5;">
                            💡 Anda akan mendapat notifikasi kembali ketika buku sudah tersedia di perpustakaan.
                        </p>
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
                        <a href="{{ $requestsUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Daftar Permintaan →
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
