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
                Buku yang Anda pinjam sudah melewati batas waktu pengembalian. Mohon segera kembalikan.
            </p>
        </td>
    </tr>
    
    {{-- Book Card --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Buku</p>
                        <p style="margin: 0 0 6px; font-size: 14px; color: #1e293b; font-weight: 500;">{{ $bookTitle }}</p>
                        <p style="margin: 0 0 12px; font-size: 12px; color: #64748b;">{{ $bookAuthor }}</p>
                        
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 4px; border: 1px solid #fecaca;">
                            <tr>
                                <td style="padding: 10px 12px; border-right: 1px solid #fecaca;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Jatuh Tempo</p>
                                    <p style="margin: 0; font-size: 13px; color: #dc2626; font-weight: 600;">{{ $dueDate }}</p>
                                </td>
                                <td style="padding: 10px 12px;" width="50%">
                                    <p style="margin: 0 0 2px; font-size: 10px; color: #94a3b8; text-transform: uppercase;">Terlambat</p>
                                    <p style="margin: 0; font-size: 13px; color: #dc2626; font-weight: 600;">{{ $daysOverdue }} hari</p>
                                </td>
                            </tr>
                        </table>
                        
                        @if(isset($fine) && $fine > 0)
                        <p style="margin: 12px 0 0; padding: 8px 12px; background-color: #fee2e2; border-radius: 4px; color: #dc2626; font-size: 13px; font-weight: 600;">
                            💰 Denda: Rp {{ number_format($fine, 0, ',', '.') }}
                        </p>
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
                        <a href="{{ $portalUrl }}" style="display: inline-block; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: 500; font-size: 13px;">
                            Lihat Detail →
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
