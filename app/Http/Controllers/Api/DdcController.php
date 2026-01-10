<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DdcService;
use App\Services\DdcRecommendationService;
use Illuminate\Http\Request;

class DdcController extends Controller
{
    public function search(Request $request, DdcService $ddcService)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 100), 500);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        return response()->json($ddcService->search($query, $limit));
    }

    public function recommend(Request $request, DdcRecommendationService $service)
    {
        $title = $request->get('title', '');
        
        if (strlen($title) < 3) {
            return response()->json(['error' => 'Title too short'], 400);
        }

        return response()->json($service->analyze($title));
    }

    public function mainClasses()
    {
        return response()->json([
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
        ]);
    }
}
