@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum,
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Berikut ringkasan aktivitas perpustakaan bulan <strong>{{ $month }}</strong>:
            </p>
        </td>
    </tr>
    
    {{-- Stats --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50%" style="padding-right: 8px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border-radius: 8px; text-align: center;">
                            <tr>
                                <td style="padding: 16px;">
                                    <p style="margin: 0; font-size: 24px; color: #1e40af; font-weight: 700;">{{ number_format($totalVisitors) }}</p>
                                    <p style="margin: 4px 0 0; font-size: 11px; color: #64748b; text-transform: uppercase;">Pengunjung</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" style="padding-left: 8px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; text-align: center;">
                            <tr>
                                <td style="padding: 16px;">
                                    <p style="margin: 0; font-size: 24px; color: #166534; font-weight: 700;">{{ number_format($totalLoans) }}</p>
                                    <p style="margin: 4px 0 0; font-size: 11px; color: #64748b; text-transform: uppercase;">Peminjaman</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    {{-- Top Books --}}
    @if(!empty($topBooks))
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0 0 10px; font-size: 13px; color: #1e293b; font-weight: 600;">📚 Buku Terpopuler</p>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 12px;">
                        @foreach($topBooks as $i => $book)
                        <p style="margin: {{ $i > 0 ? '8px' : '0' }} 0 0; font-size: 13px; color: #475569;">
                            {{ $i + 1 }}. {{ $book['title'] }} <span style="color: #94a3b8;">({{ $book['loans'] }}x)</span>
                        </p>
                        @endforeach
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    
    {{-- Upcoming Events --}}
    @if(!empty($upcomingEvents))
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0 0 10px; font-size: 13px; color: #1e293b; font-weight: 600;">📅 Kegiatan Mendatang</p>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 8px; border: 1px solid #fcd34d;">
                <tr>
                    <td style="padding: 12px;">
                        @foreach($upcomingEvents as $event)
                        <p style="margin: 0 0 4px; font-size: 13px; color: #1e293b; font-weight: 500;">{{ $event['title'] }}</p>
                        <p style="margin: 0; font-size: 12px; color: #92400e;">{{ $event['date'] }}</p>
                        @endforeach
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    
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
