<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use SQLite3;

/**
 * ShamelaLocalService - Read from local Shamela Desktop database
 * 
 * Database structure:
 * - master.db: Main catalog with books, authors, categories
 * - book/XXX/YYYY.db: Individual book content (pages, titles)
 * - cover.db: Book covers
 */
class ShamelaLocalService
{
    protected ?SQLite3 $masterDb = null;
    protected ?SQLite3 $coverDb = null;
    protected string $basePath;
    protected bool $isAvailable = false;

    public function __construct()
    {
        $this->basePath = storage_path('database');
        $this->isAvailable = file_exists($this->basePath . '/master.db');
    }

    /**
     * Check if local Shamela database is available
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Get master database connection
     */
    protected function getMasterDb(): ?SQLite3
    {
        if (!$this->isAvailable) return null;
        
        if (!$this->masterDb) {
            try {
                $this->masterDb = new SQLite3($this->basePath . '/master.db', SQLITE3_OPEN_READONLY);
                $this->masterDb->enableExceptions(true);
            } catch (\Exception $e) {
                \Log::error('Shamela: Failed to open master.db: ' . $e->getMessage());
                return null;
            }
        }
        return $this->masterDb;
    }

    /**
     * Get cover database connection
     */
    protected function getCoverDb(): ?SQLite3
    {
        if (!$this->isAvailable) return null;
        
        if (!$this->coverDb) {
            try {
                $coverPath = $this->basePath . '/cover.db';
                if (file_exists($coverPath)) {
                    $this->coverDb = new SQLite3($coverPath, SQLITE3_OPEN_READONLY);
                }
            } catch (\Exception $e) {
                \Log::warning('Shamela: Failed to open cover.db: ' . $e->getMessage());
            }
        }
        return $this->coverDb;
    }

    /**
     * Get book database path
     */
    protected function getBookDbPath(int $bookId): ?string
    {
        // Books are stored in XXX/YYYYYY.db format
        // Example: book_id 1000 -> book/000/1000.db
        // Example: book_id 12345 -> book/012/12000.db (grouped by thousands)
        
        $thousands = intdiv($bookId, 1000);
        $dir = str_pad($thousands, 3, '0', STR_PAD_LEFT);
        $dbFile = ($thousands * 1000) . '.db';
        
        $path = $this->basePath . '/book/' . $dir . '/' . $dbFile;
        
        if (!file_exists($path)) {
            // Try exact book ID
            $dbFile = $bookId . '.db';
            $path = $this->basePath . '/book/' . $dir . '/' . $dbFile;
        }
        
        return file_exists($path) ? $path : null;
    }

    /**
     * Get statistics about the database
     */
    public function getStats(): array
    {
        return Cache::remember('shamela_local_stats', 3600, function () {
            $db = $this->getMasterDb();
            if (!$db) return ['available' => false];

            return [
                'available' => true,
                'total_books' => (int) $db->querySingle('SELECT COUNT(*) FROM book WHERE hidden = 0'),
                'total_authors' => (int) $db->querySingle('SELECT COUNT(*) FROM author'),
                'total_categories' => (int) $db->querySingle('SELECT COUNT(*) FROM category'),
                'database_path' => $this->basePath,
            ];
        });
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return Cache::remember('shamela_local_categories', 3600, function () {
            $db = $this->getMasterDb();
            if (!$db) return [];

            $categories = [];
            $result = $db->query('SELECT category_id, category_name, category_order FROM category ORDER BY category_order');
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $categories[] = [
                    'id' => $row['category_id'],
                    'name' => $row['category_name'],
                    'order' => $row['category_order'],
                ];
            }
            
            return $categories;
        });
    }

    /**
     * Search books
     */
    public function search(string $query, int $limit = 20, int $offset = 0, ?int $categoryId = null): array
    {
        $db = $this->getMasterDb();
        if (!$db) return ['results' => [], 'total' => 0];

        $cacheKey = 'shamela_search_' . md5($query . $limit . $offset . $categoryId);
        
        return Cache::remember($cacheKey, 300, function () use ($db, $query, $limit, $offset, $categoryId) {
            $searchTerm = '%' . $query . '%';
            
            // Build query
            $sql = "SELECT 
                        b.book_id, 
                        b.book_name, 
                        b.book_category,
                        b.book_date,
                        b.authors,
                        b.main_author,
                        b.pdf_links,
                        b.cover_online,
                        c.category_name,
                        a.author_name,
                        a.death_number
                    FROM book b
                    LEFT JOIN category c ON b.book_category = c.category_id
                    LEFT JOIN author a ON b.main_author = a.author_id
                    WHERE b.hidden = 0 AND (
                        b.book_name LIKE :query
                        OR a.author_name LIKE :query
                    )";
            
            if ($categoryId) {
                $sql .= " AND b.book_category = :category";
            }
            
            $sql .= " ORDER BY b.book_name LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':query', $searchTerm, SQLITE3_TEXT);
            $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
            $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
            
            if ($categoryId) {
                $stmt->bindValue(':category', $categoryId, SQLITE3_INTEGER);
            }
            
            $result = $stmt->execute();
            $books = [];
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $books[] = $this->formatBook($row);
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM book b
                         LEFT JOIN author a ON b.main_author = a.author_id
                         WHERE b.hidden = 0 AND (
                             b.book_name LIKE :query
                             OR a.author_name LIKE :query
                         )";
            
            if ($categoryId) {
                $countSql .= " AND b.book_category = :category";
            }
            
            $countStmt = $db->prepare($countSql);
            $countStmt->bindValue(':query', $searchTerm, SQLITE3_TEXT);
            if ($categoryId) {
                $countStmt->bindValue(':category', $categoryId, SQLITE3_INTEGER);
            }
            
            $total = (int) $countStmt->execute()->fetchArray()[0];
            
            return [
                'results' => $books,
                'total' => $total,
                'query' => $query,
            ];
        });
    }

    /**
     * Get book by ID
     */
    public function getBook(int $bookId): ?array
    {
        $db = $this->getMasterDb();
        if (!$db) return null;

        $cacheKey = 'shamela_book_' . $bookId;
        
        return Cache::remember($cacheKey, 3600, function () use ($db, $bookId) {
            $stmt = $db->prepare("
                SELECT 
                    b.*, 
                    c.category_name,
                    a.author_name,
                    a.death_number,
                    a.death_text
                FROM book b
                LEFT JOIN category c ON b.book_category = c.category_id
                LEFT JOIN author a ON b.main_author = a.author_id
                WHERE b.book_id = :id
            ");
            $stmt->bindValue(':id', $bookId, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            
            if (!$row) return null;
            
            return $this->formatBook($row, true);
        });
    }

    /**
     * Get books by category
     */
    public function getBooksByCategory(int $categoryId, int $limit = 20, int $offset = 0): array
    {
        $db = $this->getMasterDb();
        if (!$db) return ['results' => [], 'total' => 0];

        $stmt = $db->prepare("
            SELECT 
                b.book_id, 
                b.book_name, 
                b.book_category,
                b.book_date,
                b.authors,
                b.main_author,
                b.pdf_links,
                b.cover_online,
                c.category_name,
                a.author_name,
                a.death_number
            FROM book b
            LEFT JOIN category c ON b.book_category = c.category_id
            LEFT JOIN author a ON b.main_author = a.author_id
            WHERE b.hidden = 0 AND b.book_category = :category
            ORDER BY b.book_name
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindValue(':category', $categoryId, SQLITE3_INTEGER);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $books = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $books[] = $this->formatBook($row);
        }
        
        // Get total
        $total = (int) $db->querySingle("SELECT COUNT(*) FROM book WHERE hidden = 0 AND book_category = $categoryId");
        
        return [
            'results' => $books,
            'total' => $total,
        ];
    }

    /**
     * Get random/featured books
     */
    public function getFeaturedBooks(int $limit = 12): array
    {
        $db = $this->getMasterDb();
        if (!$db) return [];

        $cacheKey = 'shamela_featured_' . date('Y-m-d');

        return Cache::remember($cacheKey, 3600, function () use ($db, $limit) {
            $stmt = $db->prepare("
                SELECT 
                    b.book_id, 
                    b.book_name, 
                    b.book_category,
                    b.book_date,
                    b.main_author,
                    b.pdf_links,
                    b.cover_online,
                    c.category_name,
                    a.author_name,
                    a.death_number
                FROM book b
                LEFT JOIN category c ON b.book_category = c.category_id
                LEFT JOIN author a ON b.main_author = a.author_id
                WHERE b.hidden = 0 AND b.pdf_online = 1
                ORDER BY RANDOM()
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
            $result = $stmt->execute();
            
            $books = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $books[] = $this->formatBook($row);
            }
            
            return $books;
        });
    }

    /**
     * Get popular/classic books (old books with PDF available)
     */
    public function getClassicBooks(int $limit = 20): array
    {
        $db = $this->getMasterDb();
        if (!$db) return [];

        return Cache::remember('shamela_classics', 3600, function () use ($db, $limit) {
            $stmt = $db->prepare("
                SELECT 
                    b.book_id, 
                    b.book_name, 
                    b.book_category,
                    b.book_date,
                    b.main_author,
                    b.pdf_links,
                    b.cover_online,
                    c.category_name,
                    a.author_name,
                    a.death_number
                FROM book b
                LEFT JOIN category c ON b.book_category = c.category_id
                LEFT JOIN author a ON b.main_author = a.author_id
                WHERE b.hidden = 0 
                    AND b.book_date > 0 
                    AND b.book_date < 1000
                    AND b.pdf_online = 1
                ORDER BY b.book_date ASC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
            $result = $stmt->execute();
            
            $books = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $books[] = $this->formatBook($row);
            }
            
            return $books;
        });
    }

    /**
     * Format book data for API response
     */
    protected function formatBook(array $row, bool $detailed = false): array
    {
        $pdfLinks = [];
        $coverUrl = null;
        
        // Parse PDF links JSON
        if (!empty($row['pdf_links'])) {
            $pdfData = json_decode($row['pdf_links'], true);
            if (isset($pdfData['files'])) {
                $pdfLinks = $pdfData['files'];
            }
            if (isset($pdfData['cover']) && $pdfData['cover']) {
                // Cover from Internet Archive
                $coverUrl = 'https://shamela.ws/images/covers/' . $row['book_id'] . '.jpg';
            }
        }
        
        // Fallback cover
        if (!$coverUrl) {
            $coverUrl = 'https://ui-avatars.com/api/?name=' . urlencode(mb_substr($row['book_name'], 0, 2)) 
                      . '&background=059669&color=fff&size=200&font-size=0.5&bold=true';
        }

        $book = [
            'id' => $row['book_id'],
            'title' => $row['book_name'],
            'author' => $row['author_name'] ?? null,
            'author_death' => $row['death_number'] ?? null,
            'category' => $row['category_name'] ?? null,
            'category_id' => $row['book_category'] ?? null,
            'year' => ($row['book_date'] ?? 0) < 2000 ? ($row['book_date'] ?? null) : null,
            'hijri_year' => ($row['book_date'] ?? 0) < 2000 ? ($row['book_date'] . ' هـ') : null,
            'cover' => $coverUrl,
            'has_pdf' => !empty($pdfLinks),
            'pdf_links' => $pdfLinks,
            'source' => 'shamela-local',
            'url' => 'https://shamela.ws/book/' . $row['book_id'],
        ];

        if ($detailed) {
            $book['death_text'] = $row['death_text'] ?? null;
            $book['meta_data'] = json_decode($row['meta_data'] ?? '{}', true);
        }

        return $book;
    }

    /**
     * Destructor - close database connections
     */
    public function __destruct()
    {
        if ($this->masterDb) {
            $this->masterDb->close();
        }
        if ($this->coverDb) {
            $this->coverDb->close();
        }
    }
}
