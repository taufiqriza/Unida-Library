<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentFingerprint extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'content_text',
        'content_chunks',
        'content_hash',
        'word_count',
    ];

    protected $casts = [
        'content_chunks' => 'array',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate content hash from text
     */
    public static function hashContent(string $text): string
    {
        // Normalize text before hashing
        $normalized = self::normalizeText($text);
        return hash('sha256', $normalized);
    }

    /**
     * Normalize text for comparison
     */
    public static function normalizeText(string $text): string
    {
        // Lowercase
        $text = mb_strtolower($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        return trim($text);
    }

    /**
     * Split text into chunks for comparison
     */
    public static function chunkText(string $text, int $chunkSize = 100): array
    {
        $words = preg_split('/\s+/', $text);
        $chunks = [];
        
        for ($i = 0; $i < count($words); $i += $chunkSize) {
            $chunk = array_slice($words, $i, $chunkSize);
            if (count($chunk) >= 20) { // Minimum 20 words per chunk
                $chunks[] = implode(' ', $chunk);
            }
        }
        
        return $chunks;
    }

    /**
     * Count words in text
     */
    public static function countWords(string $text): int
    {
        return str_word_count($text, 0, 'àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ');
    }
}
