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
                Kami informasikan bahwa akun keanggotaan perpustakaan Anda telah dinonaktifkan.
            </p>
        </td>
    </tr>
    
    {{-- Account Status Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">⚠️ AKUN DINONAKTIFKAN</p>
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #fecaca; margin-top: 12px;">
                            <tr>
                                <td style="padding: 12px;">
                                    <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Member ID</p>
                                    <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 600;">{{ $memberId }}</p>
                                    
                                    @if(isset($reason))
                                    <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Alasan</p>
                                    <p style="margin: 0; font-size: 13px; color: #dc2626;">{{ $reason }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Impact Info --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fee2e2; border-radius: 6px; border: 1px solid #fca5a5;">
                <tr>
                    <td style="padding: 12px 14px;">
                        <p style="margin: 0 0 8px; font-size: 11px; color: #b91c1c; font-weight: 600;">Dampak Penonaktifan:</p>
                        <p style="margin: 0; color: #991b1b; font-size: 12px; line-height: 1.6;">
                            ✕ Tidak dapat meminjam buku<br>
                            ✕ Tidak dapat mengakses e-resources<br>
                            ✕ Tidak dapat menggunakan fasilitas perpustakaan
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
                        <a href="{{ $contactUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Hubungi Perpustakaan →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Jika Anda merasa ini adalah kesalahan atau ingin mengaktifkan kembali akun, silakan hubungi bagian layanan perpustakaan.
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
