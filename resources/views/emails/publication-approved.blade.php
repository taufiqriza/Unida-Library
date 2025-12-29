@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Success Icon --}}
    <tr>
        <td align="center" style="padding-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                ✓
            </div>
        </td>
    </tr>

    <tr>
        <td align="center">
            <h2 style="margin: 0 0 8px 0; color: #166534; font-size: 20px; font-weight: 600;">
                Karya Ilmiah Dipublikasikan!
            </h2>
            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px;">
                Selamat, karya Anda telah berhasil dipublikasikan
            </p>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 16px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                Halo <strong style="color: #1e293b;">{{ $author }}</strong>,
            </p>
        </td>
    </tr>
    
    {{-- Publication Card --}}
    <tr>
        <td style="padding: 8px 0 24px 0;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 12px; border-left: 4px solid #3b82f6;">
                <tr>
                    <td style="padding: 20px;">
                        <p style="margin: 0 0 4px 0; color: #3b82f6; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                            {{ $type }}
                        </p>
                        <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 16px; font-weight: 600; line-height: 1.4;">
                            {{ $title }}
                        </h3>
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding-right: 16px;">
                                    <p style="margin: 0; color: #64748b; font-size: 12px;">
                                        📅 Tahun: <strong style="color: #334155;">{{ $year }}</strong>
                                    </p>
                                </td>
                                @if(isset($nim))
                                <td>
                                    <p style="margin: 0; color: #64748b; font-size: 12px;">
                                        🎓 NIM: <strong style="color: #334155;">{{ $nim }}</strong>
                                    </p>
                                </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 24px 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Anda dapat melihat karya ilmiah dan mengunduh sertifikat publikasi melalui Member Portal.
            </p>
        </td>
    </tr>

    {{-- CTA Button --}}
    <tr>
        <td align="center" style="padding-bottom: 8px;">
            <a href="{{ $portalUrl }}" style="display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Buka Member Portal
            </a>
        </td>
    </tr>
</table>
@endsection
