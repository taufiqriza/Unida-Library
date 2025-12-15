# Shamela Desktop Integration - Arsitektur & Implementasi

## ✅ Status: IMPLEMENTED

Database Shamela lokal sudah terintegrasi dengan sistem!

---

## 📊 Database Statistics

| Metric | Value |
|--------|-------|
| **Total Buku** | 8,425 |
| **Total Penulis** | 3,146 |
| **Total Kategori** | 41 |
| **Database Size** | ~1 GB |
| **Oldest Book** | 110 H (Fada'il Makkah - Hasan al-Basri) |

---

## 📦 Struktur Database Shamela Desktop

```
storage/database/
├── master.db                # Main catalog (books, authors, categories)
├── cover.db                 # Book covers (38 MB)
├── book/                    # 8,425 book content folders
│   ├── 000/                 # Books 0-999
│   │   ├── 1000.db          # Book content database
│   │   ├── 10000.db
│   │   └── ...
│   ├── 001/                 # Books 1000-1999
│   └── ... (1000 folders)
├── store/                   # Additional indexes
├── service/                 # Service data
├── update/                  # Update data
└── user/                    # User preferences
```

---

## 🏗️ Database Schema

### master.db Tables

#### `book` - Katalog Buku
```sql
book_id INTEGER PRIMARY KEY
book_name TEXT              -- Judul buku (Arabic)
book_category INTEGER       -- FK to category
book_type INTEGER           -- Tipe buku
book_date INTEGER           -- Tahun Hijriah (e.g., 256 = 256 H)
authors TEXT                -- Author IDs
main_author INTEGER         -- FK to author
printed INTEGER             -- Status cetak
pdf_links TEXT              -- JSON: {"files": ["https://archive.org/..."], "cover": 1}
pdf_online INTEGER          -- Has online PDF
cover_online INTEGER        -- Has online cover
meta_data TEXT              -- Additional JSON metadata
hidden INTEGER              -- Is hidden (0 = visible)
```

#### `author` - Penulis/Ulama
```sql
author_id INTEGER PRIMARY KEY
author_name TEXT            -- Nama (Arabic)
death_number INTEGER        -- Tahun wafat Hijriah
death_text TEXT             -- Tahun wafat teks
alpha INTEGER               -- Sort order
```

#### `category` - Kategori
```sql
category_id INTEGER PRIMARY KEY
category_name TEXT          -- Nama kategori (Arabic)
category_order INTEGER      -- Sort order
```

### Kategori Tersedia (41 Total)
1. العقيدة (Aqidah)
2. الفرق والردود (Firaq & Raddu)
3. التفسير (Tafsir)
4. علوم القرآن وأصول التفسير (Ulum al-Quran)
5. التجويد والقراءات (Tajwid & Qira'at)
6. كتب السنة (Kutub al-Sunnah / Hadith)
7. شروح الحديث (Syarah Hadith)
8. التخريج والأطراف (Takhrij)
9. العلل والسؤلات الحديثية (Ilal)
10. علوم الحديث (Ulum al-Hadith)
... dan 31 kategori lainnya

---

## 🏗️ Service Architecture

### Implemented Services

#### 1. `ShamelaLocalService` (NEW)
```php
App\Services\ShamelaLocalService

Methods:
- isAvailable(): bool              // Check if database exists
- getStats(): array                // Get total books, authors, categories
- getCategories(): array           // Get all 41 categories
- search(query, limit): array      // Full-text search
- getBook(id): array               // Get book detail
- getBooksByCategory(catId): array // Browse by category
- getFeaturedBooks(): array        // Random books with PDF
- getClassicBooks(): array         // Oldest/classic books
```

#### 2. `ShamelaService` (Updated)
```php
App\Services\ShamelaService

Priority:
1. Uses ShamelaLocalService FIRST (8,425 books, offline)
2. Falls back to web scraping from shamela.ws
3. Falls back to hardcoded popular books list
```

---

## 🔄 Data Flow

```
User Search: "صحيح البخاري"
        ↓
GlobalSearch (Livewire)
        ↓
ShamelaService::search()
        ↓
┌─────────────────────────────────────┐
│ 1. ShamelaLocalService (SQLite)     │ ← PREFERRED (fast, offline)
│    - Direct query to master.db      │
│    - Returns in ~100ms              │
├─────────────────────────────────────┤
│ 2. Web Scraping (shamela.ws)        │ ← FALLBACK (if local unavailable)
│    - HTTP request                   │
│    - 2-3 seconds                    │
├─────────────────────────────────────┤
│ 3. Hardcoded Popular Books          │ ← FALLBACK (if all fails)
└─────────────────────────────────────┘
        ↓
Results with:
- title (Arabic)
- author & death year
- category
- PDF links (Internet Archive)
- shamela.ws URL
```

---

## 📚 Sample Book Data

```json
{
    "id": 1680,
    "title": "صحيح البخاري",
    "author": "البخاري",
    "author_death": 256,
    "hijri_year": "256 هـ",
    "category": "كتب السنة",
    "category_id": 6,
    "has_pdf": true,
    "pdf_links": ["https://archive.org/download/..."],
    "cover": "https://shamela.ws/images/covers/1680.jpg",
    "url": "https://shamela.ws/book/1680",
    "source": "shamela-local"
}
```

---

## 🚀 Performance

| Operation | Local DB | Web Scraping |
|-----------|----------|--------------|
| Search | ~100ms | 2-3 seconds |
| Category Browse | ~50ms | 2-3 seconds |
| Book Detail | ~30ms | 1-2 seconds |
| Works Offline | ✅ Yes | ❌ No |

---

## 📋 Setup Instructions

### 1. Install Shamela Desktop (Windows)
Download from [shamela.ws](https://shamela.ws) and install.

### 2. Copy Database to Laravel
```bash
# Copy the database folder
cp -r "C:/Users/*/AppData/Local/shamela/database" storage/database/

# Or on Mac (if using Wine)
cp -r ~/.wine/drive_c/users/*/AppData/Local/shamela/database storage/database/
```

### 3. Verify Structure
```bash
ls storage/database/
# Should show: book/ cover.db master.db service/ store/ update/ user/
```

### 4. Clear Cache
```bash
php artisan cache:clear
```

---

## 🔒 Security Notes

1. **Database files are gitignored** - terlalu besar (~1GB)
2. **Read-only access** - database dibuka dengan `SQLITE3_OPEN_READONLY`
3. **No direct file access** - semua via Service layer
4. **Cached results** - mengurangi database queries

---

## 📈 Future Enhancements

### Phase 2: Reader Component
- [ ] Book page reader (lazy load dari book/*.db)
- [ ] Table of contents navigation
- [ ] Search within book

### Phase 3: Full-Text Search
- [ ] Meilisearch integration for Arabic text
- [ ] Advanced search filters

### Phase 4: User Features
- [ ] Bookmarks
- [ ] Reading history
- [ ] Notes & annotations

---

*Last Updated: December 15, 2024*
