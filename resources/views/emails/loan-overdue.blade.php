@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Alert Icon --}}
    <tr>
        <td align="center" style="padding-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                ⚠️
            </div>
        </td>
    </tr>

    <tr>
        <td align="center">
            <h2 style="margin: 0 0 8px 0; color: #dc2626; font-size: 20px; font-weight: 600;">
                Buku Terlambat Dikembalikan
            </h2>
            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px;">
                Segera kembalikan untuk menghindari denda lebih lanjut
            </p>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 20px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                Halo <strong style="color: #1e293b;">{{ $name }}</strong>,
            </p>
        </td>
    </tr>
    
    {{-- Book Card --}}
    <tr>
        <td style="padding-bottom: 24px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 12px; border-left: 4px solid #dc2626;">
                <tr>
                    <td style="padding: 20px;">
                        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 16px; font-weight: 600; line-height: 1.4;">
                            {{ $bookTitle }}
                        </h3>
                        <p style="margin: 0 0 12px 0; color: #64748b; font-size: 13px;">
                            {{ $bookAuthor }}
                        </p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 4px 0;">
                                    <p style="margin: 0; color: #64748b; font-size: 13px;">
                                        Jatuh tempo: <strong style="color: #dc2626;">{{ $dueDate }}</strong>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;">
                                    <p style="margin: 0; color: #64748b; font-size: 13px;">
                                        Keterlambatan: <strong style="color: #dc2626;">{{ $daysOverdue }} hari</strong>
                                    </p>
                                </td>
                            </tr>
                            @if(isset($fine) && $fine > 0)
                            <tr>
                                <td style="padding: 8px 0 0 0;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" style="background-color: #fee2e2; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 8px 12px;">
                                                <p style="margin: 0; color: #dc2626; font-size: 14px; font-weight: 600;">
                                                    💰 Denda: Rp {{ number_format($fine, 0, ',', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 24px 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Mohon segera kembalikan buku ke perpustakaan untuk menghindari penambahan denda. Jika ada kendala, silakan hubungi petugas perpustakaan.
            </p>
        </td>
    </tr>

    {{-- CTA Button --}}
    <tr>
        <td align="center">
            <a href="{{ $portalUrl }}" style="display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Lihat Detail Peminjaman
            </a>
        </td>
    </tr>
</table>
@endsection
