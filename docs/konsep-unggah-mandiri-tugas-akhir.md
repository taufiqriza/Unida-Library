# Konsep Arsitektur: Unggah Mandiri Tugas Akhir

## Overview
Fitur ini memungkinkan mahasiswa mengunggah tugas akhir (skripsi/tesis/disertasi) secara mandiri melalui OPAC, yang kemudian akan diverifikasi oleh pustakawan sebelum dipublikasikan ke koleksi E-Thesis.

## Alur Kerja (Workflow)

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Mahasiswa     │────▶│   Submission    │────▶│   Pustakawan    │────▶│    E-Thesis     │
│   Upload Form   │     │   (Pending)     │     │   Review        │     │   (Published)   │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
                                │                       │
                                │                       ▼
                                │               ┌─────────────────┐
                                │               │   Feedback &    │
                                │◀──────────────│   Revision      │
                                │               └─────────────────┘
```

## Status Flow

```
DRAFT ──▶ SUBMITTED ──▶ UNDER_REVIEW ──┬──▶ APPROVED ──▶ PUBLISHED
                                       │
                                       ├──▶ REVISION_REQUIRED ──▶ SUBMITTED
                                       │
                                       └──▶ REJECTED
```

## Jenis Tugas Akhir (ThesisType Enum)

| Value | Label | Degree | Icon |
|-------|-------|--------|------|
| skripsi | Skripsi | S1 | fa-graduation-cap |
| tesis | Tesis | S2 | fa-user-graduate |
| disertasi | Disertasi | S3 | fa-award |

## File yang Diunggah

| File | Status | Format | Max Size | Visibilitas Default |
|------|--------|--------|----------|---------------------|
| Cover | Wajib | JPG/PNG | 2MB | Publik |
| Lembar Pengesahan | Wajib | PDF | 5MB | Restricted |
| BAB 1-3 (Preview) | Wajib | PDF | 20MB | Publik |
| Full Text | Opsional | PDF | 50MB | Configurable |

### Alasan Pemisahan File:
- **BAB 1-3**: Standar perpustakaan PT Indonesia, melindungi hak cipta penulis
- **Full Text**: Akses dapat dikontrol oleh admin berdasarkan kebijakan
- **Lembar Pengesahan**: Biasanya hanya untuk internal/verifikasi

## Database Schema

### Tabel: `thesis_submissions`
```php
Schema::create('thesis_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ethesis_id')->nullable()->constrained()->nullOnDelete();
    
    // Informasi Tugas Akhir
    $table->string('type')->default('skripsi'); // skripsi, tesis, disertasi
    $table->string('title');
    $table->string('title_en')->nullable();
    $table->text('abstract');
    $table->text('abstract_en')->nullable();
    $table->string('keywords')->nullable();
    
    // Penulis
    $table->string('author');
    $table->string('nim');
    
    // Pembimbing & Penguji
    $table->string('advisor1');
    $table->string('advisor2')->nullable();
    $table->string('examiner1')->nullable();
    $table->string('examiner2')->nullable();
    $table->string('examiner3')->nullable();
    
    // Tanggal
    $table->year('year');
    $table->date('defense_date')->nullable();
    
    // Files
    $table->string('cover_file')->nullable();
    $table->string('approval_file')->nullable();
    $table->string('preview_file')->nullable();      // BAB 1-3
    $table->string('fulltext_file')->nullable();
    
    // Status
    $table->string('status')->default('draft');
    
    // Review
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('reviewed_at')->nullable();
    $table->text('review_notes')->nullable();
    $table->text('rejection_reason')->nullable();
    
    // Visibility Settings (Admin controlled)
    $table->boolean('cover_visible')->default(true);
    $table->boolean('approval_visible')->default(false);
    $table->boolean('preview_visible')->default(true);
    $table->boolean('fulltext_visible')->default(false);
    $table->boolean('allow_fulltext_public')->default(false); // User request
    
    $table->timestamps();
});
```

## Keamanan File

### Access Control
File diakses melalui controller dengan pengecekan akses:

```php
// Route
Route::get('/thesis-file/{submission}/{type}', [ThesisFileController::class, 'show'])->name('thesis.file');

// Access Rules
- Admin/Pustakawan: Selalu bisa akses semua file
- Owner (Member): Selalu bisa akses file sendiri
- Publik: Hanya setelah published + visibility = true
```

### Visibility Matrix

| File | Owner | Admin | Publik (Published) |
|------|-------|-------|-------------------|
| Cover | ✅ | ✅ | Jika cover_visible |
| Pengesahan | ✅ | ✅ | Jika approval_visible |
| BAB 1-3 | ✅ | ✅ | Jika preview_visible |
| Full Text | ✅ | ✅ | Jika fulltext_visible OR allow_fulltext_public |

## Komponen yang Diimplementasi

### 1. Backend (Laravel)
- [x] Migration: `create_thesis_submissions_table`
- [x] Model: `ThesisSubmission` dengan visibility settings
- [x] Model: `ThesisSubmissionLog`
- [x] Enum: `ThesisType` (skripsi, tesis, disertasi)
- [x] Controller: `ThesisFileController` (secure file access)
- [x] Livewire: `ThesisSubmissionForm` (Multi-step form)
- [x] Livewire: `MySubmissions` (List submission member)

### 2. Filament Admin
- [x] Resource: `ThesisSubmissionResource`
- [x] Page: `ReviewThesisSubmission` (Custom review page)
- [x] Tab: Pengaturan Akses (visibility controls)
- [x] Actions: Approve, Reject, Request Revision, Publish
- [x] Tabs: Filter by status

### 3. OPAC Frontend
- [x] Form unggah (5-step wizard)
- [x] Daftar submission dengan status detail
- [x] Review notes & rejection reason display
- [x] File status indicators
- [x] Compact type selector (S1/S2/S3 toggle)

## Multi-Step Form Structure

### Step 1: Informasi Dasar
- Jenis (Toggle: S1/S2/S3)
- Judul (ID & EN)
- Abstrak (ID & EN)
- Kata Kunci
- Tahun & Tanggal Sidang

### Step 2: Data Penulis
- Nama Lengkap (auto-fill, readonly)
- NIM (auto-fill, readonly)
- Fakultas → Program Studi (cascading select)

### Step 3: Pembimbing & Penguji
- Pembimbing 1 (required)
- Pembimbing 2 (optional)
- Penguji 1-3 (optional)

### Step 4: Upload File
- Cover (required, image, 2MB)
- Lembar Pengesahan (required, PDF, 5MB)
- BAB 1-3 (required, PDF, 20MB) - akan publik
- Full Text (optional, PDF, 50MB)
- Checkbox: Izinkan akses publik full-text

### Step 5: Review & Submit
- Summary semua data
- File checklist
- Pernyataan keaslian (checkbox)
- Info "Apa yang terjadi selanjutnya"

## Filament Review Interface

### List View
- Tabs by status (Semua, Menunggu, Revisi, Disetujui, Published, Ditolak)
- Cover thumbnail
- Type badge (S1/S2/S3)
- File completeness indicator
- Quick actions

### Review Page
- Collapsible sections untuk info detail
- File preview/download links
- Timeline riwayat aktivitas
- Action buttons dengan form:
  - Approve: Set visibility + notes
  - Revision: Required notes
  - Reject: Required reason
  - Publish: Confirmation

## User Experience Improvements

### Form
- Progress bar dengan completion percentage
- Step indicators dengan checkmark untuk completed
- Compact type selector (toggle button style)
- Real-time character count
- File upload dengan preview
- Clear validation messages

### My Submissions
- Status legend
- Detailed status cards dengan:
  - Review notes (untuk revision_required)
  - Rejection reason (untuk rejected)
  - Approval message (untuk approved)
  - Waiting indicator (untuk submitted/under_review)
- File status icons
- Timeline-style feedback

## Routes

```php
// Member Area
Route::get('/member/submissions', ...)->name('opac.member.submissions');
Route::get('/member/submit-thesis', ...)->name('opac.member.submit-thesis');
Route::get('/member/submit-thesis/{id}', ...)->name('opac.member.edit-submission');

// File Access
Route::get('/thesis-file/{submission}/{type}', ...)->name('thesis.file');
Route::get('/thesis-file/{submission}/{type}/download', ...)->name('thesis.file.download');
```

## Catatan Implementasi

1. **Storage**: File disimpan di `public` disk untuk kemudahan akses. Untuk keamanan lebih tinggi, bisa dipindah ke `local` disk dengan signed URLs.

2. **Visibility**: Admin dapat mengatur visibility per-file saat approve. Default: cover & preview publik, approval & fulltext restricted.

3. **User Request**: User bisa request akses publik untuk full text via checkbox. Admin bisa override saat approve.

4. **Migration**: Jalankan `php artisan migrate:fresh` atau buat migration baru untuk menambah kolom visibility jika tabel sudah ada.
