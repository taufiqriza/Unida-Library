# Staff Portal Architecture & Implementation Plan

## 1. Overview
Staff Portal adalah dashboard khusus untuk Pustakawan (Librarian) dan Admin Cabang yang berfokus pada operasional harian perpustakaan. Portal ini terpisah dari Admin Panel (Filament) yang kompleks dan Member Portal.

**Tujuan:**
- Menyederhanakan UI untuk workflow sirkulasi yang cepat.
- Membatasi akses data berdasarkan `branch_id`.
- Memberikan pengalaman *Desktop App-like* yang powerful dan responsif.

---

## 2. Authentication Flow

### 2.1 Unified Login dengan Auto-Detection
Menggunakan **satu halaman login** dengan deteksi otomatis berdasarkan input:

```
┌─────────────────────────────────────────────────────────────┐
│                    LOGIN DETECTION FLOW                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Input: "john@unida.ac.id" + password                       │
│      ↓                                                      │
│  Detect "@" → Query `users` table                           │
│      ↓                                                      │
│  Found + password match → auth('web')->login()              │
│      ↓                                                      │
│  Redirect → /staff/dashboard                                │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Input: "2024001234" + password                             │
│      ↓                                                      │
│  No "@" → Query `members` table (by member_id)              │
│      ↓                                                      │
│  Found + password match → auth('member')->login()           │
│      ↓                                                      │
│  Redirect → /member/dashboard                               │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Staff Account Creation (Security First)
Akun staff **TIDAK** bisa dibuat via Google OAuth langsung. Flow yang aman:

```
┌─────────────────────────────────────────────────────────────┐
│                    STAFF ONBOARDING                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Super Admin buat akun staff di Filament Admin           │
│     → Set email, password temporary, role, branch           │
│                                                             │
│  2. Staff login pertama kali (email + password)             │
│     → Redirect ke /staff/dashboard                          │
│                                                             │
│  3. Di Staff Portal → Settings/Profile                      │
│     → "Hubungkan dengan Google Account"                     │
│     → OAuth flow → simpan ke social_accounts                │
│                                                             │
│  4. Login berikutnya bisa pakai:                            │
│     - Email + Password (tetap bisa)                         │
│     - Google OAuth (jika sudah terhubung)                   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**Keuntungan:**
- Security: Tidak sembarang orang bisa jadi staff hanya dengan email @unida.ac.id
- Accountability: Ada audit trail siapa yang buat akun staff
- Flexibility: Staff bisa pilih login manual atau Google
- Separation: Member dan Staff benar-benar terpisah dari awal

---

## 3. User Roles & Permissions
Berdasarkan tabel `users`:
- `super_admin`: Akses Full Filament + Staff Portal (semua cabang)
- `admin`: Akses Staff Portal (cabang sendiri) + kelola data master
- `librarian`: Akses Staff Portal (cabang sendiri) + fokus sirkulasi

---

## 4. UI/UX Design Concept

### 4.1 Dual Mode: Desktop & Mobile
Mengadopsi konsep **TAMS Teacher Portal** dengan dua mode yang berbeda:

#### Desktop Mode (≥1024px)
```
┌──────────────────────────────────────────────────────────────────────┐
│ ┌──────────┐ ┌─────────────────────────────────────────────────────┐ │
│ │          │ │  HEADER (Fixed)                                     │ │
│ │          │ │  [≡] Dashboard          Rabu, 11 Des 2024    [👤]   │ │
│ │ SIDEBAR  │ ├─────────────────────────────────────────────────────┤ │
│ │ (Fixed)  │ │                                                     │ │
│ │          │ │                    MAIN CONTENT                     │ │
│ │ • Dash   │ │                    (Scrollable)                     │ │
│ │ • Sirku  │ │                                                     │ │
│ │ • Buku   │ │                                                     │ │
│ │ • Member │ │                                                     │ │
│ │ • Stock  │ │                                                     │ │
│ │          │ │                                                     │ │
│ │ ──────── │ │                                                     │ │
│ │ [Logout] │ │                                                     │ │
│ └──────────┘ └─────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

**Fitur Desktop:**
- Fixed sidebar (224px expanded, 80px collapsed)
- Collapsible dengan smooth transition (cubic-bezier)
- Sidebar state disimpan di localStorage
- Fixed header dengan breadcrumb & user info
- Main content scrollable (body overflow hidden)
- GPU acceleration untuk smooth animation
- Custom scrollbar styling

#### Mobile Mode (<1024px)
```
┌────────────────────────────┐
│ HEADER (Fixed)             │
│ [Logo] Dashboard [👤][🔔]  │
├────────────────────────────┤
│                            │
│      MAIN CONTENT          │
│      (Scrollable)          │
│                            │
│                            │
│                            │
├────────────────────────────┤
│ BOTTOM NAV (Fixed)         │
│ [🏠] [📚] [🔄] [👥] [⚙️]  │
└────────────────────────────┘
```

**Fitur Mobile:**
- Bottom navigation bar (seperti native app)
- Floating action button untuk scan
- Compact header dengan dropdown menu
- Touch-friendly dengan hover states

### 4.2 Design System

#### Colors
```css
/* Primary Blue Gradient (Sidebar & Header) */
--sidebar-bg: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
--header-bg: linear-gradient(135deg, #1d4ed8 0%, #1e40af 50%, #1e3a8a 100%);

/* Accent Colors */
--active-indicator: linear-gradient(to bottom, #fbbf24, #f97316);
--scanner-btn: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);

/* Status Colors */
--success: #10b981;
--warning: #f59e0b;
--danger: #ef4444;
--info: #3b82f6;
```

#### Typography
```css
font-family: 'Inter', sans-serif;
/* High Density Mode */
--text-xs: 0.75rem;   /* 12px - labels, badges */
--text-sm: 0.875rem;  /* 14px - body text */
--text-base: 1rem;    /* 16px - headings */
```

#### Components
- **Cards**: `rounded-2xl`, `shadow-sm`, `border border-slate-200`
- **Buttons**: `rounded-xl`, gradient backgrounds, hover lift effect
- **Inputs**: `rounded-lg`, focus ring, clean borders
- **Badges**: `rounded-full`, colored backgrounds

### 4.3 Animations & Transitions
```css
/* Sidebar collapse */
transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);

/* Hover effects */
transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

/* Button lift */
hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200

/* GPU Acceleration */
backface-visibility: hidden;
transform: translateZ(0);
-webkit-font-smoothing: antialiased;
```

---

## 5. Tech Stack

| Layer | Technology | Reason |
|-------|------------|--------|
| Backend | Laravel 11 | Existing stack |
| Templating | Blade | Server-side rendering |
| Styling | Tailwind CSS | Utility-first, consistent |
| Interactivity | Alpine.js | Lightweight, declarative |
| Reactivity | Livewire | Real-time updates tanpa full SPA |
| Icons | Font Awesome 6 | Comprehensive icon set |

---

## 6. Module Breakdown

### A. Dashboard (`/staff/dashboard`)
- **Stats Cards**: Peminjaman hari ini, Pengunjung, Jatuh tempo, Denda
- **Quick Actions**: Scan Barcode, Cek Stok, Input Pengunjung
- **Recent Activities**: Log sirkulasi terakhir
- **Alerts**: Overdue, Expired members

### B. Sirkulasi (`/staff/circulation`) - *Core Feature*
- **POS-like Interface**: Scan member → Scan buku → Proses
- **Member Preview**: Foto, status, tanggungan denda
- **Batch Processing**: Multiple books per transaction
- **Return Mode**: Scan buku → Auto calculate denda
- **Sound Feedback**: Beep sukses/gagal

### C. Bibliografi (`/staff/books`)
- **Search**: Quick search by title, ISBN, author
- **Filter**: By branch, category, availability
- **Stock View**: Exemplar availability per rak
- **Print**: Label & barcode printing

### D. Member Management (`/staff/members`)
- **List & Search**: Filter by branch, status
- **Verification**: Approve new registrations
- **Card Print**: QR Code member card

### E. Stock Opname (`/staff/stock-opname`)
- **Audit Interface**: Scan item → Update status
- **Progress Tracking**: Items scanned vs total
- **Report**: Missing items list

### F. Profile & Settings (`/staff/profile`)
- **Profile View**: Personal info, photo
- **Google Account Link**: Connect/disconnect OAuth
- **Change Password**: Self-service password change

---

## 7. Multi-Branch Logic

### Middleware: `CheckStaffBranch`
```php
// Staff hanya bisa akses data branch sendiri
$query->where('branch_id', auth()->user()->branch_id);

// Super Admin: Branch selector di header
if (auth()->user()->isSuperAdmin()) {
    $branchId = session('staff_current_branch_id');
    // Show branch dropdown in header
}
```

---

## 8. Keyboard Shortcuts (Desktop)
| Shortcut | Action |
|----------|--------|
| `F1` | Scan Pinjam |
| `F2` | Scan Kembali |
| `Ctrl+M` | Cari Member |
| `Ctrl+B` | Cari Buku |
| `Ctrl+/` | Toggle Sidebar |
| `Esc` | Close Modal |

---

## 9. File Structure
```
resources/views/staff/
├── layouts/
│   └── app.blade.php          # Main layout (desktop + mobile)
├── components/
│   ├── sidebar.blade.php      # Desktop sidebar
│   ├── header.blade.php       # Desktop header
│   ├── mobile-header.blade.php
│   ├── mobile-nav.blade.php   # Bottom navigation
│   └── stats-card.blade.php
├── dashboard/
│   └── index.blade.php
├── circulation/
│   ├── index.blade.php        # POS interface
│   └── return.blade.php
├── books/
│   └── index.blade.php
├── members/
│   └── index.blade.php
├── stock-opname/
│   └── index.blade.php
└── profile/
    └── index.blade.php

public/css/
├── staff-portal.css           # Main styles
├── staff-desktop.css          # Desktop-only styles
└── staff-mobile.css           # Mobile-only styles

public/js/
├── staff-portal.js            # Alpine components
└── staff-scanner.js           # Barcode scanner logic
```

---

## 10. Implementation Priority

### Phase 1: Foundation
1. ✅ Layout & Auth (login detection, staff guard)
2. ✅ Desktop sidebar & header
3. ✅ Mobile header & bottom nav
4. ✅ Profile page with Google link

### Phase 2: Core Features
5. 🔲 Dashboard with stats
6. 🔲 Circulation module (POS interface)
7. 🔲 Barcode scanner integration

### Phase 3: Supporting Features
8. 🔲 Bibliography search & view
9. 🔲 Member management
10. 🔲 Stock opname

### Phase 4: Polish
11. 🔲 Keyboard shortcuts
12. 🔲 Sound feedback
13. 🔲 Print integration
14. 🔲 PWA/Offline support

---

## 11. Security Considerations

- **Session Timeout**: 8 jam untuk staff (lebih lama dari member)
- **Audit Trail**: Log semua transaksi dengan user_id & timestamp
- **Branch Isolation**: Middleware memastikan data isolation
- **CSRF Protection**: Semua form dengan @csrf
- **Rate Limiting**: Prevent brute force pada login

---

## 12. References

- **TAMS Teacher Portal**: Konsep dual-mode (desktop/mobile), sidebar collapse, bottom nav
- **SLiMS**: Workflow sirkulasi perpustakaan
- **POS Systems**: Interface transaksi cepat

---

*Last Updated: 2024-12-11*
*Author: Development Team*
