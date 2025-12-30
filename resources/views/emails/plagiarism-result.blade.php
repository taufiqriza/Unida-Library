@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Greeting --}}
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong>{{ $name }}</strong>,
            </p>
        </td>
    </tr>
    
    {{-- Message --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                @if($status === 'completed')
                Pengecekan plagiasi dokumen Anda telah selesai dengan hasil berikut:
                @else
                Mohon maaf, pengecekan plagiasi dokumen Anda mengalami kendala.
                @endif
            </p>
        </td>
    </tr>
    
    {{-- Result Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 16px;">
                        {{-- Document Title --}}
                        <p style="margin: 0 0 4px; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Dokumen</p>
                        <p style="margin: 0 0 14px; font-size: 13px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $documentTitle }}</p>
                        
                        @if($status === 'completed')
                        {{-- Score Section --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <tr>
                                <td style="padding: 12px; text-align: center; border-right: 1px solid #e2e8f0;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Similarity</p>
                                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #1e40af;">{{ $score }}%</p>
                                </td>
                                <td style="padding: 12px; text-align: center;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Status</p>
                                    @if($score <= 25)
                                    <span style="display: inline-block; padding: 4px 12px; background-color: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; border-radius: 12px;">
                                        ✓ Lolos
                                    </span>
                                    @else
                                    <span style="display: inline-block; padding: 4px 12px; background-color: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 600; border-radius: 12px;">
                                        Revisi
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @else
                        {{-- Error --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 6px;">
                            <tr>
                                <td style="padding: 10px 12px;">
                                    <p style="margin: 0; color: #dc2626; font-size: 12px;">
                                        {{ $errorMessage ?? 'Terjadi kesalahan saat memproses dokumen.' }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif
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
                        <a href="{{ $detailUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Detail →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Closing --}}
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
