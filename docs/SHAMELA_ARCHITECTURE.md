# Shamela Desktop Integration - Arsitektur & Konsep

## 📦 Struktur Database Shamela Desktop

Berdasarkan explorasi awal, struktur Shamela Desktop:

```
shamela.full.1446.1/data/
├── app/                     # Aplikasi files
├── database/
│   ├── book/               # 1000 folders (sharded by ID)
│   │   ├── 000/            # Books 0-999
│   │   ├── 001/            # Books 1000-1999
│   │   └── ...
│   ├── store/              # Master indexes
│   │   ├── author/         # Authors data
│   │   ├── book/           # Book metadata
│   │   ├── page/           # Pages content
│   │   ├── title/          # Table of contents
│   │   ├── aya/            # Quran verses
│   │   ├── esnad/          # Narration chains
│   │   ├── s_author/       # Author search index
│   │   └── s_book/         # Book search index
│   ├── service/            # Service data
│   ├── update/             # Update data
│   └── user/               # User preferences
└── shamela.bin             # Original archive (12GB)
```

## 🏗️ Arsitektur yang Direkomendasikan

### 1. Struktur Data Laravel

```
app/
├── Models/
│   ├── ShamelaBook.php          # Book metadata
│   ├── ShamelaAuthor.php        # Authors
│   ├── ShamelaPage.php          # Book pages/content
│   └── ShamelaTitle.php         # Table of contents
├── Services/
│   └── ShamelaLocalService.php  # Read from local DB
├── Livewire/Opac/
│   └── ShamelaShow.php          # Book detail page
└── Http/Controllers/
    └── ShamelaReaderController.php  # Protected reading API
```

### 2. Database Migration

```php
// shamela_books table
Schema::create('shamela_books', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('shamela_id')->unique(); // Original Shamela ID
    $table->string('title');
    $table->string('author_name')->nullable();
    $table->unsignedBigInteger('author_id')->nullable();
    $table->unsignedInteger('category_id')->nullable();
    $table->string('category_name')->nullable();
    $table->text('description')->nullable();
    $table->unsignedInteger('page_count')->default(0);
    $table->unsignedInteger('volume_count')->default(0);
    $table->string('cover_path')->nullable();
    $table->boolean('is_searchable')->default(true);
    $table->timestamps();
    
    $table->index(['author_id', 'category_id']);
    $table->fullText(['title', 'author_name']);
});

// shamela_authors table
Schema::create('shamela_authors', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('shamela_id')->unique();
    $table->string('name');
    $table->string('death_year')->nullable(); // وفاة
    $table->text('bio')->nullable();
    $table->timestamps();
});

// shamela_categories table
Schema::create('shamela_categories', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('shamela_id')->unique();
    $table->string('name');
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->unsignedInteger('book_count')->default(0);
    $table->timestamps();
});
```

### 3. Storage Strategy

**Opsi A: Import ke MySQL (Recommended)**
- Import metadata (books, authors, categories) ke MySQL
- Konten halaman tetap di file Shamela (lazy load)
- Pencarian cepat, integrasi Meilisearch

**Opsi B: Direct SQLite Access**
- Langsung baca file .db Shamela
- Tidak perlu import
- Lebih lambat tapi simple

**Rekomendasi: Opsi A (Hybrid)**
- Import metadata → MySQL untuk search
- Konten halaman → Read langsung dari file Shamela

### 4. User Flow

```
[Global Search]
    ↓
[ketik "صحيح البخاري"]
    ↓
[Tab Shamela] → Results dari MySQL (cepat)
    ↓
[Klik hasil]
    ↓
[ShamelaShow] → Detail page
    ↓
[Baca Online] → Reader component (lazy load pages)
    ↓
[Page content] → Loaded from Shamela files
```

### 5. Security (Anti-Download)

```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    // Page content only via authenticated API
    Route::get('/shamela/page/{bookId}/{pageNum}', [ShamelaReaderController::class, 'page'])
        ->name('shamela.page')
        ->middleware('throttle:30,1'); // Rate limit
});

// ShamelaReaderController.php
public function page(int $bookId, int $pageNum)
{
    // Return content as HTML, not downloadable
    $content = $this->shamelaService->getPage($bookId, $pageNum);
    
    return response()->json([
        'content' => $content,
        'page' => $pageNum,
    ])->header('X-Robots-Tag', 'noindex, nofollow');
}
```

### 6. Reader UI Concept

- **Online reader** seperti Google Books
- **No raw text download** - konten di-render sebagai HTML
- **Copy protection** via CSS `user-select: none` + JS event blockers
- **Watermark** dengan username pengguna
- **Session-based access** - logout = tidak bisa baca

### 7. Directory Structure (Storage)

```
storage/
├── app/
│   └── shamela/
│       └── symlink → ../../../shamela.full.1446.1/data/database
```

## 📋 Implementation Steps

### Phase 1: Import Metadata (Hari 1)
1. Analisis format database Shamela
2. Buat script import metadata
3. Migrate ke MySQL

### Phase 2: Search Integration (Hari 2)
1. Index ke Meilisearch
2. Update GlobalSearch untuk Shamela
3. Test search functionality

### Phase 3: Reader Component (Hari 3-4)
1. Buat Livewire reader component
2. Implement lazy page loading
3. Add anti-copy protection
4. Style dengan tema emerald

### Phase 4: Polish (Hari 5)
1. Add author pages
2. Add category browsing
3. Performance optimization
4. Testing

---

## 🔐 Security Measures

1. **No direct file access** - semua via controller
2. **Authentication required** - hanya member
3. **Rate limiting** - max 30 pages/minute
4. **Session validation** - setiap request divalidasi
5. **Watermarking** - username di setiap halaman
6. **CSS protection** - `user-select: none`
7. **JS protection** - block right-click, print screen
8. **Image rendering** - text bisa di-render sebagai image untuk anti-copy

---

*Dokumen ini akan diupdate setelah analisis penuh database Shamela selesai.*
