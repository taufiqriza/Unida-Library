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
                Tugas akhir Anda membutuhkan <strong>revisi</strong> sebelum dapat dipublikasikan. Mohon perhatikan catatan dari reviewer berikut.
            </p>
        </td>
    </tr>
    
    {{-- Thesis Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 8px; border: 1px solid #fde047;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #a16207; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">⚠️ PERLU REVISI</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $title }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Review Notes --}}
    @if(isset($reviewNotes) && $reviewNotes)
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fffbeb; border-radius: 6px; border: 1px solid #fcd34d;">
                <tr>
                    <td style="padding: 14px;">
                        <p style="margin: 0 0 8px; font-size: 11px; color: #92400e; font-weight: 600;">📝 CATATAN REVISI:</p>
                        <p style="margin: 0; color: #78350f; font-size: 13px; line-height: 1.6;">{{ $reviewNotes }}</p>
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
                        <a href="{{ $editUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Edit Submission →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Silakan perbaiki tugas akhir Anda sesuai catatan di atas dan submit ulang untuk direview kembali.
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
