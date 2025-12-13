# 🔍 Prompt untuk Audit & Testing Mendalam

Gunakan prompt ini untuk AI lain melakukan analisis mendalam terhadap sistem.

---

## PROMPT 1: Security Audit

```
Kamu adalah Security Auditor expert untuk aplikasi Laravel. Lakukan audit keamanan mendalam pada sistem perpustakaan ini.

## Fokus Audit:

### 1. Authentication & Authorization
- Periksa semua guard (web, member) apakah sudah benar implementasinya
- Cek Gate dan Policy apakah ada bypass
- Audit session management dan token handling
- Periksa OAuth flow (Google login) untuk vulnerability

### 2. Input Validation & Sanitization
- Cari semua input user yang tidak di-validate
- Periksa SQL injection vulnerability (terutama di search, filter)
- Cek XSS vulnerability di semua output
- Audit file upload untuk malicious file

### 3. Access Control
- Periksa branch-based access control apakah bisa di-bypass
- Cek apakah user bisa akses data branch lain
- Audit thesis file access control
- Periksa API endpoint authorization

### 4. Sensitive Data
- Cari hardcoded credentials atau API keys
- Periksa apakah password di-hash dengan benar
- Audit logging - apakah ada sensitive data yang ter-log
- Cek .env exposure

### 5. Rate Limiting
- Periksa semua endpoint yang perlu rate limit
- Audit brute force protection di login
- Cek OTP rate limiting

## File Penting untuk Diperiksa:
- app/Http/Controllers/MemberAuthController.php
- app/Http/Controllers/Auth/StaffRegisterController.php
- app/Services/OtpService.php
- app/Livewire/Staff/Control/StaffControl.php
- routes/web.php
- routes/staff.php
- routes/api.php
- app/Providers/AppServiceProvider.php

## Output yang Diharapkan:
1. Daftar vulnerability dengan severity (Critical/High/Medium/Low)
2. Lokasi file dan line number
3. Proof of concept atau cara exploit
4. Rekomendasi perbaikan dengan code example
```

---

## PROMPT 2: Code Quality & Architecture Review

```
Kamu adalah Senior Laravel Architect. Review arsitektur dan kualitas kode sistem perpustakaan ini.

## Fokus Review:

### 1. Architecture
- Apakah separation of concerns sudah benar?
- Cek apakah ada business logic di Controller yang seharusnya di Service
- Review Livewire component - apakah terlalu besar/kompleks?
- Periksa Model relationships dan eager loading

### 2. Performance
- Cari N+1 query problems
- Periksa query yang tidak optimal
- Audit caching strategy
- Cek apakah ada memory leak potential

### 3. Code Duplication
- Cari kode yang duplikat dan bisa di-refactor
- Periksa apakah ada trait yang bisa dibuat
- Review helper functions

### 4. Error Handling
- Apakah semua exception di-handle dengan benar?
- Cek try-catch yang terlalu broad
- Periksa error messages - apakah expose internal info?

### 5. Database
- Review migration files untuk consistency
- Cek index yang kurang
- Audit foreign key constraints
- Periksa soft delete implementation

## File Penting:
- Semua file di app/Livewire/Staff/
- app/Models/*.php
- database/migrations/*.php
- app/Services/*.php

## Output:
1. Code smells dengan lokasi
2. Refactoring suggestions
3. Performance improvement recommendations
4. Architecture improvement suggestions
```

---

## PROMPT 3: Feature Testing Checklist

```
Kamu adalah QA Engineer. Buat test case dan lakukan testing manual untuk fitur-fitur berikut.

## Fitur yang Perlu Ditest:

### 1. Member Registration Flow
Test Cases:
- [ ] Register dengan email @unida.gontor.ac.id → harus auto-verified, langsung login
- [ ] Register dengan email @gmail.com → harus kirim OTP, redirect ke verify page
- [ ] Register dengan email @ugm.ac.id → harus kirim OTP, institution auto-detect "UGM"
- [ ] Input OTP salah 3x → harus block dan minta kirim ulang
- [ ] OTP expired (>15 menit) → harus minta kirim ulang
- [ ] Resend OTP sebelum 1 menit → harus ditolak dengan countdown
- [ ] Register dengan email yang sudah ada → harus error "email sudah terdaftar"
- [ ] Login dengan member yang belum verified → harus redirect ke verify page

### 2. Staff Registration & Approval
Test Cases:
- [ ] Staff register dengan pilih branch → status harus "pending"
- [ ] Staff pending coba login → harus ditolak dengan pesan "menunggu persetujuan"
- [ ] Admin approve staff → status jadi "approved", is_active = true
- [ ] Admin reject staff dengan alasan → status jadi "rejected"
- [ ] Staff rejected coba login → harus ditolak dengan pesan "ditolak"
- [ ] Admin cabang A tidak bisa lihat pending staff cabang B (kecuali super_admin)

### 3. Staff Chat
Test Cases:
- [ ] Kirim pesan ke user lain → pesan muncul di kedua sisi
- [ ] Upload gambar → preview muncul, bisa di-klik untuk fullscreen
- [ ] Upload file → link download muncul
- [ ] Kirim link URL → harus clickable
- [ ] Online status update saat user aktif
- [ ] Read receipt (centang biru) saat pesan dibaca
- [ ] Polling setiap 3 detik untuk pesan baru

### 4. Email Configuration
Test Cases:
- [ ] Simpan config SMTP di App Settings → config tersimpan di database
- [ ] Test email button → email terkirim ke alamat pengirim
- [ ] Config dari database override config .env
- [ ] Invalid SMTP credentials → error message yang jelas

### 5. E-Library Dashboard
Test Cases:
- [ ] Tab E-Book menampilkan list dengan pagination
- [ ] Tab E-Thesis menampilkan list dengan pagination
- [ ] Tab Submissions menampilkan thesis submissions
- [ ] Filter by status bekerja
- [ ] Search bekerja
- [ ] Approve/Reject submission bekerja
- [ ] Publish to E-Thesis bekerja

### 6. Staff Profile
Test Cases:
- [ ] Upload photo → photo tersimpan dan ditampilkan
- [ ] Edit nama → tersimpan
- [ ] Edit email → tersimpan (jika tidak duplikat)
- [ ] Ganti password → password baru bisa digunakan untuk login

### 7. Wire:Navigate (SPA)
Test Cases:
- [ ] Klik menu sidebar → halaman berganti tanpa full reload
- [ ] Chart.js di dashboard tetap render setelah navigate
- [ ] Browser back/forward button bekerja
- [ ] URL berubah sesuai halaman

## Output:
1. Test result untuk setiap case (Pass/Fail)
2. Screenshot atau evidence untuk failures
3. Steps to reproduce untuk bugs
4. Severity assessment untuk setiap bug
```

---

## PROMPT 4: Database & Migration Audit

```
Kamu adalah Database Administrator. Audit struktur database dan migration files.

## Tasks:

### 1. Migration Consistency
- Periksa semua migration files apakah bisa di-rollback
- Cek apakah ada migration yang conflict
- Audit foreign key constraints
- Periksa index yang diperlukan

### 2. Schema Review
Periksa tabel-tabel berikut:
- members (registration_type, email_verified, institution fields)
- users (status, approved_by, approval fields)
- email_verifications (OTP storage)
- staff_messages (chat messages)

### 3. Data Integrity
- Cek apakah ada orphan records potential
- Periksa cascade delete behavior
- Audit unique constraints

### 4. Performance
- Identifikasi query yang butuh index
- Cek composite index yang diperlukan
- Review enum vs lookup table decisions

## Files:
- database/migrations/*.php
- app/Models/*.php

## Output:
1. Schema diagram atau ERD
2. Missing indexes list
3. Migration issues
4. Optimization recommendations
```

---

## PROMPT 5: UI/UX Review

```
Kamu adalah UI/UX Designer. Review interface dan user experience.

## Fokus Review:

### 1. Consistency
- Warna dan styling konsisten di semua halaman?
- Button styles konsisten?
- Spacing dan typography konsisten?

### 2. Accessibility
- Contrast ratio cukup?
- Form labels ada?
- Error messages jelas?
- Keyboard navigation bekerja?

### 3. Responsive Design
- Mobile view bekerja?
- Tablet view bekerja?
- Sidebar collapse di mobile?

### 4. User Flow
- Registration flow intuitif?
- Error handling user-friendly?
- Loading states ada?
- Success feedback jelas?

### 5. Specific Pages
- /register - Member/Staff switcher
- /verify-email - OTP input
- /staff/control - Approval interface
- /staff/profile - Profile edit
- Staff chat widget

## Output:
1. UI inconsistencies list
2. UX improvement suggestions
3. Accessibility issues
4. Mobile responsiveness issues
```

---

## PROMPT 6: Full System Integration Test

```
Kamu adalah Integration Tester. Test end-to-end flow sistem.

## Scenario 1: New Member Journey
1. User buka /register
2. Pilih tab Member
3. Isi form dengan email gmail
4. Submit → redirect ke /verify-email
5. Cek email untuk OTP
6. Input OTP
7. Verify → redirect ke member dashboard
8. Logout
9. Login kembali → berhasil masuk dashboard

## Scenario 2: New Staff Journey
1. User buka /register
2. Pilih tab Staff
3. Isi form, pilih branch
4. Submit → redirect ke login dengan pesan sukses
5. Coba login → ditolak (pending)
6. Admin login ke /staff/control
7. Admin approve staff
8. Staff login → berhasil masuk staff portal

## Scenario 3: Staff Chat
1. Staff A login
2. Buka chat widget
3. Pilih Staff B dari contacts
4. Kirim pesan
5. Staff B login
6. Buka chat → pesan dari A muncul
7. Staff B reply
8. Staff A lihat reply (via polling)

## Scenario 4: E-Library Workflow
1. Member submit thesis
2. Staff login ke E-Library
3. Review submission
4. Approve submission
5. Publish ke E-Thesis
6. Cek di OPAC → thesis muncul

## Output:
1. Pass/Fail untuk setiap scenario
2. Detailed steps yang gagal
3. Error messages atau screenshots
4. Performance observations
```

---

## Cara Menggunakan

1. Copy salah satu prompt di atas
2. Paste ke AI lain (Claude, GPT, dll)
3. Berikan akses ke codebase atau paste file yang relevan
4. Minta AI melakukan analisis sesuai prompt
5. Compile hasil dari semua prompt menjadi report

## Files yang Perlu Diberikan ke AI

Untuk audit lengkap, berikan file-file berikut:

```
# Core Auth
app/Http/Controllers/MemberAuthController.php
app/Http/Controllers/Auth/StaffRegisterController.php
app/Services/OtpService.php

# Staff Portal
app/Livewire/Staff/Control/StaffControl.php
app/Livewire/Staff/Chat/StaffChat.php
app/Livewire/Staff/Profile/StaffProfile.php
app/Livewire/Staff/Elibrary/ElibraryDashboard.php

# Models
app/Models/Member.php
app/Models/User.php
app/Models/EmailVerification.php
app/Models/StaffMessage.php

# Routes
routes/web.php
routes/staff.php

# Views
resources/views/opac/register.blade.php
resources/views/opac/verify-email.blade.php
resources/views/livewire/staff/control/staff-control.blade.php

# Config
app/Providers/AppServiceProvider.php
app/Filament/Pages/AppSettings.php

# Migrations
database/migrations/2025_12_13_*.php
```
