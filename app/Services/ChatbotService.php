<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatRoom;

class ChatbotService
{
    protected array $knowledge;
    protected array $greetings = ['halo', 'hai', 'hi', 'hello', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'assalamualaikum', 'permisi', 'p', 'hallo', 'hay'];
    protected array $thanks = ['terima kasih', 'terimakasih', 'makasih', 'thanks', 'thank you', 'ok', 'oke', 'baik', 'siap', 'mantap', 'good', 'bagus', 'sip'];
    protected array $confirmations = ['iya', 'ya', 'yup', 'yes', 'betul', 'benar', 'bener'];
    protected array $negations = ['tidak', 'nggak', 'gak', 'no', 'bukan', 'belum', 'jangan'];
    
    protected ChatAiService $aiService;

    public function __construct(ChatAiService $aiService)
    {
        $this->knowledge = config('chatbot', []);
        $this->aiService = $aiService;
    }

    public function processMessage(ChatRoom $room, string $message): ?array
    {
        $originalMessage = $message;
        $message = $this->normalizeMessage($message);
        
        // Check greeting
        if ($this->isGreeting($message)) {
            return $this->greetingResponse($room);
        }
        
        // Check thanks
        if ($this->isThanks($message)) {
            return $this->thanksResponse();
        }
        
        // Check help/menu
        if ($this->isAskingHelp($message)) {
            return $this->helpResponse();
        }
        
        // Check staff request
        if ($this->wantsStaff($message)) {
            return $this->staffResponse();
        }
        
        // Find matching intent with context understanding
        $match = $this->findIntent($message, $room);
        if ($match) {
            return $this->buildResponse($match['category'], $match['subtype']);
        }
        
        // No match - try AI fallback
        return $this->noMatchResponse($room, $originalMessage);
    }

    protected function normalizeMessage(string $message): string
    {
        $message = strtolower(trim($message));
        // Remove extra spaces
        $message = preg_replace('/\s+/', ' ', $message);
        // Common typo fixes
        $typos = [
            'gimana' => 'bagaimana', 'gmn' => 'bagaimana', 'gmna' => 'bagaimana',
            'caranya' => 'cara', 'gmn cara' => 'bagaimana cara',
            'kyk' => 'seperti', 'kayak' => 'seperti',
            'gk' => 'tidak', 'ga' => 'tidak', 'ngga' => 'tidak',
            'udah' => 'sudah', 'udh' => 'sudah',
            'blm' => 'belum', 'blum' => 'belum',
            'lg' => 'lagi', 'lgi' => 'lagi',
            'yg' => 'yang', 'dgn' => 'dengan', 'utk' => 'untuk',
            'bs' => 'bisa', 'bsa' => 'bisa',
            'mw' => 'mau', 'mo' => 'mau', 'pengen' => 'mau', 'pingin' => 'mau',
            'klo' => 'kalau', 'kalo' => 'kalau',
            'aja' => 'saja', 'aj' => 'saja',
            'dpt' => 'dapat', 'bgt' => 'banget',
            'skripsi' => 'skripsi thesis', 'tesis' => 'thesis',
            'ta' => 'tugas akhir', 'tugas akhir' => 'tugas akhir thesis',
        ];
        foreach ($typos as $typo => $fix) {
            $message = str_replace($typo, $fix, $message);
        }
        return $message;
    }

    protected function isGreeting(string $message): bool
    {
        // Only greeting if message is short
        if (strlen($message) > 30) return false;
        foreach ($this->greetings as $g) {
            if (str_contains($message, $g)) return true;
        }
        return false;
    }

    protected function isThanks(string $message): bool
    {
        if (strlen($message) > 50) return false;
        foreach ($this->thanks as $t) {
            if (str_contains($message, $t)) return true;
        }
        return false;
    }

    protected function isAskingHelp(string $message): bool
    {
        $helpWords = ['help', 'bantuan', 'menu', 'apa saja', 'bisa apa', 'layanan apa', 'fitur', 'ada apa'];
        foreach ($helpWords as $h) {
            if (str_contains($message, $h)) return true;
        }
        return false;
    }

    protected function wantsStaff(string $message): bool
    {
        $staffWords = ['staff', 'staf', 'petugas', 'pustakawan', 'admin', 'manusia', 'orang', 'bicara langsung', 'komplain', 'keluhan', 'masalah serius'];
        foreach ($staffWords as $s) {
            if (str_contains($message, $s)) return true;
        }
        return false;
    }

    protected function findIntent(string $message, ChatRoom $room): ?array
    {
        $scores = [];
        
        foreach ($this->knowledge as $category => $data) {
            $score = 0;
            $keywords = $data['keywords'] ?? [];
            
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    // Longer keyword = higher score
                    $score += strlen($keyword) * 2;
                    // Exact word match bonus
                    if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $message)) {
                        $score += 5;
                    }
                }
            }
            
            if ($score > 0) {
                $scores[$category] = $score;
            }
        }
        
        // Require minimum score threshold to avoid false positives
        // Let AI handle low-confidence matches
        $minScore = 15;
        
        if (empty($scores) || max($scores) < $minScore) {
            // Try context from room topic only for very short messages
            if (strlen($message) < 20) {
                $topicMap = [
                    'unggah' => 'unggah', 'unggah_mandiri' => 'unggah',
                    'plagiasi' => 'plagiasi',
                    'bebas' => 'bebas', 'bebas_pustaka' => 'bebas',
                    'pinjam' => 'pinjam', 'peminjaman' => 'pinjam',
                ];
                $roomTopic = $room->topic ?? '';
                if (isset($topicMap[$roomTopic])) {
                    return [
                        'category' => $topicMap[$roomTopic],
                        'subtype' => $this->detectSubtype($message, $topicMap[$roomTopic])
                    ];
                }
            }
            return null;
        }
        
        // Get highest scoring category
        arsort($scores);
        $category = array_key_first($scores);
        
        return [
            'category' => $category,
            'subtype' => $this->detectSubtype($message, $category)
        ];
    }

    protected function detectSubtype(string $message, string $category): string
    {
        $subtypePatterns = [
            'format' => ['format', 'file', 'pdf', 'ukuran', 'size', 'tipe', 'jenis file'],
            'status' => ['status', 'progress', 'sudah sampai mana', 'gimana', 'bagaimana', 'cek', 'lihat'],
            'hasil' => ['hasil', 'result', 'laporan', 'report', 'nilai', 'skor'],
            'gagal' => ['gagal', 'ditolak', 'rejected', 'tinggi', 'tidak lolos', 'error', 'masalah'],
            'revisi' => ['revisi', 'perbaiki', 'edit', 'ubah', 'ganti'],
            'syarat' => ['syarat', 'requirement', 'ketentuan', 'persyaratan', 'apa saja', 'perlu apa'],
            'download' => ['download', 'unduh', 'cetak', 'print', 'ambil'],
            'perpanjang' => ['perpanjang', 'extend', 'tambah waktu', 'lama'],
            'denda' => ['denda', 'fine', 'bayar', 'terlambat', 'telat', 'biaya'],
            'hilang' => ['hilang', 'rusak', 'sobek', 'hancur'],
            'reservasi' => ['reservasi', 'booking', 'pesan', 'antri'],
            'katalog' => ['katalog', 'cari buku', 'search', 'temukan'],
            'lupa' => ['lupa', 'forgot', 'tidak bisa login', 'tidak bisa masuk', 'error login'],
            'profil' => ['profil', 'profile', 'data diri', 'lengkapi'],
            'akses' => ['akses', 'buka', 'masuk', 'cara buka'],
            'sertifikat' => ['sertifikat', 'certificate', 'bukti'],
            'biaya' => ['biaya', 'harga', 'bayar', 'gratis', 'berbayar'],
            'belum' => ['belum', 'tidak muncul', 'tidak ada', 'kosong', 'dimana'],
        ];
        
        foreach ($subtypePatterns as $subtype => $patterns) {
            foreach ($patterns as $p) {
                if (str_contains($message, $p)) {
                    // Check if this subtype exists in category responses
                    if (isset($this->knowledge[$category]['responses'][$subtype])) {
                        return $subtype;
                    }
                }
            }
        }
        
        return 'main';
    }

    protected function buildResponse(string $category, string $subtype): array
    {
        $responses = $this->knowledge[$category]['responses'] ?? [];
        $response = $responses[$subtype] ?? $responses['main'] ?? null;
        
        if (!$response) {
            return $this->noMatchResponse();
        }
        
        return [
            'message' => $response,
            'type' => 'bot',
            'handled' => true,
        ];
    }

    protected function greetingResponse(ChatRoom $room): array
    {
        $member = $room->member;
        $name = $member ? ucwords(strtolower(explode(' ', $member->name)[0])) : 'Kak';
        $hour = (int) date('H');
        
        $greeting = match(true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
        
        return [
            'message' => "👋 **{$greeting}, {$name}!**\n\nSaya asisten virtual perpustakaan UNIDA Gontor. Ada yang bisa saya bantu?\n\n" . $this->getQuickMenu(),
            'type' => 'bot',
            'handled' => true,
        ];
    }

    protected function thanksResponse(): array
    {
        $responses = [
            "Sama-sama! 😊 Ada lagi yang bisa dibantu?",
            "Senang bisa membantu! 🙏 Jangan ragu bertanya lagi ya.",
            "Terima kasih kembali! Semoga bermanfaat 📚",
        ];
        
        return [
            'message' => $responses[array_rand($responses)],
            'type' => 'bot',
            'handled' => true,
        ];
    }

    protected function helpResponse(): array
    {
        return [
            'message' => "📋 **Layanan Perpustakaan**\n\n" . $this->getQuickMenu() . "\n\nKetik topik yang ingin ditanyakan.\nKetik **staff** untuk bicara dengan pustakawan.",
            'type' => 'bot',
            'handled' => true,
        ];
    }

    protected function staffResponse(): array
    {
        return [
            'message' => "👨‍💼 **Menghubungkan ke Pustakawan**\n\nPesan Anda akan diteruskan ke pustakawan. Mohon tunggu balasan.\n\n⏱️ Jam layanan: Senin-Jumat, 08:00-16:00 WIB\n\nSilakan jelaskan keperluan Anda.",
            'type' => 'bot',
            'handled' => false, // Forward to staff
        ];
    }

    protected function noMatchResponse(ChatRoom $room = null, string $originalMessage = ''): array
    {
        // Try AI response as fallback
        if ($this->aiService->isEnabled() && $room && strlen($originalMessage) >= 5) {
            $aiResponse = $this->aiService->generateResponse(
                $originalMessage,
                $room->topic ?? 'lainnya',
                ['member_name' => $room->member?->name]
            );
            
            if ($aiResponse) {
                return [
                    'message' => "🤖 " . $aiResponse,
                    'type' => 'bot',
                    'handled' => true,
                ];
            }
        }
        
        return [
            'message' => "🤔 Maaf, saya belum paham pertanyaan Anda.\n\nCoba ketik salah satu:\n" . $this->getQuickMenu() . "\n\nAtau ketik **staff** untuk bicara dengan pustakawan.",
            'type' => 'bot',
            'handled' => true,
        ];
    }

    protected function getQuickMenu(): string
    {
        return "• **unggah** - Upload karya ilmiah\n• **plagiasi** - Cek plagiasi\n• **bebas pustaka** - Surat bebas pustaka\n• **pinjam** - Peminjaman buku\n• **jam** - Jam operasional\n• **kontak** - Info kontak\n• **fasilitas** - Fasilitas perpustakaan";
    }

    public function createBotMessage(ChatRoom $room, string $message): ChatMessage
    {
        return ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => null,
            'message' => $message,
            'type' => 'bot',
        ]);
    }
}
