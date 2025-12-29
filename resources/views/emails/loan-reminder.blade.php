@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Book Icon --}}
    <tr>
        <td align="center" style="padding-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                📖
            </div>
        </td>
    </tr>

    <tr>
        <td align="center">
            <h2 style="margin: 0 0 8px 0; color: #92400e; font-size: 20px; font-weight: 600;">
                Pengingat Pengembalian Buku
            </h2>
            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px;">
                Buku yang Anda pinjam akan segera jatuh tempo
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
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 12px; border-left: 4px solid #f59e0b;">
                <tr>
                    <td style="padding: 20px;">
                        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 16px; font-weight: 600; line-height: 1.4;">
                            {{ $bookTitle }}
                        </h3>
                        <p style="margin: 0 0 12px 0; color: #64748b; font-size: 13px;">
                            {{ $bookAuthor }}
                        </p>
                        <table role="presentation" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 6px;">
                            <tr>
                                <td style="padding: 8px 12px;">
                                    <p style="margin: 0; color: #92400e; font-size: 13px; font-weight: 600;">
                                        ⏰ Jatuh tempo: {{ $dueDate }}
                                    </p>
                                </td>
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
                Mohon kembalikan buku tepat waktu untuk menghindari denda keterlambatan. Anda juga dapat memperpanjang peminjaman melalui Member Portal jika buku tidak sedang dipesan oleh anggota lain.
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
