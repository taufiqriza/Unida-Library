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
                Selamat! Tugas akhir Anda telah <strong>disetujui</strong> dan akan segera dipublikasikan di repositori UNIDA Library.
            </p>
        </td>
    </tr>
    
    {{-- Thesis Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">🎓 TUGAS AKHIR DISETUJUI</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $title }}</p>
                        
                        @if(isset($nim))
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <p style="margin: 0; color: #64748b; font-size: 12px;">🎓 NIM: <strong style="color: #1e293b;">{{ $nim }}</strong></p>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Congratulations Badge --}}
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <span style="display: inline-block; padding: 6px 16px; background-color: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; border-radius: 20px; border: 1px solid #bbf7d0;">
                🎉 Selamat atas pencapaian Anda!
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
