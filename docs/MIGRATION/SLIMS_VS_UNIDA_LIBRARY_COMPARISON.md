# 📊 Perbandingan SLiMS vs UNIDA Library System
## Bahan Presentasi: Modernisasi Sistem Perpustakaan Perguruan Tinggi

---

## 📋 Executive Summary

| Aspek | SLiMS (Senayan) | UNIDA Library System |
|-------|-----------------|----------------------|
| **Arsitektur** | Monolitik PHP Native | Modern Laravel 11 + Livewire |
| **Database** | Single-tenant | Multi-tenant (Multi-cabang) |
| **UI/UX** | Traditional Web 2.0 | Modern Web 3.0 (SPA-like) |
| **Skalabilitas** | Terbatas | Highly Scalable |
| **Integrasi** | Plugin-based | Native API + Services |
| **Target** | Perpustakaan Umum | Perguruan Tinggi (Multi-fakultas) |

---

## 1. 🏗️ Arsitektur Sistem

### SLiMS (Senayan Library Management System)
```
┌─────────────────────────────────────────┐
│           SLIMS ARCHITECTURE             │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────────────────────┐    │
│  │         PHP Native Code          │    │
│  │      (Procedural + OOP Mix)      │    │
│  └─────────────────────────────────┘    │
│               ↓                          │
│  ┌─────────────────────────────────┐    │
│  │         MySQL Database           │    │
│  │    (Single Database/Instance)    │    │
│  └─────────────────────────────────┘    │
│                                         │
│  Modules: OPAC, Bibliografi, Sirkulasi, │
│           Keanggotaan, Serial, Stocktake│
│                                         │
└─────────────────────────────────────────┘
```

**Karakteristik:**
- PHP Native tanpa framework modern
- Tidak memiliki ORM (query SQL langsung)
- Plugin system untuk ekstensi
- Template engine: Smarty/Native PHP
- Ajax dengan jQuery

### UNIDA Library System
```
┌─────────────────────────────────────────────────────────────────┐
│                UNIDA LIBRARY ARCHITECTURE                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │   MEMBER    │  │    STAFF    │  │    ADMIN    │              │
│  │   PORTAL    │  │   PORTAL    │  │    PANEL    │              │
│  │  (Public)   │  │  (Livewire) │  │  (Filament) │              │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘              │
│         │                │                 │                     │
│         └────────────────┼─────────────────┘                     │
│                          ↓                                       │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │              LARAVEL 11 APPLICATION LAYER                 │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐ │   │
│  │  │ Services │ │  Models  │ │ Policies │ │ Middlewares  │ │   │
│  │  │          │ │(Eloquent)│ │ & Gates  │ │ (Branch ACL) │ │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
│                          ↓                                       │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │        MySQL DATABASE (Multi-Branch Isolation)           │    │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────┐  │    │
│  │  │ Branch A │ │ Branch B │ │ Branch C │ │  Shared    │  │    │
│  │  │ (Filter) │ │ (Filter) │ │ (Filter) │ │   Data     │  │    │
│  │  └──────────┘ └──────────┘ └──────────┘ └────────────┘  │    │
│  └─────────────────────────────────────────────────────────┘    │
│                          ↓                                       │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                EXTERNAL INTEGRATIONS                      │    │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ │    │
│  │  │ Google │ │Shamela │ │Lucene  │ │Turnitin│ │   API  │ │    │
│  │  │ OAuth  │ │  API   │ │ Search │ │  Like  │ │Gateway │ │    │
│  │  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘ │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Karakteristik:**
- Laravel 11 Framework (LTS)
- Eloquent ORM dengan Relationships
- Livewire untuk interaktivitas real-time
- Filament untuk Admin Panel
- Alpine.js untuk UI interaktif
- Service Layer Pattern
- Multi-tenant architecture

---

## 2. 📐 Skema Database

### SLiMS Database Schema
```sql
-- SLiMS: Single-tenant, flat structure
-- Tidak ada konsep branch/cabang

biblio
├── biblio_id (PK)
├── title
├── gmd_id (FK)
├── publisher_id (FK)
├── publish_year
├── collation
├── isbn_issn
├── call_number
└── ... (max ~40 columns)

item
├── item_id (PK)
├── biblio_id (FK)
├── item_code (Barcode)
├── coll_type_id
├── location_id
└── item_status_id

member
├── member_id (PK)
├── member_name
├── member_type_id
├── expire_date
└── ... (no branch concept)

loan
├── loan_id (PK)
├── item_code
├── member_id
├── loan_date
├── due_date
└── is_return
```

### UNIDA Library Database Schema
```sql
-- UNIDA: Multi-tenant dengan branch isolation

branches
├── id (PK)
├── code
├── name
├── is_main
└── is_active

books (biblio)
├── id (PK)
├── branch_id (FK) ← Multi-branch!
├── title
├── isbn
├── publisher_id
├── call_number
└── ...

items (eksemplar)
├── id (PK)
├── book_id (FK)
├── branch_id (FK) ← Multi-branch!
├── barcode
├── status
└── location_id

members
├── id (PK)
├── branch_id (FK) ← Multi-branch!
├── member_id
├── name
├── email
├── faculty_id (FK) ← Academic Structure!
├── department_id (FK)
└── ...

loans
├── id (PK)
├── branch_id (FK) ← Multi-branch!
├── member_id (FK)
├── item_id (FK)
├── loan_date
├── due_date
└── ...

-- Additional Tables (Not in SLiMS):
faculties           ← Struktur Akademik
departments         ← Prodi/Jurusan
divisions           ← Unit Kerja
thesis_submissions  ← E-Thesis Submission
plagiarism_checks   ← Plagiarism Detection
ebooks              ← Digital Library
etheses             ← Institutional Repository
journal_articles    ← Journal Integration
tasks               ← Task Management
staff_notifications ← Notification System
stock_opname        ← Inventory Audit
```

---

## 3. 🎯 Perbandingan Fitur

### A. Fitur Inti Perpustakaan

| Fitur | SLiMS | UNIDA Library |
|-------|-------|---------------|
| **OPAC** | ✅ Basic Search | ✅ Advanced + Federated Search |
| **Bibliografi** | ✅ Standard | ✅ + Cover auto-fetch dari Google |
| **Sirkulasi** | ✅ Standard | ✅ + POS-like Interface |
| **Keanggotaan** | ✅ Basic | ✅ + Google OAuth + Email Verification |
| **Serial** | ✅ Basic | 🔄 (Future) |
| **Stocktaking** | ✅ Basic | ✅ + Progress Tracking + Report |
| **Multi-cabang** | ❌ | ✅ Native Support |
| **Laporan** | ✅ PDF/Excel | ✅ + Real-time Analytics |

### B. Fitur Lanjutan (Hanya di UNIDA Library)

| Fitur | Deskripsi |
|-------|-----------|
| **🎓 E-Thesis Submission** | Mahasiswa submit tugas akhir dengan workflow approval multi-level |
| **🔍 Plagiarism Detection** | Cek plagiarisme dengan fingerprinting + similarity check |
| **📚 Digital Library** | E-books, E-journals, Shamela (Kitab Islam) |
| **📰 Journal Integration** | SINTA journals, auto-fetch metadata |
| **📝 Task Management** | Kanban board untuk manajemen tugas pustakawan |
| **🔔 Notification System** | In-app, Email, WhatsApp notifications |
| **💬 Staff Chat** | Komunikasi antar pustakawan |
| **📊 Google Analytics** | Integrasi analytics untuk statistik pengunjung |
| **🔐 SSO Google** | Login dengan akun Google institusi |
| **📱 Responsive Design** | Mobile-first dengan bottom navigation |
| **🌙 Dark Mode Ready** | CSS custom properties untuk theming |

### C. Fitur Khusus Perguruan Tinggi

| Fitur | SLiMS | UNIDA Library |
|-------|-------|---------------|
| **Struktur Fakultas/Prodi** | ❌ | ✅ Faculty → Department → Member |
| **Bebas Pustaka Digital** | ❌ | ✅ Clearance Letter System |
| **Repositori Institusi** | ❌ | ✅ E-Thesis + E-journals |
| **Verifikasi Email** | ❌ | ✅ Domain-based verification |
| **Multi-role Staff** | ❌ | ✅ Super Admin, Admin, Librarian |

---

## 4. 👥 Pembagian Portal

### SLiMS
```
┌────────────────────────────────────────┐
│              SLiMS ACCESS               │
├────────────────────────────────────────┤
│                                        │
│  [OPAC]           [ADMIN]              │
│  (Public)         (Staff Only)         │
│      │                │                │
│      ↓                ↓                │
│  Read-only        Full Access          │
│  Catalog          to Everything        │
│                                        │
│  • Search         • Bibliografi        │
│  • Reserve        • Sirkulasi          │
│  • Member Info    • Keanggotaan        │
│                   • Laporan            │
│                   • Master Data        │
│                                        │
└────────────────────────────────────────┘
```

### UNIDA Library
```
┌────────────────────────────────────────────────────────────────────┐
│                      UNIDA LIBRARY ACCESS                           │
├────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐                 │
│  │   PUBLIC    │  │   MEMBER    │  │    STAFF    │                 │
│  │    OPAC     │  │   PORTAL    │  │   PORTAL    │                 │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘                 │
│         │                │                 │                        │
│         ↓                ↓                 ↓                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌───────────┐  │
│  │  No Login   │  │ Member Auth │  │ Staff Auth  │  │Super Admin│  │
│  │  Required   │  │   Guard     │  │   Guard     │  │  Filament │  │
│  └─────────────┘  └─────────────┘  └─────────────┘  └───────────┘  │
│                                                                     │
│  Features:        Features:        Features:        Features:       │
│  • Search         • Dashboard      • Dashboard      • Full CRUD     │
│  • Catalog        • History        • Circulation    • User Mgmt     │
│  • E-resources    • Reservations   • Bibliography   • Branch Mgmt   │
│  • News           • Profile        • Member Mgmt    • Settings      │
│  • Shamela        • Thesis Submit  • Stock Opname   • Reports       │
│                   • Plagiarism     • Statistics     • System Config │
│                   • E-book Access  • Task Board     • Audit Log     │
│                                    • Notifications                  │
│                                    • Staff Chat                     │
│                                                                     │
└────────────────────────────────────────────────────────────────────┘
```

---

## 5. 🔧 Technology Stack

| Layer | SLiMS | UNIDA Library |
|-------|-------|---------------|
| **Backend Language** | PHP 7.x/8.x | PHP 8.2+ |
| **Framework** | Native PHP + Custom MVC | Laravel 11 |
| **Database** | MySQL/MariaDB | MySQL/MariaDB + Redis |
| **ORM** | None (Raw SQL) | Eloquent |
| **Frontend Framework** | jQuery + Bootstrap 4 | Tailwind CSS + Alpine.js |
| **Reactivity** | jQuery AJAX | Livewire 3 |
| **Admin Panel** | Custom PHP | Filament 3 |
| **Template Engine** | PHP/Smarty | Blade |
| **Authentication** | Session-based | Laravel Sanctum + Guards |
| **Queue System** | None | Laravel Queue + Horizon |
| **Cache** | File-based | Redis/Database |
| **Search Engine** | MySQL LIKE | MySQL + Lucene (Java) |

---

## 6. 💎 Keunggulan UNIDA Library

### A. Multi-Branch Native
```php
// Setiap query otomatis ter-filter berdasarkan branch
class Book extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('branch', function ($query) {
            if ($branchId = auth()->user()?->branch_id) {
                $query->where('branch_id', $branchId);
            }
        });
    }
}
```

### B. Modern Authentication
```php
// Multiple auth guards
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],      // Staff
    'member' => ['driver' => 'session', 'provider' => 'members'], // Member
    'api' => ['driver' => 'sanctum'],                              // API
];

// Google OAuth dengan role detection
// Staff: Manual onboarding → Link Google
// Member: Direct OAuth → Auto create
```

### C. Real-time Updates dengan Livewire
```php
// Notification bell updates in real-time
class NotificationBell extends Component
{
    public $unreadCount = 0;
    
    protected $listeners = ['notification-received' => 'refresh'];
    
    public function render()
    {
        $this->unreadCount = StaffNotification::forUser(auth()->id())
            ->unread()
            ->count();
            
        return view('livewire.notification-bell');
    }
}
```

### D. Service Layer Architecture
```php
// Clean separation of concerns
app/
├── Http/Controllers/    # HTTP handling only
├── Livewire/           # UI Components
├── Models/             # Data & Business Rules
├── Services/           # Business Logic
│   ├── NotificationService.php
│   ├── PlagiarismService.php
│   └── CirculationService.php
├── Observers/          # Event Handling
└── Policies/           # Authorization
```

### E. Event-Driven Architecture
```php
// TaskObserver - auto notifications
class TaskObserver
{
    public function created(Task $task)
    {
        // Auto notify assignee when task created
        $this->notificationService->send(
            $task->assignee,
            'task',
            'Tugas Baru Ditugaskan',
            "Anda ditugaskan untuk: {$task->title}"
        );
    }
}
```

---

## 7. 📊 Perbandingan Visual

### UI/UX Comparison

```
┌─────────────────────────────────────────────────────────────────┐
│                        SLiMS INTERFACE                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────────────┐│
│  │ [Logo] Perpustakaan XYZ    [Bahasa] [Login] [Register]      ││
│  └─────────────────────────────────────────────────────────────┘│
│  ┌───────────┬─────────────────────────────────────────────────┐│
│  │  MENU     │                                                  ││
│  │           │     [Search Box _______________] [Search]        ││
│  │ • Home    │                                                  ││
│  │ • Katalog │     ┌─────────────────────────────────────────┐ ││
│  │ • Member  │     │  📖 Judul Buku Contoh                   │ ││
│  │ • News    │     │  Penulis: John Doe                      │ ││
│  │           │     │  call#: 000.123                         │ ││
│  │           │     └─────────────────────────────────────────┘ ││
│  └───────────┴─────────────────────────────────────────────────┘│
│  Traditional layout, basic styling, limited animations          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    UNIDA LIBRARY INTERFACE                       │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────────────┐│
│  │   [🔍 Search with filters...]                   [🔔] [👤]   ││
│  │   Faceted: [Semua ▾] [Tahun ▾] [Format ▾]                   ││
│  └─────────────────────────────────────────────────────────────┘│
│  ┌─────────────────────────────────────────────────────────────┐│
│  │  HERO SECTION                                                ││
│  │  ╔═══════════════════════════════════════════════════════╗  ││
│  │  ║  🎓 Selamat Datang di Perpustakaan Digital UNIDA     ║  ││
│  │  ║  [Cari Buku] [E-Library] [Shamela] [Submit Thesis]    ║  ││
│  │  ╚═══════════════════════════════════════════════════════╝  ││
│  └─────────────────────────────────────────────────────────────┘│
│  ┌─────────────────────────────────────────────────────────────┐│
│  │  KOLEKSI TERBARU                                 [Lihat →]  ││
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       ││
│  │  │ [Cover]  │ │ [Cover]  │ │ [Cover]  │ │ [Cover]  │       ││
│  │  │ Title 1  │ │ Title 2  │ │ Title 3  │ │ Title 4  │       ││
│  │  │ Author   │ │ Author   │ │ Author   │ │ Author   │       ││
│  │  │ ⭐⭐⭐⭐  │ │ ⭐⭐⭐⭐⭐ │ │ ⭐⭐⭐    │ │ ⭐⭐⭐⭐  │       ││
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘       ││
│  └─────────────────────────────────────────────────────────────┘│
│  Modern cards, gradients, shadows, hover animations             │
└─────────────────────────────────────────────────────────────────┘
```

---

## 8. 📈 Roadmap Pengembangan

### Phase 1: Foundation ✅ (Completed)
- [x] Multi-branch database schema
- [x] Three-portal architecture (Public, Member, Staff)
- [x] Google OAuth integration
- [x] Member registration & verification
- [x] Basic OPAC with search

### Phase 2: Core Library ✅ (Completed)
- [x] Bibliography management
- [x] Circulation system
- [x] Member management
- [x] Stock opname module
- [x] Statistics & analytics

### Phase 3: Digital Library ✅ (Completed)
- [x] E-book repository
- [x] E-thesis repository
- [x] Shamela integration (Islamic texts)
- [x] Journal articles integration

### Phase 4: Academic Features ✅ (Completed)
- [x] Thesis submission workflow
- [x] Plagiarism detection system
- [x] Clearance letter generation
- [x] Faculty/Department structure

### Phase 5: Staff Tools ✅ (Completed)
- [x] Task management (Kanban)
- [x] Notification system
- [x] Staff chat
- [x] Advanced reporting

### Phase 6: Future Enhancements 🔄
- [ ] WhatsApp notification channel
- [ ] Browser push notifications
- [ ] AI-powered book recommendations
- [ ] Self-service kiosk mode
- [ ] Mobile native app (PWA)
- [ ] Reservation system
- [ ] Room & facility booking

---

## 9. 🎓 Kesimpulan

### Mengapa UNIDA Library lebih baik untuk Perguruan Tinggi?

| Alasan | Penjelasan |
|--------|------------|
| **1. Multi-cabang Native** | Satu instance mendukung banyak perpustakaan fakultas |
| **2. Integrasi Akademik** | Terhubung dengan struktur fakultas & prodi |
| **3. Modern Stack** | Laravel 11 + Livewire = maintainable & scalable |
| **4. SSO Ready** | Google OAuth untuk civitas akademika |
| **5. E-Thesis Workflow** | End-to-end tugas akhir submission |
| **6. Plagiarism Check** | Built-in similarity detection |
| **7. Real-time UI** | Livewire untuk update tanpa refresh |
| **8. Notification System** | Multi-channel notifications |
| **9. Task Management** | Pustakawan bisa track pekerjaan |
| **10. API Ready** | Siap integrasi dengan SIAKAD dll |

### Kapan SLiMS Masih Cocok?

| Situasi | Rekomendasi |
|---------|-------------|
| Perpustakaan umum single-location | ✅ SLiMS |
| Perpustakaan sekolah sederhana | ✅ SLiMS |
| Tidak ada tim IT in-house | ✅ SLiMS (banyak komunitas) |
| Budget terbatas, butuh cepat | ✅ SLiMS |
| Perguruan tinggi multi-fakultas | ✅ **UNIDA Library** |
| Butuh integrasi dengan sistem akademik | ✅ **UNIDA Library** |
| Butuh e-thesis & plagiarism check | ✅ **UNIDA Library** |
| Butuh multi-cabang | ✅ **UNIDA Library** |

---

## 10. 📞 Kontak & Demo

**UNIDA Library System**
- Demo: [https://lib.unida.ac.id](https://lib.unida.ac.id)
- Repository: Private (upon request)
- Developer: IT UNIDA Team

---

*Dokumen ini dibuat untuk keperluan presentasi perbandingan sistem perpustakaan.*
*Last Updated: December 2024*
