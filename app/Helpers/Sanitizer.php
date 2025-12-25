<?php

namespace App\Helpers;

class Sanitizer
{
    /**
     * Sanitize string for safe database queries
     */
    public static function string(?string $input): ?string
    {
        if ($input === null) return null;
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        return $input;
    }

    /**
     * Sanitize for search queries (escape LIKE wildcards)
     */
    public static function searchQuery(?string $input): ?string
    {
        if ($input === null) return null;
        
        $input = self::string($input);
        
        // Escape LIKE wildcards
        $input = str_replace(['%', '_'], ['\%', '\_'], $input);
        
        return $input;
    }

    /**
     * Sanitize filename
     */
    public static function filename(string $filename): string
    {
        // Remove path traversal
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Prevent double extensions
        $filename = preg_replace('/\.+/', '.', $filename);
        
        return $filename;
    }

    /**
     * Sanitize for HTML output (XSS prevention)
     */
    public static function html(?string $input): ?string
    {
        if ($input === null) return null;
        
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
