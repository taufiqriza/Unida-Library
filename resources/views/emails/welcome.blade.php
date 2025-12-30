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
                Selamat datang di UNIDA Library! Akun Anda telah berhasil dibuat dan siap digunakan.
            </p>
        </td>
    </tr>
    
    {{-- Features Box --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 10px; font-size: 12px; color: #1e40af; font-weight: 600;">Dengan akun ini, Anda dapat:</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr><td style="padding: 4px 0; color: #475569; font-size: 13px;">✓ Meminjam buku dari koleksi perpustakaan</td></tr>
                            <tr><td style="padding: 4px 0; color: #475569; font-size: 13px;">✓ Mengakses e-book dan e-journal</td></tr>
                            <tr><td style="padding: 4px 0; color: #475569; font-size: 13px;">✓ Mengunggah karya ilmiah untuk publikasi</td></tr>
                            <tr><td style="padding: 4px 0; color: #475569; font-size: 13px;">✓ Melihat riwayat peminjaman</td></tr>
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
                        <a href="{{ $loginUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Masuk ke Akun →
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
