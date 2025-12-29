<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'UNIDA Library' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 32px 40px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="width: 56px; height: 56px; background-color: rgba(255,255,255,0.2); border-radius: 12px; display: inline-block; line-height: 56px; font-size: 28px;">
                                            📚
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 16px;">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 600; letter-spacing: -0.5px;">
                                            UNIDA Library
                                        </h1>
                                        <p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.8); font-size: 13px;">
                                            Perpustakaan Universitas Darussalam Gontor
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 40px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px;">
                                            © {{ date('Y') }} Perpustakaan UNIDA Gontor
                                        </p>
                                        <p style="margin: 0; color: #94a3b8; font-size: 11px;">
                                            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                {{-- Bottom Link --}}
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 520px;">
                    <tr>
                        <td align="center" style="padding: 24px 20px;">
                            <a href="{{ config('app.url') }}" style="color: #3b82f6; font-size: 12px; text-decoration: none;">
                                library.unida.gontor.ac.id
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
