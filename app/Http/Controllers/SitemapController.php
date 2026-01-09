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
            file_put_contents(public_path('sitemap.xml'), $mainSitemap);
            
            // Generate e-thesis sitemap  
            $etheses = Ethesis::where('is_public', true)->orderByDesc('updated_at')->get();
            $ethesisSitemap = view('sitemap.ethesis', compact('etheses'))->render();
            file_put_contents(public_path('sitemap-ethesis.xml'), $ethesisSitemap);
            
            // Ensure proper permissions
            if (file_exists(public_path('sitemap.xml'))) {
                chmod(public_path('sitemap.xml'), 0644);
            }
            if (file_exists(public_path('sitemap-ethesis.xml'))) {
                chmod(public_path('sitemap-ethesis.xml'), 0644);
            }
            
            return [
                'main_sitemap' => 'sitemap.xml',
                'ethesis_sitemap' => 'sitemap-ethesis.xml',
                'total_urls' => $etheses->count() + 1,
            ];
        } catch (\Exception $e) {
            \Log::error('Sitemap generation error: ' . $e->getMessage());
            throw $e;
        }
    }
}
