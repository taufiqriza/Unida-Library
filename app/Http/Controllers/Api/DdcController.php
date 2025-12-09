<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DdcClassification;
use Illuminate\Http\Request;

class DdcController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 20), 50);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = DdcClassification::where('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('code')
            ->limit($limit)
            ->get(['id', 'code', 'description']);

        return response()->json($results);
    }

    public function mainClasses()
    {
        $mainClasses = [
            ['code' => '000', 'description' => 'Karya Umum, Komputer, Informasi'],
            ['code' => '100', 'description' => 'Filsafat & Psikologi'],
            ['code' => '200', 'description' => 'Agama'],
            ['code' => '300', 'description' => 'Ilmu Sosial'],
            ['code' => '400', 'description' => 'Bahasa'],
            ['code' => '500', 'description' => 'Sains & Matematika'],
            ['code' => '600', 'description' => 'Teknologi'],
            ['code' => '700', 'description' => 'Seni & Olahraga'],
            ['code' => '800', 'description' => 'Sastra'],
            ['code' => '900', 'description' => 'Sejarah & Geografi'],
        ];

        return response()->json($mainClasses);
    }
}
