# Member Registration & Email Verification

## Overview

Sistem registrasi member dengan verifikasi email berbasis domain untuk membedakan member internal UNIDA, external (kampus lain), dan public.

**Dokumentasi terkait:**
- [Email Configuration](./email-configuration.md) - Setup SMTP untuk kirim OTP

## Registration Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         MEMBER REGISTRATION FLOW                             │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌──────────────────┐
                              │  User Register   │
                              └────────┬─────────┘
                                       │
                                       ▼
                              ┌──────────────────┐
                              │  Detect Email    │
                              │  Domain          │
                              └────────┬─────────┘
                                       │
              ┌────────────────────────┼────────────────────────┐
              │                        │                        │
              ▼                        ▼                        ▼
   ┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
   │ TRUSTED DOMAIN      │  │ ACADEMIC DOMAIN     │  │ PUBLIC EMAIL        │
   │ (UNIDA)             │  │ (.ac.id lain)       │  │ (gmail, yahoo, dll) │
   └──────────┬──────────┘  └──────────┬──────────┘  └──────────┬──────────┘
              │                        │                        │
              ▼                        ▼                        ▼
   ┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
   │ ✅ AUTO VERIFIED    │  │ 📧 OTP EMAIL        │  │ 📧 OTP EMAIL        │
   │                     │  │ + Auto Institution  │  │ + Manual Institution│
   │ registration_type:  │  │                     │  │   (opsional)        │
   │ internal            │  │ registration_type:  │  │                     │
   │                     │  │ external            │  │ registration_type:  │
   │ → Direct Login      │  │                     │  │ public              │
   └─────────────────────┘  │ → Verify OTP        │  │                     │
                            └─────────────────────┘  │ → Verify OTP        │
                                                     └─────────────────────┘
```

## Member Types

| Type | Domain | Verification | Institution |
|------|--------|--------------|-------------|
| `internal` | `@*.unida.gontor.ac.id` | ❌ Auto verified | UNIDA Gontor |
| `external` | `@*.ac.id` (non-UNIDA) | ✅ OTP Email | Auto-detect dari domain |
| `public` | `@gmail.com`, `@yahoo.com`, dll | ✅ OTP Email | Manual input (opsional) |

## Trusted Domains

Daftar domain yang trusted (auto-verified) ada di file:
```
docs/email.md
```

Contoh:
```
@gontor.ac.id
@unida.gontor.ac.id
@mhs.unida.gontor.ac.id
@student.unida.gontor.ac.id
@staff.unida.gontor.ac.id
...
```

## Database Schema

### Members Table (new columns)

| Column | Type | Description |
|--------|------|-------------|
| `registration_type` | enum | `internal`, `external`, `public` |
| `institution` | string | Nama institusi (untuk external/public) |
| `institution_city` | string | Kota institusi |
| `email_verified` | enum | `pending`, `verified` |
| `email_verified_at` | timestamp | Waktu verifikasi |

### Email Verifications Table

| Column | Type | Description |
|--------|------|-------------|
| `email` | string | Email yang diverifikasi |
| `otp` | string(6) | 6 digit OTP code |
| `attempts` | integer | Jumlah percobaan (max 3) |
| `expires_at` | timestamp | Waktu kadaluarsa (15 menit) |

## OTP Verification

### Security Features

1. **Rate Limiting**
   - Max 10 verify attempts per minute
   - Max 5 resend OTP per minute

2. **OTP Rules**
   - 6 digit numeric code
   - Expires in 15 minutes
   - Max 3 wrong attempts → must request new OTP
   - Cooldown 1 minute between resend

3. **Auto-detection**
   - Email domain checked against trusted list
   - Academic domain (.ac.id) auto-extracts institution name

## Files

### Controllers
- `app/Http/Controllers/MemberAuthController.php` - Main auth controller

### Services
- `app/Services/OtpService.php` - OTP generation, verification, domain detection

### Models
- `app/Models/Member.php` - Updated with new fields
- `app/Models/EmailVerification.php` - OTP storage

### Views
- `resources/views/opac/register.blade.php` - Registration form with email detection
- `resources/views/opac/verify-email.blade.php` - OTP verification page
- `resources/views/emails/otp.blade.php` - OTP email template

### Routes
```php
// Registration
Route::match(['get', 'post'], '/register', [MemberAuthController::class, 'register']);

// Email Verification
Route::match(['get', 'post'], '/verify-email', [MemberAuthController::class, 'verifyEmail']);
Route::post('/resend-otp', [MemberAuthController::class, 'resendOtp']);
```

## Usage Examples

### Check if email is trusted
```php
$otpService = new OtpService();
$isTrusted = $otpService->isTrustedDomain('user@mhs.unida.gontor.ac.id'); // true
$isTrusted = $otpService->isTrustedDomain('user@gmail.com'); // false
```

### Detect registration type
```php
$type = $otpService->detectRegistrationType('user@unida.gontor.ac.id'); // 'internal'
$type = $otpService->detectRegistrationType('user@mhs.ugm.ac.id'); // 'external'
$type = $otpService->detectRegistrationType('user@gmail.com'); // 'public'
```

### Extract institution from academic email
```php
$inst = $otpService->extractInstitution('user@mhs.ugm.ac.id'); // 'UGM'
$inst = $otpService->extractInstitution('user@student.its.ac.id'); // 'ITS'
```

## Adding New Trusted Domains

Edit file `docs/email.md` dan tambahkan domain baru:
```
@newdomain.unida.gontor.ac.id
```

Tidak perlu restart server, perubahan langsung aktif.
