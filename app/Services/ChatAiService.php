<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChatAiService
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
     * Generate AI response for support chat
     */
    public function generateResponse(string $question, string $topic, array $context = []): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $cacheKey = "chat_ai:" . md5($question . $topic);
        
        return Cache::remember($cacheKey, 300, function () use ($question, $topic, $context) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(10)->post($this->baseUrl, [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => $this->getSystemPrompt()],
                        ['role' => 'user', 'content' => $this->buildPrompt($question, $topic, $context)]
                    ],
                    'max_tokens' => 400,
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    $content = $response->json()['choices'][0]['message']['content'] ?? '';
                    return $this->formatResponse($content);
                }
                
                return null;
            } catch (\Exception $e) {
                Log::warning('ChatAI failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    private function getSystemPrompt(): string
    {
        return "Kamu adalah UNIDA Library AI, asisten virtual perpustakaan Universitas Darussalam Gontor.

PANDUAN:
- Jawab dalam bahasa Indonesia yang ramah dan informatif
- Gunakan emoji secukupnya untuk kesan friendly
- Jawaban singkat dan jelas (max 3-4 paragraf)
- Jika tidak yakin, sarankan untuk menghubungi pustakawan dengan ketik 'staff'
- Fokus pada layanan perpustakaan: peminjaman, pengembalian, unggah karya ilmiah, cek plagiasi, bebas pustaka

INFO PERPUSTAKAAN UNIDA GONTOR:
- Jam buka: Senin-Kamis 08:00-16:00, Sabtu-Minggu 08:00-21:00
- Lokasi: Kampus UNIDA Gontor, Siman, Ponorogo
- Website: library.unida.gontor.ac.id
- Layanan: Peminjaman buku, E-Library, Repository, Cek Plagiasi, Bebas Pustaka
- Batas pinjam: Mahasiswa 3 buku (7 hari), Dosen 5 buku (14 hari)
- Denda keterlambatan: Rp 500/buku/hari
- Unggah mandiri: Upload skripsi/tesis di menu Member > Unggah Mandiri
- Cek plagiasi: Gratis untuk mahasiswa UNIDA, max 3x pengecekan
- Bebas pustaka: Ajukan online, proses 1-3 hari kerja";
    }

    private function buildPrompt(string $question, string $topic, array $context): string
    {
        $topicLabels = [
            'unggah' => 'Unggah Mandiri/Upload Karya Ilmiah',
            'plagiasi' => 'Cek Plagiasi',
            'bebas' => 'Bebas Pustaka',
            'pinjam' => 'Peminjaman Buku',
            'lainnya' => 'Umum',
        ];
        
        $topicLabel = $topicLabels[$topic] ?? 'Umum';
        
        $prompt = "Topik: {$topicLabel}\nPertanyaan member: {$question}";
        
        if (!empty($context['member_name'])) {
            $prompt .= "\nNama member: {$context['member_name']}";
        }
        
        $prompt .= "\n\nBerikan jawaban yang helpful dan informatif.";
        
        return $prompt;
    }

    private function formatResponse(string $content): string
    {
        // Clean up response
        $content = trim($content);
        
        // Add suggestion to contact staff if response seems uncertain
        $uncertainWords = ['tidak yakin', 'mungkin', 'sepertinya', 'kurang tahu'];
        foreach ($uncertainWords as $word) {
            if (stripos($content, $word) !== false) {
                $content .= "\n\n💡 Untuk informasi lebih detail, ketik **staff** untuk bicara dengan pustakawan.";
                break;
            }
        }
        
        return $content;
    }
}
