@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <p style="margin: 0 0 20px; color: #374151; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong>{{ $name }}</strong>,
            </p>
            
            <p style="margin: 0 0 24px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                @if($status === 'completed')
                Pengecekan plagiasi dokumen Anda telah selesai.
                @else
                Pengecekan plagiasi dokumen Anda mengalami kendala.
                @endif
            </p>
        </td>
    </tr>
    
    {{-- Result Box --}}
    <tr>
        <td style="padding-bottom: 24px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 20px;">
                        <p style="margin: 0 0 4px; font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;">Dokumen</p>
                        <p style="margin: 0 0 16px; font-size: 14px; color: #1f2937; font-weight: 500;">{{ $documentTitle }}</p>
                        
                        @if($status === 'completed')
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding-right: 16px; border-right: 1px solid #e5e7eb;">
                                    <p style="margin: 0 0 2px; font-size: 11px; color: #9ca3af; text-transform: uppercase;">Similarity</p>
                                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #1e40af;">{{ $score }}%</p>
                                </td>
                                <td style="padding-left: 16px;">
                                    <p style="margin: 0 0 2px; font-size: 11px; color: #9ca3af; text-transform: uppercase;">Status</p>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: {{ $score <= 25 ? '#059669' : '#dc2626' }};">
                                        {{ $score <= 25 ? 'Lolos' : 'Perlu Revisi' }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @else
                        <p style="margin: 0; font-size: 13px; color: #dc2626;">
                            {{ $errorMessage ?? 'Terjadi kesalahan saat memproses dokumen.' }}
                        </p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Button --}}
    <tr>
        <td>
            <a href="{{ $detailUrl }}" style="display: inline-block; background-color: #1e40af; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 14px;">
                Lihat Detail
            </a>
        </td>
    </tr>

    <tr>
        <td style="padding-top: 24px;">
            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                Wassalamu'alaikum
            </p>
        </td>
    </tr>
</table>
@endsection
