# Dokumentasi Mobile Apps - Perpustakaan UNIDA Gontor

## Daftar Isi
1. [Executive Summary](#1-executive-summary)
2. [Analisis Sistem Existing](#2-analisis-sistem-existing)
3. [Rekomendasi Aplikasi Mobile](#3-rekomendasi-aplikasi-mobile)
4. [Spesifikasi Fitur](#4-spesifikasi-fitur)
5. [API Specification](#5-api-specification)
6. [Database Schema](#6-database-schema)
7. [Technical Architecture](#7-technical-architecture)
8. [Timeline & Roadmap](#8-timeline--roadmap)

---

## 1. Executive Summary

### 1.1 Latar Belakang
Perpustakaan UNIDA Gontor telah memiliki sistem manajemen perpustakaan berbasis web yang komprehensif dengan fitur:
- Katalog buku fisik (OPAC)
- E-Library (E-Book, E-Thesis, Jurnal)
- Sirkulasi peminjaman
- Cek plagiasi dokumen
- Surat bebas pustaka digital
- Multi-cabang (Pusat + Cabang)

### 1.2 Tujuan Mobile Apps
- Meningkatkan aksesibilitas layanan perpustakaan
- Memudahkan mahasiswa mengakses koleksi digital
- Notifikasi real-time (jatuh tempo, reservasi)
- Kartu anggota digital dengan QR code
- Efisiensi operasional staff

### 1.3 Rekomendasi
Berdasarkan analisis, direkomendasikan membuat **2 aplikasi terpisah**:
1. **UNIDA Library** - Untuk Member (Mahasiswa/Dosen)
2. **UNIDA Library Staff** - Untuk Pustakawan (Opsional, Fase 2)

---

## 2. Analisis Sistem Existing

### 2.1 Arsitektur Saat Ini
```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Web)                           │
├─────────────────┬─────────────────┬─────────────────────────┤
│   OPAC Portal   │  Staff Portal   │    Filament Admin       │
│   (Livewire)    │   (Livewire)    │      (PHP)              │
└────────┬────────┴────────┬────────┴───────────┬─────────────┘
         │                 │                    │
         └─────────────────┼────────────────────┘
                           │
              ┌────────────▼────────────┐
              │   Laravel Backend       │
              │   - API Controllers     │
              │   - Livewire Components │
              │   - Services            │
              └────────────┬────────────┘
                           │
              ┌────────────▼────────────┐
              │      MySQL Database     │
              └─────────────────────────┘
```

### 2.2 Model Data Utama

| Model | Deskripsi | Relasi Utama |
|-------|-----------|--------------|
| Member | Anggota perpustakaan | hasMany: Loans, Fines, ThesisSubmissions |
| Book | Data bibliografi buku | hasMany: Items, belongsToMany: Authors, Subjects |
| Item | Eksemplar fisik buku | belongsTo: Book, hasMany: Loans |
| Loan | Transaksi peminjaman | belongsTo: Member, Item |
| Fine | Denda keterlambatan | belongsTo: Loan, Member |
| Ebook | Koleksi e-book | belongsTo: Branch, Publisher |
| Ethesis | Karya ilmiah digital | belongsTo: Department |
| ThesisSubmission | Pengajuan unggah mandiri | belongsTo: Member, Department |
| PlagiarismCheck | Cek plagiasi | belongsTo: Member |
| ClearanceLetter | Surat bebas pustaka | belongsTo: Member |
| Branch | Cabang perpustakaan | hasMany: Books, Members, Loans |

### 2.3 API Existing
Sistem sudah memiliki API dasar di `/api/`:

| Endpoint | Method | Auth | Deskripsi |
|----------|--------|------|-----------|
| `/` | GET | No | Dashboard stats |
| `/branches` | GET | No | Daftar cabang |
| `/catalog` | GET | No | Katalog buku |
| `/catalog/{id}` | GET | No | Detail buku |
| `/ebooks` | GET | No | Daftar e-book |
| `/etheses` | GET | No | Daftar e-thesis |
| `/login` | POST | No | Login member |
| `/logout` | POST | Yes | Logout |
| `/me` | GET | Yes | Profil member |
| `/my/loans` | GET | Yes | Peminjaman aktif |
| `/my/loans/history` | GET | Yes | Riwayat peminjaman |
| `/my/fines` | GET | Yes | Denda |

---

## 3. Rekomendasi Aplikasi Mobile

### 3.1 Aplikasi Member (Prioritas Utama)

**Target User:** Mahasiswa, Dosen, Peneliti

**Justifikasi:**
- Volume user terbesar (ribuan mahasiswa)
- Kebutuhan akses mobile tinggi
- Fitur yang dibutuhkan relatif sederhana
- ROI tertinggi

### 3.2 Aplikasi Staff (Opsional - Fase 2)

**Target User:** Pustakawan, Admin

**Justifikasi:**
- Volume user kecil (<50 orang)
- Sebagian besar kerja di meja dengan komputer
- Web responsive sudah cukup untuk sebagian tugas
- Hanya perlu untuk fitur mobile-specific (scan barcode di rak)

### 3.3 Teknologi yang Direkomendasikan

| Aspek | Rekomendasi | Alasan |
|-------|-------------|--------|
| Framework | **Flutter** | Cross-platform, performa native, UI konsisten |
| State Management | Riverpod / BLoC | Scalable, testable |
| HTTP Client | Dio | Interceptors, retry logic |
| Local Storage | Hive / SQLite | Offline capability |
| Push Notification | Firebase Cloud Messaging | Gratis, reliable |
| Authentication | JWT + Secure Storage | Standard, aman |

---

## 4. Spesifikasi Fitur

### 4.1 Aplikasi Member - UNIDA Library

#### Fase 1: MVP (8-10 minggu)

| Modul | Fitur | Prioritas |
|-------|-------|-----------|
| **Auth** | Login dengan NIM/Email | P0 |
| | Logout | P0 |
| | Lupa password | P1 |
| **Home** | Dashboard ringkasan | P0 |
| | Statistik peminjaman | P0 |
| | Pengumuman/berita | P1 |
| **Katalog** | Pencarian buku | P0 |
| | Filter (penulis, subjek, tahun) | P0 |
| | Detail buku + ketersediaan | P0 |
| | Scan ISBN barcode | P1 |
| **Peminjaman** | Daftar pinjaman aktif | P0 |
| | Riwayat peminjaman | P0 |
| | Notifikasi jatuh tempo | P0 |
| **Denda** | Daftar denda | P0 |
| | Total tagihan | P0 |
| **Profil** | Lihat profil | P0 |
| | Kartu anggota digital (QR) | P0 |
| | Edit foto profil | P1 |

#### Fase 2: E-Library (4-6 minggu)

| Modul | Fitur | Prioritas |
|-------|-------|-----------|
| **E-Book** | Browse e-book | P0 |
| | Detail e-book | P0 |
| | Baca online (WebView) | P0 |
| | Integrasi Kubuku | P1 |
| **E-Thesis** | Browse karya ilmiah | P0 |
| | Filter fakultas/prodi | P0 |
| | Detail + abstrak | P0 |
| | Download PDF (jika public) | P1 |

#### Fase 3: Layanan Lanjutan (4-6 minggu)

| Modul | Fitur | Prioritas |
|-------|-------|-----------|
| **Plagiasi** | Upload dokumen | P1 |
| | Riwayat cek plagiasi | P0 |
| | Download sertifikat | P0 |
| **Bebas Pustaka** | Status pengajuan | P0 |
| | Download surat | P0 |
| **Unggah Mandiri** | Form pengajuan thesis | P2 |
| | Upload file | P2 |
| | Tracking status | P1 |
| **Chat** | Chat dengan pustakawan | P2 |

#### Fase 4: Enhancement (Ongoing)

| Modul | Fitur | Prioritas |
|-------|-------|-----------|
| **Offline** | Cache katalog | P2 |
| | Offline reading | P2 |
| **Reservasi** | Reservasi buku | P2 |
| | Notifikasi tersedia | P2 |
| **Bookmark** | Simpan buku favorit | P2 |
| **Review** | Rating & review buku | P3 |

### 4.2 Aplikasi Staff (Fase 2 - Opsional)

| Modul | Fitur | Prioritas |
|-------|-------|-----------|
| **Auth** | Login staff | P0 |
| **Sirkulasi** | Scan member card | P0 |
| | Scan barcode buku | P0 |
| | Proses peminjaman | P0 |
| | Proses pengembalian | P0 |
| **Member** | Cari member | P1 |
| | Lihat detail member | P1 |
| **Notifikasi** | Push notification | P1 |
| **Stock Opname** | Scan barcode massal | P2 |

---

## 5. API Specification

### 5.1 API yang Perlu Ditambahkan

Berikut API baru yang perlu dibuat untuk mendukung mobile apps:

#### 5.1.1 Authentication & Profile

```
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/refresh
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
GET    /api/v1/auth/me
PUT    /api/v1/auth/profile
POST   /api/v1/auth/profile/photo
POST   /api/v1/auth/fcm-token          # Register FCM token
DELETE /api/v1/auth/fcm-token          # Remove FCM token
```

#### 5.1.2 Catalog & Books

```
GET    /api/v1/catalog                  # List books (existing, enhance)
GET    /api/v1/catalog/{id}             # Book detail (existing, enhance)
GET    /api/v1/catalog/filters          # Filter options (existing)
GET    /api/v1/catalog/search           # Advanced search
GET    /api/v1/catalog/isbn/{isbn}      # Search by ISBN (for barcode scan)
GET    /api/v1/catalog/popular          # Popular books
GET    /api/v1/catalog/new              # New arrivals
```

#### 5.1.3 Member Loans & Fines

```
GET    /api/v1/loans                    # Active loans (existing as /my/loans)
GET    /api/v1/loans/history            # Loan history (existing)
GET    /api/v1/loans/{id}               # Loan detail
GET    /api/v1/fines                    # Fines list (existing as /my/fines)
GET    /api/v1/fines/summary            # Fines summary
```

#### 5.1.4 E-Library

```
GET    /api/v1/ebooks                   # E-books list (existing, enhance)
GET    /api/v1/ebooks/{id}              # E-book detail (existing)
GET    /api/v1/ebooks/{id}/read         # Get read URL
GET    /api/v1/etheses                  # E-thesis list (existing, enhance)
GET    /api/v1/etheses/{id}             # E-thesis detail (existing)
GET    /api/v1/etheses/{id}/download    # Download PDF (if allowed)
GET    /api/v1/journals                 # Journal articles
GET    /api/v1/journals/{id}            # Journal detail
```

#### 5.1.5 Plagiarism Check

```
GET    /api/v1/plagiarism               # List my checks
POST   /api/v1/plagiarism               # Submit new check
GET    /api/v1/plagiarism/{id}          # Check detail & status
GET    /api/v1/plagiarism/{id}/certificate  # Download certificate
POST   /api/v1/plagiarism/external      # Submit external check result
```

#### 5.1.6 Thesis Submission

```
GET    /api/v1/submissions              # My submissions
POST   /api/v1/submissions              # Create submission
GET    /api/v1/submissions/{id}         # Submission detail
PUT    /api/v1/submissions/{id}         # Update submission
DELETE /api/v1/submissions/{id}         # Cancel submission
POST   /api/v1/submissions/{id}/files   # Upload files
```

#### 5.1.7 Clearance Letter

```
GET    /api/v1/clearance                # My clearance letters
GET    /api/v1/clearance/{id}           # Letter detail
GET    /api/v1/clearance/{id}/download  # Download PDF
GET    /api/v1/clearance/check          # Check eligibility
```

#### 5.1.8 Notifications

```
GET    /api/v1/notifications            # List notifications
PUT    /api/v1/notifications/{id}/read  # Mark as read
PUT    /api/v1/notifications/read-all   # Mark all as read
GET    /api/v1/notifications/unread-count
```

#### 5.1.9 General

```
GET    /api/v1/branches                 # Library branches (existing)
GET    /api/v1/faculties                # Faculties list
GET    /api/v1/departments              # Departments (existing, enhance)
GET    /api/v1/news                     # News/announcements (existing)
GET    /api/v1/news/{slug}              # News detail (existing)
GET    /api/v1/settings                 # App settings (contact, etc)
```

### 5.2 API Response Format

#### Standard Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

#### Paginated Response
```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 195
  }
}
```

#### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

### 5.3 Authentication

#### Login Request
```json
POST /api/v1/auth/login
{
  "member_id": "432022111002",
  "password": "password123",
  "device_name": "iPhone 14 Pro",
  "fcm_token": "optional_fcm_token"
}
```

#### Login Response
```json
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer",
    "expires_at": "2026-02-01T00:00:00Z",
    "member": {
      "id": 1,
      "member_id": "432022111002",
      "name": "ABDILLAH FAHRI MUHAMMAD",
      "email": "abdillah@unida.gontor.ac.id",
      "photo_url": "https://...",
      "member_type": "Mahasiswa S1",
      "faculty": "Tarbiyah",
      "department": "Pendidikan Agama Islam",
      "is_active": true,
      "expire_date": "2026-12-31"
    }
  }
}
```

### 5.4 Detailed API Specifications

#### GET /api/v1/catalog

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| q | string | Search query (title, author, ISBN) |
| author | int | Filter by author ID |
| subject | int | Filter by subject ID |
| publisher | int | Filter by publisher ID |
| year | int | Filter by publication year |
| branch | int | Filter by branch ID |
| available | bool | Only show available items |
| sort | string | Sort: relevance, title, year, popular |
| page | int | Page number |
| per_page | int | Items per page (max 50) |

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "title": "Fiqh Sunnah",
      "authors": ["Sayyid Sabiq"],
      "publisher": "Dar al-Fikr",
      "year": 2020,
      "isbn": "978-xxx",
      "cover_url": "https://...",
      "call_number": "297.4 SAB f",
      "total_items": 5,
      "available_items": 3,
      "branch": "Perpustakaan Pusat"
    }
  ],
  "meta": { ... }
}
```

#### GET /api/v1/catalog/{id}

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "title": "Fiqh Sunnah",
    "authors": [
      {"id": 1, "name": "Sayyid Sabiq"}
    ],
    "publisher": {"id": 1, "name": "Dar al-Fikr"},
    "place": "Beirut",
    "year": 2020,
    "edition": "Cet. 5",
    "isbn": "978-xxx",
    "pages": 450,
    "language": "Arabic",
    "cover_url": "https://...",
    "call_number": "297.4 SAB f",
    "subjects": [
      {"id": 1, "name": "Fiqh Islam"}
    ],
    "abstract": "...",
    "table_of_contents": "...",
    "items": [
      {
        "id": 1,
        "barcode": "001234",
        "location": "Rak A-12",
        "status": "available",
        "branch": "Perpustakaan Pusat"
      }
    ],
    "total_items": 5,
    "available_items": 3
  }
}
```

#### GET /api/v1/loans

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "book": {
        "id": 123,
        "title": "Fiqh Sunnah",
        "authors": ["Sayyid Sabiq"],
        "cover_url": "https://..."
      },
      "item_barcode": "001234",
      "loan_date": "2025-12-20",
      "due_date": "2026-01-03",
      "is_overdue": false,
      "days_remaining": 2,
      "can_renew": true,
      "renew_count": 0,
      "max_renew": 2
    }
  ],
  "summary": {
    "total_active": 3,
    "overdue_count": 0,
    "loan_limit": 5
  }
}
```

#### GET /api/v1/plagiarism

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 34,
      "document_title": "Implementasi Machine Learning...",
      "file_name": "skripsi.pdf",
      "status": "completed",
      "status_label": "Selesai",
      "similarity_score": 18.5,
      "similarity_level": "low",
      "similarity_label": "Rendah (Aman)",
      "is_passed": true,
      "certificate_number": "PLAG-202601-00034",
      "has_certificate": true,
      "provider": "iThenticate",
      "created_at": "2026-01-01T10:00:00Z",
      "completed_at": "2026-01-01T10:15:00Z"
    }
  ]
}
```

#### POST /api/v1/plagiarism

**Request (multipart/form-data):**
```
document_title: "Judul Skripsi"
document: [file]
```

**Response:**
```json
{
  "success": true,
  "message": "Dokumen berhasil diupload dan sedang diproses",
  "data": {
    "id": 35,
    "status": "pending",
    "estimated_time": "5-15 menit"
  }
}
```


---

## 6. Database Schema

### 6.1 Tabel Utama untuk Mobile

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    members      │     │     books       │     │     items       │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id              │     │ id              │     │ id              │
│ branch_id (FK)  │     │ branch_id (FK)  │     │ book_id (FK)    │
│ member_id (NIM) │     │ title           │     │ barcode         │
│ name            │     │ isbn            │     │ call_number     │
│ email           │     │ year            │     │ location_id     │
│ password        │     │ publisher_id    │     │ item_status_id  │
│ phone           │     │ place_id        │     │ branch_id       │
│ photo           │     │ pages           │     │ is_available    │
│ member_type_id  │     │ cover           │     └────────┬────────┘
│ faculty_id      │     │ abstract        │              │
│ department_id   │     └────────┬────────┘              │
│ is_active       │              │                       │
│ expire_date     │              │                       │
└────────┬────────┘              │                       │
         │                       │                       │
         │         ┌─────────────┴───────────────────────┘
         │         │
         ▼         ▼
┌─────────────────────────────────┐
│            loans                │
├─────────────────────────────────┤
│ id                              │
│ member_id (FK)                  │
│ item_id (FK)                    │
│ branch_id (FK)                  │
│ loan_date                       │
│ due_date                        │
│ return_date                     │
│ is_returned                     │
│ renew_count                     │
└────────────────┬────────────────┘
                 │
                 ▼
┌─────────────────────────────────┐
│            fines                │
├─────────────────────────────────┤
│ id                              │
│ loan_id (FK)                    │
│ member_id (FK)                  │
│ amount                          │
│ paid_amount                     │
│ is_paid                         │
│ paid_at                         │
└─────────────────────────────────┘
```

### 6.2 Tabel E-Library

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     ebooks      │     │    etheses      │     │ journal_articles│
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id              │     │ id              │     │ id              │
│ branch_id       │     │ branch_id       │     │ source_id       │
│ title           │     │ department_id   │     │ title           │
│ publisher_id    │     │ title           │     │ authors         │
│ year            │     │ title_en        │     │ journal_name    │
│ isbn            │     │ abstract        │     │ year            │
│ cover_path      │     │ abstract_en     │     │ volume          │
│ file_path       │     │ author          │     │ doi             │
│ google_drive_id │     │ nim             │     │ url             │
│ is_public       │     │ advisor1        │     │ abstract        │
│ download_count  │     │ advisor2        │     └─────────────────┘
└─────────────────┘     │ year            │
                        │ defense_date    │
                        │ type (skripsi/  │
                        │   tesis/disertasi)
                        │ cover_path      │
                        │ file_path       │
                        │ is_public       │
                        │ is_fulltext_public
                        └─────────────────┘
```

### 6.3 Tabel Layanan

```
┌─────────────────────┐     ┌─────────────────────┐
│  plagiarism_checks  │     │ thesis_submissions  │
├─────────────────────┤     ├─────────────────────┤
│ id                  │     │ id                  │
│ member_id (FK)      │     │ member_id (FK)      │
│ document_title      │     │ department_id       │
│ original_filename   │     │ title               │
│ file_path           │     │ title_en            │
│ status              │     │ abstract            │
│ similarity_score    │     │ author              │
│ provider            │     │ nim                 │
│ certificate_number  │     │ advisor1/2          │
│ certificate_path    │     │ year                │
│ completed_at        │     │ type                │
└─────────────────────┘     │ status              │
                            │ cover_file          │
┌─────────────────────┐     │ fulltext_file       │
│  clearance_letters  │     │ reviewed_by         │
├─────────────────────┤     │ reviewed_at         │
│ id                  │     └─────────────────────┘
│ member_id (FK)      │
│ thesis_submission_id│
│ letter_number       │
│ purpose             │
│ status              │
│ approved_by         │
│ approved_at         │
└─────────────────────┘
```

### 6.4 Tabel Baru untuk Mobile

```sql
-- FCM Token untuk Push Notification
CREATE TABLE member_devices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT NOT NULL,
    device_name VARCHAR(255),
    fcm_token VARCHAR(500),
    platform ENUM('android', 'ios') NOT NULL,
    app_version VARCHAR(20),
    last_active_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY unique_fcm_token (fcm_token)
);

-- Notifikasi Member
CREATE TABLE member_notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL, -- loan_due, loan_overdue, reservation_ready, etc
    title VARCHAR(255) NOT NULL,
    body TEXT,
    data JSON, -- Additional data for deep linking
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_member_unread (member_id, read_at)
);

-- Bookmark/Favorit Buku
CREATE TABLE member_bookmarks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT NOT NULL,
    book_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_bookmark (member_id, book_id)
);
```

---

## 7. Technical Architecture

### 7.1 Mobile App Architecture (Flutter)

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
├─────────────────────────────────────────────────────────────┤
│  Screens    │   Widgets    │   State Management (Riverpod)  │
└──────┬──────┴──────┬───────┴────────────────┬───────────────┘
       │             │                        │
       └─────────────┼────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│                    Domain Layer                              │
├─────────────────────────────────────────────────────────────┤
│  Use Cases    │   Entities    │   Repository Interfaces     │
└──────┬────────┴───────────────┴─────────────┬───────────────┘
       │                                      │
       └──────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│                    Data Layer                                │
├─────────────────────────────────────────────────────────────┤
│  Repositories  │  Data Sources  │  Models (DTOs)            │
│                │  - Remote (API)│                           │
│                │  - Local (Hive)│                           │
└──────┬─────────┴────────┬───────┴───────────────────────────┘
       │                  │
       ▼                  ▼
┌──────────────┐  ┌──────────────┐
│   Dio HTTP   │  │  Hive/SQLite │
│   Client     │  │  Local DB    │
└──────────────┘  └──────────────┘
```

### 7.2 Backend API Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Mobile Apps                               │
│              (Flutter - Android/iOS)                         │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTPS
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    API Gateway                               │
│              (Rate Limiting, Auth)                           │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                 Laravel API (v1)                             │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │    Auth     │  │   Catalog   │  │   E-Library │         │
│  │  Controller │  │  Controller │  │  Controller │         │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘         │
│         │                │                │                 │
│  ┌──────▼────────────────▼────────────────▼──────┐         │
│  │              Service Layer                     │         │
│  │  (Business Logic, Validation, Transformation) │         │
│  └──────────────────────┬────────────────────────┘         │
│                         │                                   │
│  ┌──────────────────────▼────────────────────────┐         │
│  │           Repository / Eloquent ORM           │         │
│  └──────────────────────┬────────────────────────┘         │
└─────────────────────────┼───────────────────────────────────┘
                          │
┌─────────────────────────▼───────────────────────────────────┐
│                      MySQL Database                          │
└─────────────────────────────────────────────────────────────┘
```

### 7.3 Push Notification Flow

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Laravel     │     │   Firebase   │     │  Mobile App  │
│  Backend     │     │     FCM      │     │  (Flutter)   │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       │  1. Send notification                   │
       │  (title, body, data)                    │
       ├───────────────────►│                    │
       │                    │                    │
       │                    │  2. Push to device │
       │                    ├───────────────────►│
       │                    │                    │
       │                    │                    │ 3. Display
       │                    │                    │    notification
       │                    │                    │
       │                    │  4. User taps      │
       │                    │◄───────────────────┤
       │                    │                    │
       │                    │                    │ 5. Deep link
       │                    │                    │    to screen
       │                    │                    │
```

### 7.4 Offline Strategy

```
┌─────────────────────────────────────────────────────────────┐
│                    Offline-First Strategy                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────┐    ┌─────────────────┐                 │
│  │   Online Mode   │    │  Offline Mode   │                 │
│  ├─────────────────┤    ├─────────────────┤                 │
│  │ • Fetch from API│    │ • Read from     │                 │
│  │ • Update cache  │    │   local cache   │                 │
│  │ • Sync changes  │    │ • Queue writes  │                 │
│  └─────────────────┘    └─────────────────┘                 │
│                                                              │
│  Cached Data:                                                │
│  ├── User profile & credentials                              │
│  ├── Active loans                                            │
│  ├── Recent catalog searches (last 100)                      │
│  ├── Bookmarked books                                        │
│  └── Downloaded e-books (optional)                           │
│                                                              │
│  NOT Cached (always online):                                 │
│  ├── Real-time availability                                  │
│  ├── Transactions (loan/return)                              │
│  └── File uploads                                            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. Timeline & Roadmap

### 8.1 Development Timeline

```
┌─────────────────────────────────────────────────────────────┐
│                    FASE 1: MVP (10 minggu)                   │
├─────────────────────────────────────────────────────────────┤
│ Minggu 1-2:  Setup & Infrastructure                          │
│              - Setup Flutter project                         │
│              - Setup API versioning (v1)                     │
│              - Firebase project setup                        │
│              - CI/CD pipeline                                │
│                                                              │
│ Minggu 3-4:  Authentication & Profile                        │
│              - Login/logout API                              │
│              - Profile API                                   │
│              - FCM token registration                        │
│              - Flutter auth screens                          │
│                                                              │
│ Minggu 5-6:  Catalog & Search                                │
│              - Enhance catalog API                           │
│              - ISBN barcode scan                             │
│              - Flutter catalog screens                       │
│              - Search & filter UI                            │
│                                                              │
│ Minggu 7-8:  Loans & Fines                                   │
│              - Loans API enhancement                         │
│              - Fines API                                     │
│              - Flutter loan screens                          │
│              - Push notification (due date)                  │
│                                                              │
│ Minggu 9-10: Polish & Testing                                │
│              - QR member card                                │
│              - UI polish                                     │
│              - Testing & bug fixes                           │
│              - Beta release                                  │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    FASE 2: E-Library (6 minggu)              │
├─────────────────────────────────────────────────────────────┤
│ Minggu 11-12: E-Book Module                                  │
│               - E-book API                                   │
│               - Flutter e-book screens                       │
│               - WebView reader                               │
│                                                              │
│ Minggu 13-14: E-Thesis Module                                │
│               - E-thesis API                                 │
│               - Flutter e-thesis screens                     │
│               - PDF viewer                                   │
│                                                              │
│ Minggu 15-16: Integration & Testing                          │
│               - Kubuku integration                           │
│               - Testing & optimization                       │
│               - Production release                           │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    FASE 3: Services (6 minggu)               │
├─────────────────────────────────────────────────────────────┤
│ Minggu 17-18: Plagiarism Check                               │
│               - Plagiarism API                               │
│               - File upload                                  │
│               - Certificate download                         │
│                                                              │
│ Minggu 19-20: Thesis Submission                              │
│               - Submission API                               │
│               - Multi-file upload                            │
│               - Status tracking                              │
│                                                              │
│ Minggu 21-22: Clearance Letter                               │
│               - Clearance API                                │
│               - PDF download                                 │
│               - Final testing                                │
└─────────────────────────────────────────────────────────────┘
```

### 8.2 Resource Requirements

| Role | Jumlah | Durasi | Keterangan |
|------|--------|--------|------------|
| Flutter Developer | 1-2 | 22 minggu | Full-time |
| Backend Developer | 1 | 22 minggu | Part-time (API only) |
| UI/UX Designer | 1 | 4 minggu | Design phase |
| QA Tester | 1 | 6 minggu | Testing phases |

### 8.3 Deliverables

| Fase | Deliverable | Target |
|------|-------------|--------|
| Fase 1 | MVP Android APK + iOS TestFlight | Minggu 10 |
| Fase 2 | E-Library Update | Minggu 16 |
| Fase 3 | Full Feature Release | Minggu 22 |
| | Play Store + App Store Release | Minggu 24 |

---

## Lampiran

### A. Checklist Persiapan

- [ ] Akun Google Play Console ($25 one-time)
- [ ] Akun Apple Developer ($99/tahun)
- [ ] Firebase Project
- [ ] SSL Certificate (sudah ada)
- [ ] API Documentation (Swagger/OpenAPI)
- [ ] Design System & UI Kit
- [ ] App Icon & Splash Screen
- [ ] Privacy Policy & Terms of Service

### B. Kontak & Referensi

- Repository: https://github.com/taufiqriza/Unida-Library
- Production URL: https://library.unida.gontor.ac.id
- API Base URL: https://library.unida.gontor.ac.id/api/v1

---

*Dokumen ini dibuat pada: 1 Januari 2026*
*Versi: 1.0*
