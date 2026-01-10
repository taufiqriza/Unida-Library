<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchAiService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Analyze search results and provide AI summary
     */
    public function analyze(string $query, array $results, array $counts): ?array
    {
        if (!$this->isEnabled() || empty($query) || strlen($query) < 3) {
            return null;
        }

        $cacheKey = "search_ai:" . md5($query . json_encode($counts));
        
        return Cache::remember($cacheKey, 600, function () use ($query, $results, $counts) {
            try {
                $topTitles = collect($results)->take(5)->pluck('title')->implode(', ');
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(8)->post($this->baseUrl, [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Kamu asisten perpustakaan universitas. Jawab dalam bahasa Indonesia, singkat dan informatif. Output JSON saja tanpa markdown.'],
                        ['role' => 'user', 'content' => $this->buildPrompt($query, $counts, $topTitles)]
                    ],
                    'max_tokens' => 400,
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    return $this->parseResponse($response->json()['choices'][0]['message']['content'] ?? '');
                }
                
                return null;
            } catch (\Exception $e) {
                Log::warning('SearchAI failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    private function buildPrompt(string $query, array $counts, string $topTitles): string
    {
        return "Pencarian: \"{$query}\"
Hasil: {$counts['book']} buku, {$counts['ebook']} ebook, {$counts['ethesis']} tugas akhir, {$counts['journal']} jurnal
Contoh judul: {$topTitles}

Berikan JSON:
{\"summary\":\"ringkasan 1-2 kalimat tentang hasil pencarian\",\"tips\":[\"tip pencarian 1\",\"tip 2\"],\"related\":[\"topik terkait 1\",\"topik 2\",\"topik 3\"]}";
    }

    private function parseResponse(string $content): ?array
    {
        $content = preg_replace('/```json\s*|\s*```/', '', $content);
        
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $data = json_decode($matches[0], true);
            if ($data && isset($data['summary'])) {
                return [
                    'summary' => $data['summary'] ?? '',
                    'tips' => array_slice($data['tips'] ?? [], 0, 3),
                    'related' => array_slice($data['related'] ?? [], 0, 5),
                ];
            }
        }
        
        return null;
    }
}
