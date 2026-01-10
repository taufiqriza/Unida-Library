<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DdcService;
use Illuminate\Http\Request;

class DdcController extends Controller
{
    public function search(Request $request, DdcService $ddcService)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 100), 500);

        // Allow single digit for main class search (0-9)
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $results = $ddcService->search($query, $limit);

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
