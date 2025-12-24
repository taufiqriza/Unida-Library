<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'title', 'sor', 'publisher_id', 'publish_year',
        'isbn', 'edition', 'pages', 'file_size', 'file_format', 'file_path',
        'file_source', 'google_drive_id', 'google_drive_url',
        'cover_image', 'language', 'abstract', 'classification', 'call_number',
        'media_type_id', 'content_type_id', 'digital_category_id', 'collection_type',
        'access_type', 'download_count', 'view_count', 'is_active', 'opac_hide',
        'is_downloadable'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_downloadable' => 'boolean',
        'opac_hide' => 'boolean',
    ];

    // Relations
    public function branch() { return $this->belongsTo(Branch::class); }
    public function publisher() { return $this->belongsTo(Publisher::class); }
    public function authors() { return $this->belongsToMany(Author::class, 'ebook_author'); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'ebook_subject'); }
    public function downloads() { return $this->hasMany(EbookDownload::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function digitalCategory() { return $this->belongsTo(DigitalCategory::class); }
    public function mediaType() { return $this->belongsTo(MediaType::class); }
    public function contentType() { return $this->belongsTo(ContentType::class); }

    // Scopes
    public function scopeUniversitaria($query)
    {
        return $query->where('collection_type', 'universitaria');
    }

    public function scopeRegular($query)
    {
        return $query->whereNull('collection_type')->orWhere('collection_type', 'regular');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('opac_hide', false);
    }

    // Accessors
    public function getAuthorNamesAttribute(): string
    {
        return $this->authors->pluck('name')->implode('; ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    /**
     * Get the viewable URL for the ebook file
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_source === 'google_drive' && $this->google_drive_id) {
            return null; // Use getViewerUrl or getDownloadUrl instead
        }
        
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Get Google Drive preview/embed URL
     */
    public function getViewerUrlAttribute(): ?string
    {
        if ($this->file_source === 'google_drive' && $this->google_drive_id) {
            // Check if it's a folder (multi-part books) or file
            if ($this->google_drive_url && str_contains($this->google_drive_url, '/folders/')) {
                // Folder - open in Google Drive directly
                return "https://drive.google.com/drive/folders/{$this->google_drive_id}";
            }
            // File - use preview
            return "https://drive.google.com/file/d/{$this->google_drive_id}/preview";
        }
        
        // For local files, use Google Docs Viewer
        if ($this->file_path) {
            $fileUrl = asset('storage/' . $this->file_path);
            return "https://docs.google.com/viewer?url=" . urlencode($fileUrl) . "&embedded=true";
        }
        
        return null;
    }

    /**
     * Get download URL for ebook file
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_source === 'google_drive' && $this->google_drive_id) {
            return "https://drive.google.com/uc?export=download&id={$this->google_drive_id}";
        }
        
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Check if file is from Google Drive
     */
    public function isGoogleDrive(): bool
    {
        return $this->file_source === 'google_drive';
    }

    public function isUniversitaria(): bool
    {
        return $this->collection_type === 'universitaria';
    }

    /**
     * Extract Google Drive file ID from various URL formats
     */
    public static function extractGoogleDriveId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Format: https://drive.google.com/file/d/FILE_ID/view
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: https://drive.google.com/open?id=FILE_ID
        if (preg_match('/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: https://docs.google.com/document/d/FILE_ID/
        if (preg_match('/docs\.google\.com\/\w+\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Direct ID (already extracted)
        if (preg_match('/^[a-zA-Z0-9_-]{25,}$/', $url)) {
            return $url;
        }

        return null;
    }
}
