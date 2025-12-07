# Perpustakaan - Laravel Library Management System

## Project Overview
Rewrite SLiMS (Senayan Library Management System) menggunakan Laravel + Filament.
Referensi: `/Library/WebServer/web-server/opac` (SLiMS original)

## Tech Stack
- Laravel 11
- Filament 3 (Admin Panel)
- MySQL
- Livewire

## Database
- Host: localhost
- Database: perpustakaan
- User: root / root

## Admin Access
- URL: http://localhost/perpustakaan/public/admin
- Email: admin@perpustakaan.id
- Password: password

## Migration from SLiMS
Data SLiMS (~45,000 buku) akan di-migrate dari database `opac-real`

## Modules Plan
1. ✅ Branch Management (Multi-cabang)
2. ✅ Book Catalog (Bibliografi)
3. ✅ Member Management
4. ✅ CMS/News (Berita)
5. ⬜ Circulation (Peminjaman)
6. ⬜ OPAC (Public Catalog)
7. ⬜ Reporting
8. ⬜ Stock Take

## Directory Structure
```
perpustakaan/
├── app/
│   ├── Filament/Resources/  # Admin panel resources
│   └── Models/              # Eloquent models
├── database/migrations/     # Database schema
├── docs/                    # Documentation
└── public/                  # Web root
```
