<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatRoom;

class ChatbotService
{
    // Knowledge base dengan pattern matching dan jawaban
    protected array $knowledgeBase = [
        // Unggah Mandiri
        'unggah' => [
            'patterns' => ['unggah', 'upload', 'submit', 'kirim skripsi', 'kirim thesis', 'cara upload', 'bagaimana upload'],
            'responses' => [
                'main' => "📤 **Panduan Unggah Mandiri**\n\nUntuk mengunggah karya ilmiah:\n1. Login ke portal member\n2. Pilih menu **Unggah Mandiri**\n3. Isi form dengan lengkap (judul, abstrak, dll)\n4. Upload file PDF (maks 20MB)\n5. Tunggu verifikasi dari pustakawan\n\n⏱️ Proses verifikasi: 1-3 hari kerja",
                'format' => "📄 **Format File yang Diterima**\n\n• Format: PDF\n• Ukuran maks: 20MB\n• Pastikan file tidak corrupt\n• Nama file tanpa karakter khusus",
                'status' => "📊 **Cek Status Unggahan**\n\nAnda bisa cek status di:\n• Dashboard Member → Karya Ilmiah\n• Status: Pending → Review → Approved/Rejected",
            ],
        ],
        
        // Plagiasi
        'plagiasi' => [
            'patterns' => ['plagiasi', 'plagiarism', 'cek plagiasi', 'similarity', 'turnitin', 'copyleak'],
            'responses' => [
                'main' => "🔍 **Layanan Cek Plagiasi**\n\nPerpustakaan menyediakan layanan cek plagiasi:\n1. Login ke portal member\n2. Pilih menu **Cek Plagiasi**\n3. Upload dokumen (PDF/DOCX)\n4. Tunggu hasil pemeriksaan\n\n⏱️ Hasil: 1-2 hari kerja\n📊 Batas similarity: maks 25%",
                'hasil' => "📊 **Hasil Cek Plagiasi**\n\nSetelah selesai, Anda akan mendapat:\n• Laporan similarity (%)\n• Detail sumber yang terdeteksi\n• Sertifikat bebas plagiasi (jika lolos)",
                'gagal' => "❌ **Jika Similarity Tinggi**\n\nJika hasil >25%:\n1. Revisi bagian yang terdeteksi\n2. Parafrase dengan kata sendiri\n3. Tambahkan sitasi yang benar\n4. Submit ulang untuk pengecekan",
            ],
        ],
        
        // Bebas Pustaka
        'bebas' => [
            'patterns' => ['bebas pustaka', 'surat bebas', 'clearance', 'surat keterangan', 'skripsi selesai'],
            'responses' => [
                'main' => "📜 **Surat Bebas Pustaka**\n\nSurat bebas pustaka diterbitkan otomatis setelah:\n1. Karya ilmiah di-approve\n2. Dipublikasikan ke E-Thesis\n3. Tidak ada pinjaman aktif\n4. Tidak ada denda tertunggak\n\n📥 Download di: Dashboard → Surat Bebas Pustaka",
                'syarat' => "✅ **Syarat Bebas Pustaka**\n\n• Karya ilmiah sudah dipublikasikan\n• Tidak ada buku yang dipinjam\n• Tidak ada denda yang belum dibayar\n• Data member lengkap",
                'download' => "📥 **Cara Download**\n\n1. Login ke portal member\n2. Buka Dashboard\n3. Scroll ke bagian 'Surat Bebas Pustaka'\n4. Klik tombol PDF atau Cetak",
            ],
        ],
        
        // Peminjaman
        'pinjam' => [
            'patterns' => ['pinjam', 'peminjaman', 'borrow', 'perpanjang', 'extend', 'kembalikan', 'return', 'denda', 'fine', 'terlambat'],
            'responses' => [
                'main' => "📚 **Layanan Peminjaman**\n\n• Maks pinjam: 3 buku\n• Durasi: 7 hari\n• Perpanjangan: 1x (jika tidak ada reservasi)\n• Denda keterlambatan: Rp 500/hari/buku",
                'perpanjang' => "🔄 **Cara Perpanjang**\n\n1. Login ke portal member\n2. Buka Dashboard → Peminjaman Aktif\n3. Klik tombol 'Perpanjang'\n4. Atau hubungi petugas di meja sirkulasi",
                'denda' => "💰 **Informasi Denda**\n\n• Denda: Rp 500/hari/buku\n• Bayar di meja sirkulasi\n• Atau transfer ke rekening perpustakaan\n• Simpan bukti pembayaran",
                'jam' => "🕐 **Jam Layanan Sirkulasi**\n\n• Senin-Kamis: 08:00-16:00\n• Jumat: 08:00-11:30, 13:30-16:00\n• Sabtu-Minggu: Tutup",
            ],
        ],
        
        // Jam Operasional
        'jam' => [
            'patterns' => ['jam buka', 'jam operasional', 'jam kerja', 'buka jam', 'tutup jam', 'hari libur', 'jadwal'],
            'responses' => [
                'main' => "🕐 **Jam Operasional Perpustakaan**\n\n📅 Senin - Kamis:\n• 08:00 - 16:00 WIB\n\n📅 Jumat:\n• 08:00 - 11:30 WIB\n• 13:30 - 16:00 WIB\n\n📅 Sabtu & Minggu: Tutup\n\n⚠️ Jam dapat berubah saat libur nasional",
            ],
        ],
        
        // Kontak
        'kontak' => [
            'patterns' => ['kontak', 'contact', 'hubungi', 'telepon', 'telp', 'whatsapp', 'wa', 'email', 'alamat'],
            'responses' => [
                'main' => "📞 **Kontak Perpustakaan**\n\n📍 Alamat:\nKampus UNIDA Gontor\nJl. Raya Siman Km. 6, Ponorogo\n\n📱 WhatsApp: 0851-8305-3934\n📧 Email: perpustakaan@unida.gontor.ac.id\n\n🌐 Website: lib.unida.gontor.ac.id",
            ],
        ],
        
        // Keanggotaan
        'member' => [
            'patterns' => ['daftar', 'registrasi', 'kartu anggota', 'member', 'keanggotaan', 'aktivasi'],
            'responses' => [
                'main' => "👤 **Keanggotaan Perpustakaan**\n\nMahasiswa UNIDA otomatis terdaftar.\n\nUntuk aktivasi:\n1. Kunjungi lib.unida.gontor.ac.id\n2. Klik 'Login dengan Google'\n3. Gunakan email @student.unida.gontor.ac.id\n4. Lengkapi profil Anda",
                'lupa' => "🔑 **Lupa Password/Akses**\n\nGunakan fitur 'Login dengan Google' menggunakan email kampus Anda.\n\nJika ada masalah, hubungi pustakawan.",
            ],
        ],
        
        // E-Resources
        'eresource' => [
            'patterns' => ['ebook', 'e-book', 'jurnal', 'journal', 'database', 'e-thesis', 'ethesis', 'repository'],
            'responses' => [
                'main' => "📱 **E-Resources Perpustakaan**\n\n• **E-Book**: Koleksi buku digital\n• **E-Thesis**: Repository karya ilmiah\n• **E-Journal**: Akses jurnal ilmiah\n\nAkses di: lib.unida.gontor.ac.id\nLogin dengan akun member Anda",
            ],
        ],
    ];

    // Greeting patterns
    protected array $greetings = ['halo', 'hai', 'hi', 'hello', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'assalamualaikum', 'permisi'];
    
    // Thanks patterns
    protected array $thanks = ['terima kasih', 'terimakasih', 'makasih', 'thanks', 'thank you', 'ok', 'oke', 'baik', 'siap'];

    /**
     * Process member message and return bot response if applicable
     */
    public function processMessage(ChatRoom $room, string $message): ?array
    {
        $message = strtolower(trim($message));
        
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
            return $this->helpResponse($room);
        }
        
        // Check staff request
        if ($this->wantsStaff($message)) {
            return $this->staffResponse($room);
        }
        
        // Find matching intent
        $match = $this->findIntent($message);
        if ($match) {
            return $this->buildResponse($match['category'], $match['subtype'], $room);
        }
        
        // No match - suggest options or forward to staff
        return $this->noMatchResponse($room);
    }

    protected function isGreeting(string $message): bool
    {
        foreach ($this->greetings as $g) {
            if (str_contains($message, $g)) return true;
        }
        return false;
    }

    protected function isThanks(string $message): bool
    {
        foreach ($this->thanks as $t) {
            if (str_contains($message, $t)) return true;
        }
        return false;
    }

    protected function isAskingHelp(string $message): bool
    {
        $helpWords = ['help', 'bantuan', 'menu', 'apa saja', 'bisa apa', 'layanan apa'];
        foreach ($helpWords as $h) {
            if (str_contains($message, $h)) return true;
        }
        return false;
    }

    protected function wantsStaff(string $message): bool
    {
        $staffWords = ['staff', 'staf', 'petugas', 'pustakawan', 'admin', 'manusia', 'orang', 'bicara langsung'];
        foreach ($staffWords as $s) {
            if (str_contains($message, $s)) return true;
        }
        return false;
    }

    protected function findIntent(string $message): ?array
    {
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($this->knowledgeBase as $category => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (str_contains($message, $pattern)) {
                    $score = strlen($pattern);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = ['category' => $category, 'subtype' => $this->detectSubtype($message, $category)];
                    }
                }
            }
        }
        
        return $bestMatch;
    }

    protected function detectSubtype(string $message, string $category): string
    {
        $subtypePatterns = [
            'format' => ['format', 'file', 'pdf', 'ukuran'],
            'status' => ['status', 'progress', 'sudah', 'belum'],
            'hasil' => ['hasil', 'result', 'laporan'],
            'gagal' => ['gagal', 'ditolak', 'rejected', 'tinggi'],
            'syarat' => ['syarat', 'requirement', 'ketentuan', 'persyaratan'],
            'download' => ['download', 'unduh', 'cetak', 'print'],
            'perpanjang' => ['perpanjang', 'extend', 'tambah waktu'],
            'denda' => ['denda', 'fine', 'bayar', 'terlambat'],
            'jam' => ['jam', 'waktu', 'buka', 'tutup'],
            'lupa' => ['lupa', 'forgot', 'tidak bisa login'],
        ];
        
        foreach ($subtypePatterns as $subtype => $patterns) {
            foreach ($patterns as $p) {
                if (str_contains($message, $p)) {
                    return $subtype;
                }
            }
        }
        
        return 'main';
    }

    protected function buildResponse(string $category, string $subtype, ChatRoom $room): array
    {
        $responses = $this->knowledgeBase[$category]['responses'] ?? [];
        $response = $responses[$subtype] ?? $responses['main'] ?? null;
        
        if (!$response) {
            return $this->noMatchResponse($room);
        }
        
        return [
            'message' => $response,
            'type' => 'bot',
            'handled' => true,
            'show_options' => true,
        ];
    }

    protected function greetingResponse(ChatRoom $room): array
    {
        $member = $room->member;
        $name = $member ? explode(' ', $member->name)[0] : 'Kak';
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
            'show_options' => true,
        ];
    }

    protected function thanksResponse(): array
    {
        $responses = [
            "Sama-sama! 😊 Jangan ragu untuk bertanya lagi jika ada yang diperlukan.",
            "Senang bisa membantu! 🙏 Ada lagi yang bisa saya bantu?",
            "Terima kasih kembali! Semoga informasinya bermanfaat. 📚",
        ];
        
        return [
            'message' => $responses[array_rand($responses)],
            'type' => 'bot',
            'handled' => true,
            'show_options' => false,
        ];
    }

    protected function helpResponse(ChatRoom $room): array
    {
        return [
            'message' => "📋 **Layanan yang Tersedia**\n\n" . $this->getQuickMenu() . "\n\nKetik topik yang ingin ditanyakan, atau ketik **\"staff\"** untuk berbicara dengan pustakawan.",
            'type' => 'bot',
            'handled' => true,
            'show_options' => true,
        ];
    }

    protected function staffResponse(ChatRoom $room): array
    {
        return [
            'message' => "👨‍💼 **Menghubungkan ke Pustakawan**\n\nPesan Anda akan diteruskan ke pustakawan. Mohon tunggu balasan dari tim kami.\n\n⏱️ Jam layanan: Senin-Jumat, 08:00-16:00 WIB",
            'type' => 'bot',
            'handled' => false, // Forward to staff
            'show_options' => false,
        ];
    }

    protected function noMatchResponse(ChatRoom $room): array
    {
        return [
            'message' => "🤔 Maaf, saya belum memahami pertanyaan Anda.\n\n" . $this->getQuickMenu() . "\n\nAtau ketik **\"staff\"** untuk berbicara langsung dengan pustakawan.",
            'type' => 'bot',
            'handled' => true,
            'show_options' => true,
        ];
    }

    protected function getQuickMenu(): string
    {
        return "Pilih topik:\n• **unggah** - Unggah karya ilmiah\n• **plagiasi** - Cek plagiasi\n• **bebas pustaka** - Surat bebas pustaka\n• **pinjam** - Peminjaman buku\n• **jam** - Jam operasional\n• **kontak** - Informasi kontak";
    }

    /**
     * Create bot message in database
     */
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
