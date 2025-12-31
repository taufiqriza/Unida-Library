# API Specification - UNIDA Library Mobile

Base URL: `https://library.unida.gontor.ac.id/api/v1`

## Authentication

All protected endpoints require Bearer token in header:
```
Authorization: Bearer {token}
```

---

## 1. Auth Endpoints

### POST /auth/login
Login member dengan NIM/Email.

**Request:**
```json
{
  "member_id": "432022111002",
  "password": "password123",
  "device_name": "Samsung Galaxy S21",
  "fcm_token": "fcm_token_string"
}
```

**Response 200:**
```json
{
  "success": true,
  "data": {
    "token": "1|laravel_sanctum_token...",
    "token_type": "Bearer",
    "member": {
      "id": 1,
      "member_id": "432022111002",
      "name": "ABDILLAH FAHRI",
      "email": "abdillah@mhs.unida.gontor.ac.id",
      "phone": "081234567890",
      "photo_url": "https://library.unida.gontor.ac.id/storage/members/photos/1.jpg",
      "member_type": {"id": 1, "name": "Mahasiswa S1"},
      "faculty": {"id": 1, "name": "Tarbiyah"},
      "department": {"id": 1, "name": "Pendidikan Agama Islam"},
      "branch": {"id": 1, "name": "Perpustakaan Pusat"},
      "is_active": true,
      "expire_date": "2026-12-31",
      "is_expired": false
    }
  }
}
```

**Response 401:**
```json
{
  "success": false,
  "message": "NIM/Email atau password salah"
}
```

### POST /auth/logout
Logout dan hapus token.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "message": "Berhasil logout"
}
```

### GET /auth/me
Get current member profile.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "member_id": "432022111002",
    "name": "ABDILLAH FAHRI",
    "email": "abdillah@mhs.unida.gontor.ac.id",
    "phone": "081234567890",
    "photo_url": "https://...",
    "gender": "L",
    "birth_date": "2000-01-15",
    "address": "Ponorogo, Jawa Timur",
    "member_type": {"id": 1, "name": "Mahasiswa S1", "loan_limit": 5, "loan_period": 14},
    "faculty": {"id": 1, "name": "Tarbiyah"},
    "department": {"id": 1, "name": "Pendidikan Agama Islam"},
    "branch": {"id": 1, "name": "Perpustakaan Pusat"},
    "register_date": "2022-09-01",
    "expire_date": "2026-12-31",
    "is_active": true,
    "is_expired": false,
    "stats": {
      "active_loans": 2,
      "total_loans": 45,
      "unpaid_fines": 0
    }
  }
}
```

### PUT /auth/profile
Update member profile.

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "phone": "081234567890",
  "address": "Ponorogo, Jawa Timur"
}
```

### POST /auth/profile/photo
Upload profile photo.

**Headers:** `Authorization: Bearer {token}`
**Content-Type:** `multipart/form-data`

**Request:**
```
photo: [file] (max 2MB, jpg/png)
```

### POST /auth/fcm-token
Register FCM token untuk push notification.

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "fcm_token": "fcm_token_string",
  "platform": "android",
  "device_name": "Samsung Galaxy S21"
}
```

---

## 2. Catalog Endpoints

### GET /catalog
List books dengan search dan filter.

**Query Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| q | string | No | Search query |
| author_id | int | No | Filter by author |
| subject_id | int | No | Filter by subject |
| publisher_id | int | No | Filter by publisher |
| year | int | No | Filter by year |
| branch_id | int | No | Filter by branch |
| available | bool | No | Only available items |
| sort | string | No | title, year, popular, newest |
| page | int | No | Page number (default: 1) |
| per_page | int | No | Items per page (default: 20, max: 50) |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "title": "Fiqh Sunnah Jilid 1",
      "authors": [{"id": 1, "name": "Sayyid Sabiq"}],
      "publisher": "Dar al-Fikr",
      "year": 2020,
      "isbn": "978-xxx-xxx",
      "cover_url": "https://library.unida.gontor.ac.id/storage/covers/123.jpg",
      "call_number": "297.4 SAB f",
      "total_items": 5,
      "available_items": 3
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 195
  }
}
```

### GET /catalog/{id}
Get book detail.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "title": "Fiqh Sunnah Jilid 1",
    "authors": [{"id": 1, "name": "Sayyid Sabiq"}],
    "publisher": {"id": 1, "name": "Dar al-Fikr"},
    "place": "Beirut",
    "year": 2020,
    "edition": "Cet. 5",
    "isbn": "978-xxx-xxx",
    "pages": 450,
    "language": "Arabic",
    "cover_url": "https://...",
    "call_number": "297.4 SAB f",
    "subjects": [{"id": 1, "name": "Fiqh Islam"}],
    "abstract": "Kitab fiqh komprehensif...",
    "table_of_contents": "Bab 1: Thaharah...",
    "items": [
      {
        "id": 1,
        "barcode": "001234",
        "call_number": "297.4 SAB f c.1",
        "location": {"id": 1, "name": "Rak A-12"},
        "status": "available",
        "branch": {"id": 1, "name": "Perpustakaan Pusat"}
      }
    ],
    "total_items": 5,
    "available_items": 3
  }
}
```

### GET /catalog/isbn/{isbn}
Search book by ISBN (for barcode scan).

**Response 200:** Same as GET /catalog/{id}

### GET /catalog/filters
Get available filter options.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "authors": [{"id": 1, "name": "Sayyid Sabiq", "count": 15}],
    "subjects": [{"id": 1, "name": "Fiqh Islam", "count": 234}],
    "publishers": [{"id": 1, "name": "Dar al-Fikr", "count": 89}],
    "years": [2024, 2023, 2022, 2021, 2020],
    "branches": [{"id": 1, "name": "Perpustakaan Pusat"}]
  }
}
```

---

## 3. Loans Endpoints

### GET /loans
Get active loans for current member.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
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
      "item": {
        "barcode": "001234",
        "call_number": "297.4 SAB f c.1"
      },
      "loan_date": "2025-12-20",
      "due_date": "2026-01-03",
      "days_remaining": 2,
      "is_overdue": false,
      "can_renew": true,
      "renew_count": 0
    }
  ],
  "summary": {
    "total_active": 2,
    "overdue_count": 0,
    "loan_limit": 5,
    "remaining_quota": 3
  }
}
```

### GET /loans/history
Get loan history.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| page | int | Page number |
| per_page | int | Items per page |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 400,
      "book": {
        "id": 100,
        "title": "Riyadhus Shalihin",
        "authors": ["Imam Nawawi"],
        "cover_url": "https://..."
      },
      "loan_date": "2025-11-01",
      "due_date": "2025-11-15",
      "return_date": "2025-11-14",
      "was_overdue": false
    }
  ],
  "meta": {...}
}
```

### GET /fines
Get member fines.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "loan": {
        "id": 300,
        "book_title": "Tafsir Ibnu Katsir",
        "loan_date": "2025-10-01",
        "due_date": "2025-10-15",
        "return_date": "2025-10-20"
      },
      "days_overdue": 5,
      "amount": 5000,
      "paid_amount": 0,
      "remaining": 5000,
      "is_paid": false
    }
  ],
  "summary": {
    "total_fines": 1,
    "total_amount": 5000,
    "total_paid": 0,
    "total_unpaid": 5000
  }
}
```

---

## 4. E-Library Endpoints

### GET /ebooks
List e-books.

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| q | string | Search query |
| subject_id | int | Filter by subject |
| year | int | Filter by year |
| page | int | Page number |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 50,
      "title": "Metodologi Penelitian",
      "authors": ["Dr. Ahmad"],
      "publisher": "UNIDA Press",
      "year": 2023,
      "cover_url": "https://...",
      "is_public": true
    }
  ],
  "meta": {...}
}
```

### GET /ebooks/{id}
Get e-book detail.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 50,
    "title": "Metodologi Penelitian",
    "authors": [{"id": 1, "name": "Dr. Ahmad"}],
    "publisher": {"id": 1, "name": "UNIDA Press"},
    "year": 2023,
    "isbn": "978-xxx",
    "pages": 200,
    "cover_url": "https://...",
    "abstract": "Buku ini membahas...",
    "subjects": [{"id": 1, "name": "Metodologi"}],
    "is_public": true,
    "read_url": "https://library.unida.gontor.ac.id/ebook/50/read",
    "download_count": 150
  }
}
```

### GET /etheses
List e-theses.

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| q | string | Search query |
| faculty_id | int | Filter by faculty |
| department_id | int | Filter by department |
| type | string | skripsi, tesis, disertasi |
| year | int | Filter by year |
| page | int | Page number |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 100,
      "title": "Implementasi Kurikulum Merdeka...",
      "author": "Ahmad Fauzi",
      "nim": "432020111001",
      "department": "Pendidikan Agama Islam",
      "faculty": "Tarbiyah",
      "year": 2024,
      "type": "skripsi",
      "cover_url": "https://...",
      "is_fulltext_public": true
    }
  ],
  "meta": {...}
}
```

### GET /etheses/{id}
Get e-thesis detail.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 100,
    "title": "Implementasi Kurikulum Merdeka...",
    "title_en": "Implementation of Merdeka Curriculum...",
    "author": "Ahmad Fauzi",
    "nim": "432020111001",
    "advisor1": "Dr. H. Abdullah, M.Pd",
    "advisor2": "Ustadz Mahmud, M.A",
    "department": {"id": 1, "name": "Pendidikan Agama Islam"},
    "faculty": {"id": 1, "name": "Tarbiyah"},
    "year": 2024,
    "defense_date": "2024-08-15",
    "type": "skripsi",
    "abstract": "Penelitian ini bertujuan...",
    "abstract_en": "This research aims...",
    "keywords": ["kurikulum", "merdeka", "PAI"],
    "cover_url": "https://...",
    "is_fulltext_public": true,
    "download_url": "https://library.unida.gontor.ac.id/api/v1/etheses/100/download"
  }
}
```


---

## 5. Plagiarism Check Endpoints

### GET /plagiarism
List plagiarism checks for current member.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 34,
      "document_title": "Skripsi - Implementasi ML",
      "original_filename": "skripsi_final.pdf",
      "file_size": "2.5 MB",
      "status": "completed",
      "status_label": "Selesai",
      "status_color": "green",
      "similarity_score": 18.5,
      "similarity_level": "low",
      "similarity_label": "Rendah (Aman)",
      "is_passed": true,
      "provider": "iThenticate",
      "certificate_number": "PLAG-202601-00034",
      "has_certificate": true,
      "created_at": "2026-01-01T10:00:00Z",
      "completed_at": "2026-01-01T10:15:00Z"
    }
  ]
}
```

### POST /plagiarism
Submit document for plagiarism check.

**Headers:** `Authorization: Bearer {token}`
**Content-Type:** `multipart/form-data`

**Request:**
```
document_title: "Skripsi - Judul Lengkap"
document: [file] (PDF/DOCX, max 20MB)
```

**Response 201:**
```json
{
  "success": true,
  "message": "Dokumen berhasil diupload",
  "data": {
    "id": 35,
    "status": "pending",
    "status_label": "Menunggu Antrian",
    "estimated_time": "5-15 menit"
  }
}
```

### GET /plagiarism/{id}
Get plagiarism check detail.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 34,
    "document_title": "Skripsi - Implementasi ML",
    "original_filename": "skripsi_final.pdf",
    "file_size": "2.5 MB",
    "word_count": 15000,
    "page_count": 85,
    "status": "completed",
    "status_info": {
      "status": "completed",
      "label": "Selesai",
      "message": "Pengecekan selesai.",
      "color": "green",
      "icon": "check-circle"
    },
    "similarity_score": 18.5,
    "similarity_level": "low",
    "similarity_label": "Rendah (Aman)",
    "is_passed": true,
    "pass_threshold": 25,
    "provider": "iThenticate",
    "certificate_number": "PLAG-202601-00034",
    "has_certificate": true,
    "certificate_url": "/api/v1/plagiarism/34/certificate",
    "processing_time": "12 menit 30 detik",
    "created_at": "2026-01-01T10:00:00Z",
    "started_at": "2026-01-01T10:02:00Z",
    "completed_at": "2026-01-01T10:14:30Z"
  }
}
```

### GET /plagiarism/{id}/certificate
Download plagiarism certificate PDF.

**Headers:** `Authorization: Bearer {token}`

**Response 200:** PDF file download

### POST /plagiarism/external
Submit external plagiarism check result (Turnitin/iThenticate).

**Headers:** `Authorization: Bearer {token}`
**Content-Type:** `multipart/form-data`

**Request:**
```
document_title: "Skripsi - Judul"
document: [file] (PDF dokumen asli)
report: [file] (PDF hasil cek plagiasi)
platform: "turnitin" | "ithenticate" | "copyscape"
similarity_score: 15.5
```

---

## 6. Thesis Submission Endpoints

### GET /submissions
List thesis submissions for current member.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "title": "Implementasi Kurikulum Merdeka",
      "type": "skripsi",
      "type_label": "Skripsi",
      "department": "Pendidikan Agama Islam",
      "status": "approved",
      "status_label": "Disetujui",
      "status_color": "green",
      "has_clearance_letter": true,
      "created_at": "2025-12-01T10:00:00Z",
      "reviewed_at": "2025-12-05T14:00:00Z"
    }
  ]
}
```

### POST /submissions
Create new thesis submission.

**Headers:** `Authorization: Bearer {token}`
**Content-Type:** `multipart/form-data`

**Request:**
```
title: "Judul Skripsi Lengkap"
title_en: "English Title"
abstract: "Abstrak dalam Bahasa Indonesia..."
abstract_en: "Abstract in English..."
type: "skripsi" | "tesis" | "disertasi"
department_id: 1
year: 2026
defense_date: "2026-01-15"
advisor1: "Dr. H. Abdullah, M.Pd"
advisor2: "Ustadz Mahmud, M.A"
keywords: "kata kunci, keyword"
cover_file: [file] (JPG/PNG)
fulltext_file: [file] (PDF)
fulltext_visible: true | false
```

### GET /submissions/{id}
Get submission detail.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 10,
    "title": "Implementasi Kurikulum Merdeka",
    "title_en": "Implementation of Merdeka Curriculum",
    "abstract": "...",
    "abstract_en": "...",
    "author": "Ahmad Fauzi",
    "nim": "432020111001",
    "type": "skripsi",
    "department": {"id": 1, "name": "PAI"},
    "faculty": {"id": 1, "name": "Tarbiyah"},
    "year": 2026,
    "defense_date": "2026-01-15",
    "advisor1": "Dr. H. Abdullah, M.Pd",
    "advisor2": "Ustadz Mahmud, M.A",
    "keywords": ["kurikulum", "merdeka"],
    "status": "approved",
    "status_label": "Disetujui",
    "review_notes": "Dokumen lengkap dan sesuai",
    "reviewed_by": "Admin Perpustakaan",
    "reviewed_at": "2025-12-05T14:00:00Z",
    "cover_url": "https://...",
    "fulltext_visible": true,
    "clearance_letter": {
      "id": 5,
      "letter_number": "001/SKB/PERPUS/I/2026",
      "status": "approved"
    },
    "created_at": "2025-12-01T10:00:00Z"
  }
}
```

---

## 7. Clearance Letter Endpoints

### GET /clearance
List clearance letters for current member.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "letter_number": "001/SKB/PERPUS/I/2026",
      "purpose": "Bebas Pustaka - Skripsi",
      "status": "approved",
      "status_label": "Disetujui",
      "approved_at": "2026-01-05T10:00:00Z",
      "thesis_title": "Implementasi Kurikulum Merdeka",
      "download_url": "/api/v1/clearance/5/download",
      "created_at": "2026-01-01T10:00:00Z"
    }
  ]
}
```

### GET /clearance/{id}/download
Download clearance letter PDF.

**Headers:** `Authorization: Bearer {token}`

**Response 200:** PDF file download

### GET /clearance/check
Check eligibility for clearance letter.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "eligible": true,
    "requirements": {
      "no_active_loans": {"status": true, "message": "Tidak ada peminjaman aktif"},
      "no_unpaid_fines": {"status": true, "message": "Tidak ada denda belum dibayar"},
      "has_thesis_submission": {"status": true, "message": "Sudah mengajukan karya ilmiah"}
    }
  }
}
```

---

## 8. Notification Endpoints

### GET /notifications
List notifications for current member.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| unread_only | bool | Only unread notifications |
| page | int | Page number |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 100,
      "type": "loan_due_reminder",
      "title": "Pengingat Jatuh Tempo",
      "body": "Buku 'Fiqh Sunnah' akan jatuh tempo dalam 2 hari",
      "data": {
        "loan_id": 456,
        "book_id": 123
      },
      "read_at": null,
      "created_at": "2026-01-01T08:00:00Z"
    }
  ],
  "meta": {...}
}
```

### PUT /notifications/{id}/read
Mark notification as read.

**Headers:** `Authorization: Bearer {token}`

### PUT /notifications/read-all
Mark all notifications as read.

**Headers:** `Authorization: Bearer {token}`

### GET /notifications/unread-count
Get unread notification count.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "count": 3
  }
}
```

---

## 9. General Endpoints

### GET /branches
List library branches.

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "PUSAT",
      "name": "Perpustakaan Pusat",
      "address": "Kampus UNIDA Gontor",
      "phone": "0352-123456",
      "email": "perpus@unida.gontor.ac.id",
      "is_main": true
    }
  ]
}
```

### GET /faculties
List faculties.

**Response 200:**
```json
{
  "success": true,
  "data": [
    {"id": 1, "name": "Tarbiyah"},
    {"id": 2, "name": "Ushuluddin"},
    {"id": 3, "name": "Syariah"}
  ]
}
```

### GET /departments
List departments.

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| faculty_id | int | Filter by faculty |

**Response 200:**
```json
{
  "success": true,
  "data": [
    {"id": 1, "name": "Pendidikan Agama Islam", "faculty_id": 1},
    {"id": 2, "name": "Pendidikan Bahasa Arab", "faculty_id": 1}
  ]
}
```

### GET /news
List news/announcements.

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| page | int | Page number |

### GET /settings
Get app settings.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "app_name": "Perpustakaan UNIDA Gontor",
    "app_logo": "https://...",
    "contact_email": "perpus@unida.gontor.ac.id",
    "contact_phone": "0352-123456",
    "contact_whatsapp": "6281234567890",
    "address": "Kampus UNIDA Gontor, Ponorogo",
    "operating_hours": "Senin-Jumat: 08:00-16:00",
    "fine_per_day": 1000,
    "loan_period_days": 14,
    "max_renew": 2
  }
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid/expired token |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource not found |
| 422 | Validation Error |
| 429 | Too Many Requests - Rate limited |
| 500 | Server Error |

## Rate Limiting

| Endpoint | Limit |
|----------|-------|
| POST /auth/login | 5 req/min |
| General API | 60 req/min |
| File upload | 10 req/min |

---

*Last updated: 1 Januari 2026*
