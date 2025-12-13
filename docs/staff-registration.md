# Staff Registration & Approval System

## Overview

Sistem registrasi staff perpustakaan dengan approval workflow. Staff baru harus disetujui admin sebelum dapat login.

## Registration Flow

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Staff Daftar   │────▶│  Status:        │────▶│  Admin Review   │────▶│  Staff Login    │
│  via /register  │     │  PENDING        │     │  di Control     │     │  ke Portal      │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
                                                        │
                                                        ▼
                                                ┌─────────────────┐
                                                │  REJECTED       │
                                                │  (dengan alasan)│
                                                └─────────────────┘
```

## Status Staff

| Status | is_active | Dapat Login | Keterangan |
|--------|-----------|-------------|------------|
| `pending` | false | ❌ | Menunggu approval |
| `approved` | true | ✅ | Sudah disetujui |
| `rejected` | false | ❌ | Ditolak dengan alasan |

## Database Schema

### Kolom di tabel `users`

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `status` | enum | pending, approved, rejected |
| `approved_by` | foreignId | User yang approve/reject |
| `approved_at` | timestamp | Waktu approval |
| `rejection_reason` | text | Alasan penolakan |

## Cara Kerja

### 1. Staff Mendaftar
- Buka `/register` → Pilih tab **Staff**
- Isi nama, email, pilih cabang, password
- Submit → Status: `pending`, is_active: `false`
- Redirect ke login dengan pesan sukses

### 2. Admin Mereview
- Login sebagai Admin/Super Admin
- Buka menu **Control** di sidebar
- Lihat daftar staff pending
- Klik **Detail** untuk review
- Pilih **Setujui** atau **Tolak** (dengan alasan)

### 3. Staff Login
- Jika approved: bisa login ke staff portal
- Jika pending: muncul pesan "menunggu persetujuan"
- Jika rejected: muncul pesan "ditolak, hubungi admin"

## Access Control

### Siapa yang bisa approve?
- `super_admin` - Bisa approve semua cabang
- `admin` - Hanya bisa approve staff di cabangnya sendiri

### Gate Permission
```php
Gate::define('manage-staff', function ($user) {
    return in_array($user->role, ['super_admin', 'admin']);
});
```

## Files

| File | Fungsi |
|------|--------|
| `app/Http/Controllers/Auth/StaffRegisterController.php` | Handle registrasi |
| `app/Livewire/Staff/Control/StaffControl.php` | UI approval |
| `resources/views/livewire/staff/control/staff-control.blade.php` | View approval |
| `resources/views/opac/register.blade.php` | Form registrasi |

## Routes

```php
// Registrasi staff
Route::post('/register/staff', [StaffRegisterController::class, 'register'])
    ->name('opac.register.staff');

// Control panel (admin only)
Route::prefix('control')->middleware('can:manage-staff')->group(function () {
    Route::get('/', StaffControl::class)->name('staff.control.index');
});
```

## UI Components

### Register Page
- Tab switcher: Member / Staff
- Form staff dengan dropdown cabang
- Notice: "Perlu persetujuan admin"

### Control Panel
- Stats: Pending | Approved | Rejected
- List staff dengan filter status
- Modal detail dengan tombol Approve/Reject
- Input alasan penolakan (wajib jika reject)

## Notifikasi (TODO)

Fitur yang bisa ditambahkan:
- [ ] Email ke admin saat ada staff pending
- [ ] Email ke staff saat approved/rejected
- [ ] Push notification di dashboard admin
