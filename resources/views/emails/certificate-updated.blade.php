@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong>{{ $memberName }}</strong>,
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Kami ingin memberitahukan bahwa telah dilakukan pembaruan pada format sertifikat plagiarism check Anda. 
                Mohon maaf atas ketidaknyamanan ini.
            </p>
        </td>
    </tr>
    
    {{-- Certificate Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 8px; border: 1px solid #fbbf24;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">SERTIFIKAT ORIGINALITAS</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.4;">{{ $documentTitle }}</p>
                        
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding-right: 16px;">
                                    <p style="margin: 0; color: #64748b; font-size: 12px;">📋 No. Sertifikat: <strong style="color: #1e293b;">{{ $certificateNumber }}</strong></p>
                                </td>
                                <td>
                                    <p style="margin: 0; color: #64748b; font-size: 12px;">📊 Similarity: <strong style="color: #1e293b;">{{ $similarityScore }}%</strong></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Update Info --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border-radius: 8px; border: 1px solid #93c5fd;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 8px; font-size: 12px; color: #1e40af; font-weight: 600;">✨ Pembaruan Sertifikat:</p>
                        <ul style="margin: 0; padding-left: 16px; color: #475569; font-size: 13px; line-height: 1.5;">
                            <li>Layout dan desain yang lebih profesional</li>
                            <li>QR Code verifikasi yang disempurnakan</li>
                            <li>Format PNG berkualitas tinggi</li>
                            <li>Konsistensi tampilan preview dan download</li>
                        </ul>
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
                        <a href="{{ $dashboardUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Download Sertifikat Terbaru →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                Silakan login ke dashboard member Anda untuk mendownload sertifikat dengan format terbaru. 
                Sertifikat lama tetap valid, namun kami merekomendasikan menggunakan versi terbaru.
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
