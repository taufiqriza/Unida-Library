<?php

namespace App\Services;

class CallNumberService
{
    /**
     * Generate call number from book data
     * Pattern: [CollectionCode] [Classification] [AuthorCode] [TitleCode]
     * Example: S 2X9.12 TIR M
     */
    public static function generate(
        ?string $collectionCode,
        ?string $classification,
        ?string $authorName,
        ?string $title
    ): string {
        $parts = [];

        // Collection code (e.g., S for Sirkulasi, R for Referensi)
        if ($collectionCode) {
            $parts[] = strtoupper($collectionCode);
        }

        // Classification number (DDC)
        if ($classification) {
            $parts[] = $classification;
        }

        // Author code (3 huruf pertama nama belakang/utama)
        if ($authorName) {
            $parts[] = self::getAuthorCode($authorName);
        }

        // Title code (huruf pertama, skip artikel)
        if ($title) {
            $parts[] = self::getTitleCode($title);
        }

        return implode("\n", $parts);
    }

    /**
     * Get author code (3 huruf pertama)
     */
    public static function getAuthorCode(?string $authorName): string
    {
        if (!$authorName) return '';
        
        // Ambil nama pertama/utama
        $name = trim($authorName);
        
        // Jika ada koma, ambil bagian sebelum koma (nama belakang)
        if (str_contains($name, ',')) {
            $name = trim(explode(',', $name)[0]);
        }
        
        // Jika ada spasi, ambil kata pertama
        if (str_contains($name, ' ')) {
            $words = explode(' ', $name);
            // Ambil kata terakhir (biasanya nama keluarga)
            $name = end($words);
        }
        
        // Ambil 3 huruf pertama, uppercase
        return strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
    }

    /**
     * Get title code (huruf pertama, skip artikel)
     */
    public static function getTitleCode(?string $title): string
    {
        if (!$title) return '';
        
        $title = trim($title);
        
        // Skip artikel di awal
        $articles = ['the', 'a', 'an', 'al-', 'al ', 'si ', 'sang '];
        $lowerTitle = strtolower($title);
        
        foreach ($articles as $article) {
            if (str_starts_with($lowerTitle, $article)) {
                $title = trim(substr($title, strlen($article)));
                break;
            }
        }
        
        // Ambil huruf pertama, uppercase
        $firstChar = substr(preg_replace('/[^a-zA-Z0-9]/', '', $title), 0, 1);
        
        return strtoupper($firstChar);
    }

    /**
     * Format call number for display (single line)
     */
    public static function formatSingleLine(string $callNumber): string
    {
        return str_replace("\n", ' ', $callNumber);
    }

    /**
     * Parse call number parts
     */
    public static function parse(string $callNumber): array
    {
        $lines = explode("\n", $callNumber);
        
        return [
            'collection_code' => $lines[0] ?? '',
            'classification' => $lines[1] ?? '',
            'author_code' => $lines[2] ?? '',
            'title_code' => $lines[3] ?? '',
        ];
    }
}
