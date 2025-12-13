# Email Configuration

## Overview

Sistem email digunakan untuk:
- Verifikasi OTP saat registrasi member
- Notifikasi approval staff
- Notifikasi publikasi E-Library (opsional)

## Konfigurasi via Admin Panel

### Akses
1. Login ke Filament Admin (`/admin`)
2. Buka **App Settings** → Tab **Email**

### Parameter SMTP

| Field | Deskripsi | Contoh |
|-------|-----------|--------|
| Mailer | Metode pengiriman | `smtp` |
| SMTP Host | Server SMTP | `smtp.gmail.com` |
| Port | Port SMTP | `587` (TLS) atau `465` (SSL) |
| Encryption | Jenis enkripsi | `tls` atau `ssl` |
| Username | Email untuk login SMTP | `library@unida.gontor.ac.id` |
| Password | Password atau App Password | `xxxx xxxx xxxx xxxx` |
| Email Pengirim | Alamat yang tampil | `library@unida.gontor.ac.id` |
| Nama Pengirim | Nama yang tampil | `UNIDA Library` |

## Setup Google Workspace

Jika email UNIDA menggunakan Google Workspace:

### 1. Aktifkan 2-Step Verification
- Buka https://myaccount.google.com
- Security → 2-Step Verification → Turn On

### 2. Generate App Password
- Security → 2-Step Verification → App passwords
- Select app: **Mail**
- Select device: **Other** (masukkan "Library Portal")
- Copy 16 digit password yang muncul

### 3. Konfigurasi di App Settings
```
SMTP Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: library@unida.gontor.ac.id
Password: [16 digit App Password]
```

## Konfigurasi via .env (Alternatif)

Jika tidak ingin menyimpan di database:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=library@unida.gontor.ac.id
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=library@unida.gontor.ac.id
MAIL_FROM_NAME="UNIDA Library"
```

> **Note:** Konfigurasi di database (App Settings) akan override konfigurasi .env

## Menghindari Spam

### 1. Gunakan Domain Sendiri
Email dari `@unida.gontor.ac.id` lebih dipercaya daripada `@gmail.com`

### 2. Konfigurasi DNS Records

**SPF Record:**
```
v=spf1 include:_spf.google.com ~all
```

**DKIM:** Aktifkan di Google Admin Console

**DMARC:**
```
v=DMARC1; p=quarantine; rua=mailto:admin@unida.gontor.ac.id
```

### 3. Best Practices
- Jangan kirim email massal dalam waktu singkat
- Gunakan subject yang jelas dan tidak clickbait
- Sertakan unsubscribe link untuk newsletter

## Testing

### Via Admin Panel
1. Buka App Settings → Tab Email
2. Isi semua konfigurasi
3. Klik **Simpan**
4. Klik **Kirim Test Email**
5. Cek inbox email pengirim

### Via Artisan
```bash
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
```

## Troubleshooting

### Error: Connection refused
- Pastikan port tidak diblokir firewall
- Coba port 465 dengan SSL

### Error: Authentication failed
- Pastikan menggunakan App Password, bukan password biasa
- Pastikan 2-Step Verification aktif

### Email masuk Spam
- Periksa SPF, DKIM, DMARC
- Gunakan email domain sendiri
- Hindari kata-kata spam di subject

## Files Terkait

| File | Fungsi |
|------|--------|
| `app/Filament/Pages/AppSettings.php` | UI konfigurasi email |
| `app/Providers/AppServiceProvider.php` | Load config dari database |
| `app/Services/OtpService.php` | Kirim OTP email |
| `resources/views/emails/otp.blade.php` | Template email OTP |
