@extends('emails.layout')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">
                Assalamu'alaikum Warahmatullahi Wabarakatuh,
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 8px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Yth. Dekan dan Civitas Akademika<br>
                <strong style="color: #1e293b;">{{ $recipientName }}</strong><br>
                Universitas Darussalam Gontor
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                Perpustakaan UNIDA Gontor menginformasikan bahwa kami telah meluncurkan 
                <strong>sistem layanan digital terbaru</strong> untuk mendukung proses akademik.
            </p>
        </td>
    </tr>
    
    {{-- Layanan 1: Unggah Tugas Akhir --}}
    <tr>
        <td style="padding-bottom: 16px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border-radius: 8px; border: 1px solid #86efac;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 8px; font-size: 12px; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">🎓 UNGGAH TUGAS AKHIR ONLINE</p>
                        <ul style="margin: 0; padding-left: 16px; color: #475569; font-size: 13px; line-height: 1.6;">
                            <li>Upload dokumen PDF langsung dari browser</li>
                            <li>Validasi otomatis format dan kelengkapan</li>
                            <li>Tracking status pengajuan real-time</li>
                            <li>Notifikasi email setiap perubahan status</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Layanan 2: Cek Plagiasi --}}
    <tr>
        <td style="padding-bottom: 16px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 8px; border: 1px solid #fbbf24;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 8px; font-size: 12px; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">🔍 SISTEM CEK PLAGIASI TERINTEGRASI</p>
                        <ul style="margin: 0; padding-left: 16px; color: #475569; font-size: 13px; line-height: 1.6;">
                            <li>Powered by Turnitin - standar internasional</li>
                            <li>Database 190+ juta publikasi ilmiah</li>
                            <li>Deteksi AI Writing (ChatGPT, dll)</li>
                            <li>Hasil similarity report dalam hitungan menit</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Layanan 3: Sertifikat --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border-radius: 8px; border: 1px solid #93c5fd;">
                <tr>
                    <td style="padding: 16px;">
                        <p style="margin: 0 0 8px; font-size: 12px; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">📜 SERTIFIKAT ORIGINALITAS DIGITAL</p>
                        <ul style="margin: 0; padding-left: 16px; color: #475569; font-size: 13px; line-height: 1.6;">
                            <li>Sertifikat resmi dari Perpustakaan UNIDA</li>
                            <li>QR Code verifikasi keaslian</li>
                            <li>Nomor sertifikat unik terverifikasi online</li>
                            <li>Download kapan saja melalui dashboard</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Mekanisme --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0 0 8px; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">📋 Mekanisme Penggunaan:</p>
            <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.8;">
                1. Mahasiswa mendaftar/login di website<br>
                2. Upload dokumen tugas akhir (PDF)<br>
                3. Sistem melakukan pengecekan plagiasi<br>
                4. Hasil tersedia di dashboard<br>
                5. Jika lolos, sertifikat dapat didownload
            </p>
        </td>
    </tr>

    {{-- Button Besar --}}
    <tr>
        <td style="padding-bottom: 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">
                        <table role="presentation" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 8px; width: 100%;">
                            <tr>
                                <td align="center" style="padding: 20px 32px;">
                                    <a href="{{ $websiteUrl }}" style="display: block; color: #ffffff; text-decoration: none;">
                                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">Kunjungi Sekarang</span><br>
                                        <span style="font-size: 18px; font-weight: 700; letter-spacing: 0.5px;">🌐 WEBSITE PERPUSTAKAAN UNIDA</span>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom: 16px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                Mohon bantuan Bapak/Ibu untuk menyampaikan informasi ini kepada seluruh 
                mahasiswa, khususnya mahasiswa tingkat akhir.
            </p>
        </td>
    </tr>
    
    <tr>
        <td>
            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                Wassalamu'alaikum Warahmatullahi Wabarakatuh,<br><br>
                <span style="color: #1e40af; font-weight: 500;">Tim Perpustakaan UNIDA Gontor</span>
            </p>
        </td>
    </tr>
</table>
@endsection
