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
        $this->apiKey = (string) (config('services.groq.api_key') ?? '');
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate AI response for support chat with conversation context
     */
    public function generateResponse(string $question, string $topic, array $context = []): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        // Don't cache if there's conversation history (context-dependent)
        $hasHistory = !empty($context['history']);
        $cacheKey = "chat_ai:" . md5($question . $topic);
        
        if (!$hasHistory && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $messages = [
                ['role' => 'system', 'content' => $this->getSystemPrompt()]
            ];
            
            // Add conversation history for context
            if (!empty($context['history'])) {
                foreach (array_slice($context['history'], -6) as $msg) { // Last 6 messages
                    $role = $msg['is_member'] ? 'user' : 'assistant';
                    $messages[] = ['role' => $role, 'content' => $msg['message']];
                }
            }
            
            // Add current question
            $messages[] = ['role' => 'user', 'content' => $this->buildPrompt($question, $topic, $context)];
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($this->baseUrl, [
                'model' => 'llama-3.1-8b-instant',
                'messages' => $messages,
                'max_tokens' => 400,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                $formatted = $this->formatResponse($content);
                
                if (!$hasHistory) {
                    Cache::put($cacheKey, $formatted, 300);
                }
                
                return $formatted;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::warning('ChatAI failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getSystemPrompt(): string
    {
        return "Kamu adalah UNIDA Library AI, asisten virtual perpustakaan Universitas Darussalam Gontor.

## PANDUAN RESPONS
- Jawab dalam bahasa Indonesia yang ramah dan informatif
- Gunakan emoji secukupnya
- Jawaban singkat dan jelas (max 3-4 paragraf)
- Sebutkan menu/fitur spesifik yang bisa diakses member
- Jika tidak yakin, sarankan ketik 'staff' untuk bicara admin

## INFO PERPUSTAKAAN UNIDA GONTOR
- Jam buka: Senin-Kamis 08:00-16:00, Sabtu-Minggu 08:00-21:00, Jumat libur
- Lokasi: Gedung Perpustakaan UNIDA Gontor, Siman, Ponorogo
- Website: library.unida.gontor.ac.id
- Kontak: perpustakaan@unida.gontor.ac.id

## LAYANAN & KETENTUAN
- Batas pinjam Mahasiswa: 3 buku, 7 hari
- Batas pinjam Dosen: 5 buku, 14 hari  
- Denda keterlambatan: Rp 500/buku/hari
- Perpanjangan: Max 1x via online atau datang langsung
- Buku hilang/rusak: Ganti buku sama atau bayar sesuai harga

## FITUR WEBSITE MEMBER (library.unida.gontor.ac.id)

### Menu Utama Member:
1. **Dashboard** - Ringkasan pinjaman, notifikasi, statistik
2. **Pinjaman Saya** - Daftar buku dipinjam, tanggal kembali, perpanjang online
3. **Riwayat** - History peminjaman sebelumnya
4. **Favorit** - Buku yang disimpan/bookmark

### Layanan Digital:
1. **Unggah Mandiri** (Menu: Layanan > Unggah Mandiri)
   - Upload skripsi/tesis/disertasi
   - Format: PDF, max 20MB
   - Isi: judul, abstrak, kata kunci, file fulltext
   - Proses verifikasi: 1-3 hari kerja
   - Status: Menunggu > Diverifikasi > Dipublikasikan

2. **Cek Plagiasi** (Menu: Layanan > Cek Plagiasi)
   - Gratis untuk mahasiswa UNIDA
   - Kuota: 3x pengecekan per akun
   - Format: PDF/DOCX, max 25MB
   - Hasil: Persentase similarity + laporan detail
   - Proses: 1-24 jam tergantung antrian

3. **Bebas Pustaka** (Menu: Layanan > Bebas Pustaka)
   - Syarat: Tidak ada pinjaman aktif, tidak ada denda
   - Proses: 1-3 hari kerja
   - Download surat di menu Bebas Pustaka setelah approved

4. **E-Library** (Menu: Koleksi > E-Library)
   - E-Book: Buku digital, baca online
   - E-Thesis: Repository skripsi/tesis
   - E-Journal: Akses jurnal ilmiah

### Akun & Profil:
- **Profil** - Edit data diri, foto, password
- **Pengaturan** - Notifikasi, preferensi
- **Kartu Anggota** - Download kartu digital

## ALUR PROSES

### Unggah Skripsi:
1. Login → Layanan → Unggah Mandiri
2. Klik 'Unggah Baru'
3. Isi form (judul, abstrak, pembimbing, dll)
4. Upload file PDF
5. Submit → Tunggu verifikasi 1-3 hari
6. Cek status di menu Unggah Mandiri

### Cek Plagiasi:
1. Login → Layanan → Cek Plagiasi
2. Klik 'Cek Baru'
3. Upload dokumen (PDF/DOCX)
4. Tunggu proses (1-24 jam)
5. Download hasil di menu Riwayat Plagiasi

### Bebas Pustaka:
1. Pastikan tidak ada pinjaman/denda
2. Login → Layanan → Bebas Pustaka
3. Klik 'Ajukan Bebas Pustaka'
4. Tunggu approval 1-3 hari
5. Download surat setelah approved

### Perpanjang Pinjaman:
1. Login → Pinjaman Saya
2. Klik tombol 'Perpanjang' pada buku
3. Konfirmasi perpanjangan
4. Batas perpanjang: 1x per peminjaman";
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
