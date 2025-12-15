<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use SQLite3;

/**
 * ShamelaContentService - Membaca konten buku Shamela dari SQLite
 * 
 * Database ini di-generate dari Lucene index Shamela Desktop
 * berisi 7+ juta halaman dari 8,425 kitab Islam.
 */
class ShamelaContentService
{
    protected ?SQLite3 $db = null;
    protected string $dbPath;
    
    public function __construct()
    {
        $this->dbPath = storage_path('database/shamela_content.db');
    }
    
    /**
     * Check if content database is available
     */
    public function isAvailable(): bool
    {
        return file_exists($this->dbPath);
    }
    
    /**
     * Get database connection
     */
    protected function getDb(): ?SQLite3
    {
        if (!$this->isAvailable()) {
            return null;
        }
        
        if ($this->db === null) {
            $this->db = new SQLite3($this->dbPath, SQLITE3_OPEN_READONLY);
            $this->db->busyTimeout(5000);
        }
        
        return $this->db;
    }
    
    /**
     * Get statistics about the content database
     */
    public function getStats(): array
    {
        return Cache::remember('shamela_content_stats', 3600, function () {
            $db = $this->getDb();
            if (!$db) {
                return ['available' => false];
            }
            
            return [
                'available' => true,
                'total_pages' => $db->querySingle('SELECT COUNT(*) FROM pages'),
                'total_books' => $db->querySingle('SELECT COUNT(DISTINCT book_id) FROM pages'),
                'database_size_mb' => round(filesize($this->dbPath) / 1024 / 1024, 1),
            ];
        });
    }
    
    /**
     * Get a specific page of a book
     */
    public function getPage(int $bookId, int $pageNum): ?array
    {
        $db = $this->getDb();
        if (!$db) {
            return null;
        }
        
        $stmt = $db->prepare('SELECT * FROM pages WHERE book_id = :book_id AND page_num = :page_num LIMIT 1');
        $stmt->bindValue(':book_id', $bookId, SQLITE3_INTEGER);
        $stmt->bindValue(':page_num', $pageNum, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }
    
    /**
     * Get all pages for a book (for reader)
     */
    public function getBookPages(int $bookId, int $limit = 1000, int $offset = 0): array
    {
        $db = $this->getDb();
        if (!$db) {
            return [];
        }
        
        $stmt = $db->prepare('SELECT * FROM pages WHERE book_id = :book_id ORDER BY page_num LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':book_id', $bookId, SQLITE3_INTEGER);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $pages = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $pages[] = $row;
        }
        
        return $pages;
    }
    
    /**
     * Get page count for a book
     */
    public function getBookPageCount(int $bookId): int
    {
        $db = $this->getDb();
        if (!$db) {
            return 0;
        }
        
        $stmt = $db->prepare('SELECT COUNT(*) FROM pages WHERE book_id = :book_id');
        $stmt->bindValue(':book_id', $bookId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        return (int) $result->fetchArray()[0];
    }
    
    /**
     * Search within a specific book
     */
    public function searchInBook(int $bookId, string $query, int $limit = 50): array
    {
        $db = $this->getDb();
        if (!$db || empty($query)) {
            return [];
        }
        
        // Use LIKE for simple search
        $stmt = $db->prepare('SELECT * FROM pages WHERE book_id = :book_id AND body LIKE :query ORDER BY page_num LIMIT :limit');
        $stmt->bindValue(':book_id', $bookId, SQLITE3_INTEGER);
        $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $pages = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Add snippet with highlighted query
            $body = $row['body'];
            $pos = mb_stripos($body, $query);
            if ($pos !== false) {
                $start = max(0, $pos - 50);
                $row['snippet'] = '...' . mb_substr($body, $start, 150) . '...';
            } else {
                $row['snippet'] = mb_substr($body, 0, 100) . '...';
            }
            $pages[] = $row;
        }
        
        return $pages;
    }
    
    /**
     * Global search across all books (limited)
     */
    public function search(string $query, int $limit = 100): array
    {
        $db = $this->getDb();
        if (!$db || empty($query)) {
            return [];
        }
        
        $stmt = $db->prepare('SELECT * FROM pages WHERE body LIKE :query LIMIT :limit');
        $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $pages = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $body = $row['body'];
            $pos = mb_stripos($body, $query);
            if ($pos !== false) {
                $start = max(0, $pos - 50);
                $row['snippet'] = '...' . mb_substr($body, $start, 150) . '...';
            }
            $pages[] = $row;
        }
        
        return $pages;
    }
    
    /**
     * Get first and last page numbers for a book
     */
    public function getBookPageRange(int $bookId): array
    {
        $db = $this->getDb();
        if (!$db) {
            return ['min' => 1, 'max' => 1];
        }
        
        $min = $db->querySingle("SELECT MIN(page_num) FROM pages WHERE book_id = $bookId");
        $max = $db->querySingle("SELECT MAX(page_num) FROM pages WHERE book_id = $bookId");
        
        return [
            'min' => $min ?: 1,
            'max' => $max ?: 1,
        ];
    }
    
    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }
}
