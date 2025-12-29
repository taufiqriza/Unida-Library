@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Welcome Icon --}}
    <tr>
        <td align="center" style="padding-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                👋
            </div>
        </td>
    </tr>

    <tr>
        <td align="center">
            <h2 style="margin: 0 0 8px 0; color: #1e40af; font-size: 20px; font-weight: 600;">
                Selamat Datang!
            </h2>
            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px;">
                Akun Anda telah berhasil dibuat
            </p>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 20px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                Halo <strong style="color: #1e293b;">{{ $name }}</strong>,
            </p>
            <p style="margin: 0 0 24px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                Terima kasih telah mendaftar di UNIDA Library. Akun Anda telah aktif dan siap digunakan.
            </p>
        </td>
    </tr>
    
    {{-- Features --}}
    <tr>
        <td style="padding-bottom: 24px;">
            <p style="margin: 0 0 12px 0; color: #334155; font-size: 14px; font-weight: 600;">
                Dengan akun ini, Anda dapat:
            </p>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 8px 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="width: 24px; vertical-align: top;">
                                    <span style="color: #3b82f6;">✓</span>
                                </td>
                                <td style="color: #475569; font-size: 14px;">
                                    Meminjam buku dari koleksi perpustakaan
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="width: 24px; vertical-align: top;">
                                    <span style="color: #3b82f6;">✓</span>
                                </td>
                                <td style="color: #475569; font-size: 14px;">
                                    Mengakses e-book dan e-journal
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="width: 24px; vertical-align: top;">
                                    <span style="color: #3b82f6;">✓</span>
                                </td>
                                <td style="color: #475569; font-size: 14px;">
                                    Mengunggah karya ilmiah untuk publikasi
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="width: 24px; vertical-align: top;">
                                    <span style="color: #3b82f6;">✓</span>
                                </td>
                                <td style="color: #475569; font-size: 14px;">
                                    Melihat riwayat peminjaman
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- CTA Button --}}
    <tr>
        <td align="center">
            <a href="{{ $loginUrl }}" style="display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Masuk ke Akun
            </a>
        </td>
    </tr>
</table>
@endsection
