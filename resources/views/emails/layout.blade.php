<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'UNIDA Library' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 520px;">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="padding-bottom: 24px;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width: 40px; height: 40px; background-color: #1e40af; border-radius: 10px; text-align: center; vertical-align: middle;">
                                        <span style="color: #ffffff; font-size: 18px; line-height: 40px;">📚</span>
                                    </td>
                                    <td style="padding-left: 12px;">
                                        <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;">UNIDA Library</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Main Card --}}
                    <tr>
                        <td>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 32px;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding: 24px 0 0;">
                            <p style="margin: 0 0 4px; font-size: 12px; color: #64748b;">
                                Perpustakaan Universitas Darussalam Gontor
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                library.unida.gontor.ac.id
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
