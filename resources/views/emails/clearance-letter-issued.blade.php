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
                Selamat! Surat Bebas Pustaka Anda telah berhasil diterbitkan oleh Perpustakaan UNIDA Gontor.
            </p>
        </td>
    </tr>
    
    {{-- Clearance Letter Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">✓ SURAT BEBAS PUSTAKA</p>
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #dcfce7; margin-top: 12px;">
                            <tr>
                                <td style="padding: 12px;">
                                    <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Nomor Surat</p>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #166534;">{{ $letterNumber }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 12px 12px;">
                                    <p style="margin: 0 0 6px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Keperluan</p>
                                    <p style="margin: 0; font-size: 13px; font-weight: 500; color: #1e293b;">{{ $purpose }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Requirements Met Badge --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ecfdf5; border-radius: 6px; border: 1px solid #a7f3d0;">
                <tr>
                    <td style="padding: 12px 14px;">
                        <p style="margin: 0 0 6px; font-size: 11px; color: #166534; font-weight: 600;">Persyaratan Terpenuhi:</p>
                        <p style="margin: 0; color: #047857; font-size: 12px; line-height: 1.6;">
                            ✓ Tidak memiliki tunggakan peminjaman<br>
                            ✓ Telah mengunggah tugas akhir ke repositori
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
                        <a href="{{ $letterUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Unduh Surat →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Surat ini dapat digunakan untuk keperluan administrasi akademik Anda. Silakan download dan cetak jika diperlukan.
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
