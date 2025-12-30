<?php

return [
    // Unggah Mandiri
    'unggah' => [
        'keywords' => ['unggah', 'upload', 'submit', 'kirim', 'skripsi', 'thesis', 'tesis', 'tugas akhir', 'ta', 'karya ilmiah', 'repository', 'repo'],
        'responses' => [
            'main' => "📤 **Panduan Unggah Mandiri**\n\n1. Login ke portal member\n2. Pilih menu **Unggah Mandiri**\n3. Isi form lengkap (judul, abstrak, dll)\n4. Upload file PDF (maks 20MB)\n5. Tunggu verifikasi pustakawan\n\n⏱️ Proses: 1-3 hari kerja\n\nKetik **format** untuk info format file, atau **status** untuk cek progress.",
            'format' => "📄 **Format File Unggah**\n\n✅ Format: PDF\n✅ Ukuran: Maks 20MB\n✅ Nama file: Tanpa karakter khusus\n✅ Pastikan file tidak corrupt/rusak\n\n💡 Tips: Compress PDF jika terlalu besar",
            'status' => "📊 **Cek Status Unggahan**\n\nLihat di Dashboard → Karya Ilmiah\n\nStatus:\n• **Pending** - Menunggu review\n• **Review** - Sedang diperiksa\n• **Revision** - Perlu perbaikan\n• **Approved** - Disetujui\n• **Published** - Sudah terbit",
            'revisi' => "✏️ **Revisi Unggahan**\n\nJika diminta revisi:\n1. Buka Dashboard → Karya Ilmiah\n2. Lihat catatan dari pustakawan\n3. Perbaiki sesuai catatan\n4. Upload ulang file yang sudah direvisi",
            'gagal' => "❌ **Unggahan Ditolak?**\n\nKemungkinan penyebab:\n• File corrupt/tidak bisa dibuka\n• Format tidak sesuai\n• Data tidak lengkap\n• Judul/abstrak tidak sesuai\n\nPerbaiki dan submit ulang, atau ketik **staff** untuk bantuan.",
        ],
    ],
    
    // Plagiasi
    'plagiasi' => [
        'keywords' => ['plagiasi', 'plagiarism', 'plagiat', 'similarity', 'turnitin', 'copyleak', 'cek keaslian', 'originalitas', 'duplikat', 'jiplak'],
        'responses' => [
            'main' => "🔍 **Layanan Cek Plagiasi**\n\n1. Login ke portal member\n2. Menu **Cek Plagiasi**\n3. Upload dokumen (PDF/DOCX)\n4. Tunggu hasil pemeriksaan\n\n⏱️ Hasil: 1-2 hari kerja\n📊 Batas similarity: Maks 25%\n\nKetik **hasil** untuk info hasil, atau **gagal** jika similarity tinggi.",
            'hasil' => "📊 **Hasil Cek Plagiasi**\n\nAnda akan mendapat:\n• Persentase similarity\n• Detail sumber terdeteksi\n• Sertifikat (jika lolos <25%)\n\nHasil bisa didownload di Dashboard.",
            'gagal' => "❌ **Similarity Tinggi (>25%)?**\n\nCara menurunkan:\n1. Parafrase dengan kata sendiri\n2. Tambah sitasi yang benar\n3. Hindari copy-paste langsung\n4. Gunakan kutipan dengan tanda petik\n5. Submit ulang setelah revisi",
            'sertifikat' => "📜 **Sertifikat Bebas Plagiasi**\n\nDiterbitkan otomatis jika:\n• Similarity < 25%\n• Dokumen sudah direview\n\nDownload di: Dashboard → Sertifikat Plagiasi",
            'biaya' => "💰 **Biaya Cek Plagiasi**\n\nLayanan ini **GRATIS** untuk mahasiswa UNIDA Gontor.\n\nMaksimal 3x pengecekan per dokumen.",
        ],
    ],
    
    // Bebas Pustaka
    'bebas' => [
        'keywords' => ['bebas pustaka', 'surat bebas', 'clearance', 'surat keterangan', 'skl', 'lulus', 'wisuda', 'yudisium'],
        'responses' => [
            'main' => "📜 **Surat Bebas Pustaka**\n\nDiterbitkan otomatis setelah:\n✅ Karya ilmiah dipublikasikan\n✅ Tidak ada pinjaman aktif\n✅ Tidak ada denda tertunggak\n\n📥 Download: Dashboard → Surat Bebas Pustaka\n\nKetik **syarat** untuk detail persyaratan.",
            'syarat' => "✅ **Syarat Bebas Pustaka**\n\n1. Karya ilmiah sudah published di E-Thesis\n2. Tidak ada buku yang masih dipinjam\n3. Tidak ada denda yang belum dibayar\n4. Data profil lengkap\n\nJika ada kendala, ketik **staff**.",
            'download' => "📥 **Cara Download Surat**\n\n1. Login portal member\n2. Buka Dashboard\n3. Scroll ke 'Surat Bebas Pustaka'\n4. Klik **PDF** untuk download\n5. Atau **Cetak** untuk print langsung",
            'belum' => "⏳ **Surat Belum Muncul?**\n\nPastikan:\n• Skripsi sudah status 'Published'\n• Tidak ada pinjaman aktif\n• Tidak ada denda\n\nJika sudah memenuhi tapi belum muncul, ketik **staff**.",
        ],
    ],
    
    // Peminjaman
    'pinjam' => [
        'keywords' => ['pinjam', 'peminjaman', 'borrow', 'loan', 'perpanjang', 'extend', 'kembalikan', 'return', 'denda', 'fine', 'terlambat', 'telat', 'buku'],
        'responses' => [
            'main' => "📚 **Layanan Peminjaman**\n\n• Maks pinjam: 3 buku\n• Durasi: 7 hari\n• Perpanjangan: 1x (jika tidak ada reservasi)\n• Denda: Rp 500/hari/buku\n\nKetik **perpanjang**, **denda**, atau **jam** untuk info lebih.",
            'perpanjang' => "🔄 **Cara Perpanjang Buku**\n\n**Online:**\n1. Dashboard → Peminjaman Aktif\n2. Klik 'Perpanjang'\n\n**Offline:**\nDatang ke meja sirkulasi\n\n⚠️ Tidak bisa perpanjang jika:\n• Sudah perpanjang 1x\n• Ada member lain yang reservasi",
            'denda' => "💰 **Informasi Denda**\n\n• Tarif: Rp 500/hari/buku\n• Bayar di meja sirkulasi\n• Atau transfer ke rekening perpustakaan\n\n⚠️ Denda harus lunas sebelum:\n• Pinjam buku baru\n• Mengurus bebas pustaka",
            'hilang' => "📕 **Buku Hilang/Rusak**\n\nJika buku hilang atau rusak parah:\n1. Lapor ke pustakawan\n2. Ganti dengan buku yang sama, ATAU\n3. Ganti dengan uang sesuai harga buku\n\nHubungi pustakawan untuk detail.",
            'reservasi' => "📋 **Reservasi Buku**\n\nJika buku sedang dipinjam orang lain:\n1. Cari buku di katalog\n2. Klik 'Reservasi'\n3. Anda akan dihubungi saat tersedia\n\nMaks reservasi: 2 buku",
            'katalog' => "🔎 **Cari Buku di Katalog**\n\n1. Buka lib.unida.gontor.ac.id\n2. Gunakan kolom pencarian\n3. Filter berdasarkan kategori\n4. Lihat ketersediaan & lokasi rak",
        ],
    ],
    
    // Jam & Lokasi
    'jam' => [
        'keywords' => ['jam', 'buka', 'tutup', 'operasional', 'jadwal', 'waktu', 'libur', 'hari'],
        'responses' => [
            'main' => "🕐 **Jam Operasional**\n\n📅 **Senin - Kamis**\n08:00 - 16:00 WIB\n\n📅 **Jumat**\nLibur\n\n📅 **Sabtu - Minggu**\n08:00 - 21:00 WIB\n\n💡 Perpustakaan buka sampai malam untuk mahasiswa asrama.",
        ],
    ],
    
    // Kontak
    'kontak' => [
        'keywords' => ['kontak', 'contact', 'hubungi', 'telepon', 'telp', 'hp', 'whatsapp', 'wa', 'email', 'alamat', 'lokasi', 'dimana'],
        'responses' => [
            'main' => "📞 **Kontak Perpustakaan**\n\n📍 **Alamat:**\nGedung Perpustakaan UNIDA Gontor\nJl. Raya Siman Km. 6, Ponorogo\n\n📱 **WhatsApp:** 0851-8305-3934\n📧 **Email:** perpustakaan@unida.gontor.ac.id\n🌐 **Website:** library.unida.gontor.ac.id",
        ],
    ],
    
    // Keanggotaan
    'member' => [
        'keywords' => ['daftar', 'registrasi', 'register', 'kartu', 'anggota', 'member', 'keanggotaan', 'aktivasi', 'akun', 'login', 'masuk', 'password', 'lupa'],
        'responses' => [
            'main' => "👤 **Keanggotaan Perpustakaan**\n\nMahasiswa UNIDA otomatis terdaftar.\n\n**Cara Aktivasi:**\n1. Buka lib.unida.gontor.ac.id\n2. Klik 'Login dengan Google'\n3. Gunakan email @student.unida.gontor.ac.id\n4. Lengkapi profil\n\nKetik **lupa** jika ada masalah login.",
            'lupa' => "🔑 **Masalah Login?**\n\nGunakan **Login dengan Google** pakai email kampus.\n\nJika tetap tidak bisa:\n1. Pastikan email benar\n2. Coba clear cache browser\n3. Gunakan browser lain\n4. Ketik **staff** untuk bantuan",
            'profil' => "👤 **Lengkapi Profil**\n\nData yang perlu dilengkapi:\n• Foto profil\n• NIM\n• Fakultas & Prodi\n• No. HP\n\nBuka: Dashboard → Edit Profil",
        ],
    ],
    
    // E-Resources
    'eresource' => [
        'keywords' => ['ebook', 'e-book', 'jurnal', 'journal', 'database', 'e-thesis', 'ethesis', 'repository', 'digital', 'online', 'koleksi'],
        'responses' => [
            'main' => "📱 **E-Resources Perpustakaan**\n\n📚 **E-Book** - Koleksi buku digital\n📄 **E-Thesis** - Repository karya ilmiah\n📰 **E-Journal** - Akses jurnal ilmiah\n\nAkses: lib.unida.gontor.ac.id\nLogin dengan akun member.\n\nKetik **akses** untuk cara mengakses.",
            'akses' => "🔓 **Cara Akses E-Resources**\n\n1. Login ke portal member\n2. Pilih menu E-Book/E-Thesis/E-Journal\n3. Cari koleksi yang diinginkan\n4. Klik untuk baca/download\n\n💡 Beberapa koleksi hanya bisa dibaca online.",
        ],
    ],
    
    // Fasilitas
    'fasilitas' => [
        'keywords' => ['fasilitas', 'ruang', 'wifi', 'internet', 'komputer', 'pc', 'print', 'fotocopy', 'scan', 'ac', 'toilet', 'mushola'],
        'responses' => [
            'main' => "🏛️ **Fasilitas Perpustakaan**\n\n✅ Ruang baca ber-AC\n✅ WiFi gratis\n✅ Komputer untuk akses katalog\n✅ Stop kontak untuk charging\n✅ Toilet\n✅ Mushola terdekat\n\n📍 Lokasi: Gedung Perpustakaan Lt. 1-2",
        ],
    ],
    
    // Aturan
    'aturan' => [
        'keywords' => ['aturan', 'peraturan', 'tata tertib', 'larangan', 'boleh', 'tidak boleh', 'dilarang'],
        'responses' => [
            'main' => "📋 **Tata Tertib Perpustakaan**\n\n✅ **Boleh:**\n• Bawa laptop & charger\n• Minum (botol tertutup)\n• Diskusi pelan di area diskusi\n\n❌ **Tidak Boleh:**\n• Makan di ruang baca\n• Berisik/mengganggu\n• Merusak koleksi\n• Membawa tas besar (titip di loker)",
        ],
    ],
];
