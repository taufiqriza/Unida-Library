# Perpustakaan - Laravel Library Management System

## Project Overview
Sistem Perpustakaan Digital UNIDA Gontor - Rewrite dari SLiMS menggunakan Laravel + Filament.

## Tech Stack
- Laravel 12
- Filament 3 (Admin Panel)
- Livewire 3 (Staff Portal & OPAC)
- MySQL
- Meilisearch (Full-text Search)
- TailwindCSS + Alpine.js

## Database
- Host: localhost
- Database: perpustakaan
- User: root / root

## Access URLs

| Portal | URL | Guard |
|--------|-----|-------|
| OPAC (Public) | `/` | - |
| Member Area | `/member` | `member` |
| Staff Portal | `/staff` | `web` |
| Admin Panel | `/admin` | `web` |

## Modules Status

### ✅ Completed
1. Branch Management (Multi-cabang dengan scope)
2. Book Catalog (Bibliografi + Items)
3. Member Management
4. CMS/News
5. Circulation (Peminjaman/Pengembalian)
6. OPAC (Public Catalog + Search)
7. E-Library (E-Book & E-Thesis)
8. Thesis Submission (Unggah Mandiri)
9. Plagiarism Check (Internal + iThenticate)
10. Stock Opname
11. Member Registration (OTP Verification)
12. Staff Registration (Approval Workflow)
13. Staff Chat (Inter-branch Communication)

### 🔄 In Progress
- Reporting & Statistics
- Mobile App API

## User Roles

| Role | Access |
|------|--------|
| `super_admin` | Full access semua cabang |
| `admin` | Full access cabang sendiri |
| `librarian` | Circulation + Catalog |
| `staff` | Limited access |
| `member` | OPAC + Member area |

## Directory Structure
```
perpustakaan/
├── app/
│   ├── Filament/           # Admin panel (Filament)
│   ├── Livewire/           # Staff portal components
│   ├── Http/Controllers/   # API & Web controllers
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic
│   └── Traits/             # Reusable traits
├── database/migrations/    # Database schema
├── docs/                   # Documentation
├── resources/views/
│   ├── filament/           # Admin views
│   ├── livewire/           # Livewire components
│   ├── opac/               # Public OPAC views
│   └── staff/              # Staff portal views
└── routes/
    ├── web.php             # OPAC routes
    ├── staff.php           # Staff portal routes
    └── api.php             # API routes
```

## Documentation
Lihat [docs/README.md](./README.md) untuk index dokumentasi lengkap.
