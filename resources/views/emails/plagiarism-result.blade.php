@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    {{-- Status Icon --}}
    <tr>
        <td align="center" style="padding-bottom: 24px;">
            @if($status === 'completed')
                @if($score <= 15)
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                    ✓
                </div>
                @elseif($score <= 25)
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                    ⚠️
                </div>
                @else
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                    ⚠️
                </div>
                @endif
            @else
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px;">
                ✗
            </div>
            @endif
        </td>
    </tr>

    <tr>
        <td align="center">
            <h2 style="margin: 0 0 8px 0; color: {{ $status === 'completed' ? '#1e40af' : '#dc2626' }}; font-size: 20px; font-weight: 600;">
                Hasil Cek Plagiasi
            </h2>
            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px;">
                {{ $status === 'completed' ? 'Pengecekan telah selesai' : 'Pengecekan gagal' }}
            </p>
        </td>
    </tr>

    <tr>
        <td>
            <p style="margin: 0 0 20px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum <strong style="color: #1e293b;">{{ $name }}</strong>,
            </p>
        </td>
    </tr>
    
    {{-- Document Card --}}
    <tr>
        <td style="padding-bottom: 24px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 12px; border-left: 4px solid {{ $status === 'completed' ? '#3b82f6' : '#dc2626' }};">
                <tr>
                    <td style="padding: 20px;">
                        <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            Dokumen
                        </p>
                        <h3 style="margin: 0 0 16px 0; color: #1e293b; font-size: 15px; font-weight: 600; line-height: 1.4;">
                            {{ $documentTitle }}
                        </h3>
                        
                        @if($status === 'completed')
                        {{-- Score Display --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px;">Tingkat Kemiripan</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding-right: 12px;">
                                                <span style="font-size: 32px; font-weight: 700; color: {{ $score <= 15 ? '#16a34a' : ($score <= 25 ? '#ca8a04' : '#dc2626') }};">
                                                    {{ $score }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($score <= 15)
                                                <span style="display: inline-block; padding: 4px 10px; background-color: #dcfce7; color: #166534; font-size: 11px; font-weight: 600; border-radius: 20px;">
                                                    RENDAH
                                                </span>
                                                @elseif($score <= 25)
                                                <span style="display: inline-block; padding: 4px 10px; background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 600; border-radius: 20px;">
                                                    SEDANG
                                                </span>
                                                @else
                                                <span style="display: inline-block; padding: 4px 10px; background-color: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 600; border-radius: 20px;">
                                                    TINGGI
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @else
                        {{-- Error Message --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fee2e2; border-radius: 6px;">
                            <tr>
                                <td style="padding: 10px 12px;">
                                    <p style="margin: 0; color: #dc2626; font-size: 13px;">
                                        {{ $errorMessage ?? 'Terjadi kesalahan saat memproses dokumen' }}
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

    @if($status === 'completed')
    <tr>
        <td style="padding-bottom: 24px;">
            @if($score <= 15)
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <tr>
                    <td style="padding: 12px 16px;">
                        <p style="margin: 0; color: #166534; font-size: 13px;">
                            ✅ Dokumen Anda memiliki tingkat kemiripan rendah. Bagus!
                        </p>
                    </td>
                </tr>
            </table>
            @elseif($score <= 25)
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fefce8; border-radius: 8px; border: 1px solid #fef08a;">
                <tr>
                    <td style="padding: 12px 16px;">
                        <p style="margin: 0; color: #854d0e; font-size: 13px;">
                            ⚠️ Dokumen Anda memiliki tingkat kemiripan sedang. Mohon periksa kembali bagian yang terdeteksi.
                        </p>
                    </td>
                </tr>
            </table>
            @else
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                <tr>
                    <td style="padding: 12px 16px;">
                        <p style="margin: 0; color: #dc2626; font-size: 13px;">
                            ❌ Dokumen Anda memiliki tingkat kemiripan tinggi. Perlu dilakukan revisi sebelum dapat diterima.
                        </p>
                    </td>
                </tr>
            </table>
            @endif
        </td>
    </tr>
    @endif

    {{-- CTA Button --}}
    <tr>
        <td align="center">
            <a href="{{ $detailUrl }}" style="display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Lihat Detail Hasil
            </a>
        </td>
    </tr>

    <tr>
        <td style="padding-top: 24px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                Wassalamu'alaikum,<br>
                <strong>Perpustakaan UNIDA Gontor</strong>
            </p>
        </td>
    </tr>
</table>
@endsection
