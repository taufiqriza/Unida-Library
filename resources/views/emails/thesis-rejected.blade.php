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
                Mohon maaf, tugas akhir Anda <strong>tidak dapat diproses</strong>. Mohon perhatikan alasan penolakan berikut.
            </p>
        </td>
    </tr>
    
    {{-- Thesis Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">✕ SUBMISSION DITOLAK</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $title }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Rejection Reason --}}
    @if(isset($rejectionReason) && $rejectionReason)
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fee2e2; border-radius: 6px; border: 1px solid #fca5a5;">
                <tr>
                    <td style="padding: 14px;">
                        <p style="margin: 0 0 8px; font-size: 11px; color: #b91c1c; font-weight: 600;">⚠️ ALASAN PENOLAKAN:</p>
                        <p style="margin: 0; color: #991b1b; font-size: 13px; line-height: 1.6;">{{ $rejectionReason }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    {{-- Button --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 6px;">
                        <a href="{{ $submitUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Ajukan Ulang →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Silakan perbaiki tugas akhir Anda sesuai dengan ketentuan yang berlaku dan ajukan kembali melalui portal member.
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
