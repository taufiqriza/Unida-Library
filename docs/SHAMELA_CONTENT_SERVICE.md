# Shamela Content Reader Service

Setelah export selesai, service ini akan membaca konten buku dari SQLite.

## Database Schema (shamela_content.db)

```sql
CREATE TABLE pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    page_id TEXT,          -- Format: "bookId-pageNum" 
    book_id INTEGER,       -- Book ID dari master.db
    page_num INTEGER,      -- Nomor halaman
    body TEXT,             -- Konten teks Arabic
    foot TEXT              -- Footnotes
);

CREATE INDEX idx_book ON pages(book_id);
CREATE INDEX idx_book_page ON pages(book_id, page_num);
```

## Estimated Size
- Total pages: 7,358,148
- Estimated SQLite size: ~15-20GB

## Usage

```php
$service = new ShamelaContentService();

// Get page content
$page = $service->getPage($bookId, $pageNum);

// Get all pages for a book
$pages = $service->getBookPages($bookId);

// Search within a book
$results = $service->searchInBook($bookId, "query");
```
