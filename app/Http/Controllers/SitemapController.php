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
            $tempMain = tempnam(sys_get_temp_dir(), 'sitemap_main_');
            file_put_contents($tempMain, $mainSitemap);
            
            // Generate e-thesis sitemap  
            $etheses = Ethesis::where('is_public', true)->orderByDesc('updated_at')->get();
            $ethesisSitemap = view('sitemap.ethesis', compact('etheses'))->render();
            $tempEthesis = tempnam(sys_get_temp_dir(), 'sitemap_ethesis_');
            file_put_contents($tempEthesis, $ethesisSitemap);
            
            // Move to public directory with proper permissions
            $publicPath = public_path();
            $mainTarget = $publicPath . '/sitemap.xml';
            $ethesisTarget = $publicPath . '/sitemap-ethesis.xml';
            
            // Use copy and unlink for better permission handling
            if (copy($tempMain, $mainTarget)) {
                chmod($mainTarget, 0644);
                unlink($tempMain);
            }
            
            if (copy($tempEthesis, $ethesisTarget)) {
                chmod($ethesisTarget, 0644);
                unlink($tempEthesis);
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
