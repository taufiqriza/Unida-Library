<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DdcClassification extends Model
{
    protected $fillable = ['code', 'description', 'description_en'];

    /**
     * Search DDC by code or description
     */
    public static function search(string $query, int $limit = 20)
    {
        return static::where('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('code')
            ->limit($limit)
            ->get();
    }

    /**
     * Get main classes (000-900)
     */
    public static function getMainClasses()
    {
        return static::whereRaw('LENGTH(code) = 3')
            ->whereRaw("code REGEXP '^[0-9]00$'")
            ->orderBy('code')
            ->get();
    }
}
