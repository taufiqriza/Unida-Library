<?php

namespace App\Http\Controllers;

use App\Models\Ethesis;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = view('sitemap.index')->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    public function ethesis(): Response
    {
        $etheses = Ethesis::where('is_public', true)
            ->orderByDesc('updated_at')
            ->get();
            
        $xml = view('sitemap.ethesis', compact('etheses'))->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    public function generate(): array
    {
        try {
            // Generate main sitemap
            $mainSitemap = view('sitemap.index')->render();
            \Storage::disk('public')->put('sitemap.xml', $mainSitemap);
            
            // Generate e-thesis sitemap  
            $etheses = Ethesis::where('is_public', true)->orderByDesc('updated_at')->get();
            $ethesisSitemap = view('sitemap.ethesis', compact('etheses'))->render();
            \Storage::disk('public')->put('sitemap-ethesis.xml', $ethesisSitemap);
            
            return [
                'main_sitemap' => 'sitemap.xml',
                'ethesis_sitemap' => 'sitemap-ethesis.xml',
                'total_urls' => $etheses->count() + 1,
                'urls' => [
                    asset('storage/sitemap.xml'),
                    asset('storage/sitemap-ethesis.xml')
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Sitemap generation error: ' . $e->getMessage());
            throw $e;
        }
    }
}
