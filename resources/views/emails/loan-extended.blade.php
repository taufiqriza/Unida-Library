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
                Perpanjangan peminjaman buku Anda telah berhasil diproses.
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 4px; font-size: 10px; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">✓ Perpanjangan Berhasil</p>
                        <p style="margin: 0 0 12px; font-size: 14px; color: #1e293b; font-weight: 500;">{{ $bookTitle }}</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 4px 0; color: #64748b; font-size: 12px;">
                                    Batas lama: <span style="text-decoration: line-through;">{{ $oldDueDate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #166534; font-size: 13px; font-weight: 600;">
                                    Batas baru: {{ $newDueDate }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.5;">
                Harap kembalikan buku sebelum tanggal jatuh tempo untuk menghindari denda keterlambatan.
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
