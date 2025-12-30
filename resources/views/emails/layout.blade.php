<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'UNIDA Library' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #eff6ff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff;">
        <tr>
            <td align="center" style="padding: 32px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 540px;">
                    
                    {{-- Header with gradient --}}
                    <tr>
                        <td>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 12px 12px 0 0;">
                                <tr>
                                    <td style="padding: 24px 28px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/logo.png') }}" alt="UNIDA Library" style="height: 36px; width: auto;" onerror="this.style.display='none'">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Main Card --}}
                    <tr>
                        <td>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 0 0 12px 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <tr>
                                    <td style="padding: 28px;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 8px; font-size: 12px; color: #1e40af; font-weight: 500;">
                                            Perpustakaan Universitas Darussalam Gontor
                                        </p>
                                        <p style="margin: 0 0 12px; font-size: 11px; color: #64748b;">
                                            Jl. Raya Siman Km. 6, Ponorogo, Jawa Timur
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 0 6px;">
                                                    <a href="{{ config('app.url') }}" style="color: #3b82f6; font-size: 11px; text-decoration: none;">🌐 Website</a>
                                                </td>
                                                <td style="padding: 0 6px;">
                                                    <a href="mailto:library@unida.gontor.ac.id" style="color: #3b82f6; font-size: 11px; text-decoration: none;">✉️ Email</a>
                                                </td>
                                                <td style="padding: 0 6px;">
                                                    <a href="https://instagram.com/perpustakaanunida" style="color: #3b82f6; font-size: 11px; text-decoration: none;">📷 Instagram</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
