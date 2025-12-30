<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'UNIDA Library' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 48px 24px;">
                
                {{-- Logo Section --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px;">
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); width: 48px; height: 48px; border-radius: 12px; text-align: center; vertical-align: middle;">
                                        <span style="font-size: 24px; line-height: 48px;">📚</span>
                                    </td>
                                    <td style="padding-left: 14px;">
                                        <p style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px;">UNIDA Library</p>
                                        <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">Perpustakaan Digital</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                {{-- Main Card --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05);">
                    
                    {{-- Accent Bar --}}
                    <tr>
                        <td style="height: 4px; background: linear-gradient(90deg, #2563eb 0%, #7c3aed 50%, #2563eb 100%);"></td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 40px 44px;">
                            @yield('content')
                        </td>
                    </tr>

                </table>

                {{-- Footer --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px;">
                    <tr>
                        <td align="center" style="padding: 32px 20px 16px;">
                            <p style="margin: 0 0 6px 0; font-size: 13px; color: #64748b;">
                                Perpustakaan Universitas Darussalam Gontor
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Jl. Raya Siman Km. 6, Ponorogo, Jawa Timur
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 16px 20px;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 0 8px;">
                                        <a href="{{ config('app.url') }}" style="color: #2563eb; font-size: 12px; text-decoration: none;">Website</a>
                                    </td>
                                    <td style="color: #cbd5e1;">•</td>
                                    <td style="padding: 0 8px;">
                                        <a href="mailto:perpustakaan@unida.gontor.ac.id" style="color: #2563eb; font-size: 12px; text-decoration: none;">Email</a>
                                    </td>
                                    <td style="color: #cbd5e1;">•</td>
                                    <td style="padding: 0 8px;">
                                        <a href="https://wa.me/6285156789012" style="color: #2563eb; font-size: 12px; text-decoration: none;">WhatsApp</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 16px 20px 0;">
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                © {{ date('Y') }} UNIDA Library. Email ini dikirim otomatis.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
