# 📄 Konsep Modul Cek Plagiasi - Perpustakaan UNIDA

## 📋 Overview

Modul ini memungkinkan mahasiswa untuk mengecek tingkat plagiarisme dokumen tugas akhir mereka secara mandiri dan mendapatkan sertifikat hasil pengecekan yang dapat diunduh langsung dari dashboard member.

---

## 🎯 Tujuan

1. **Menyederhanakan flow** - Mahasiswa tidak perlu mengisi form data lagi (sudah ada di membership)
2. **User-friendly** - Proses seminimal mungkin, hanya upload file
3. **Modern & Professional** - Interface yang intuitif dengan real-time progress
4. **Integrasi** - Terhubung dengan sistem keanggotaan dan submission tugas akhir

---

## 🔄 Flow Pengecekan Plagiasi

### Current Flow (Rumit):
```
Mahasiswa → Isi Form (Nama, NIM, Prodi, Fakultas, dll) → Upload File → Tunggu Manual → Ambil Hasil
```

### Proposed Flow (Simple & Modern):
```
Mahasiswa Login → Dashboard → Klik "Cek Plagiasi" → Upload File → Auto Process → Download Sertifikat
```

---

## 📊 Arsitektur Sistem

### Option A: Integrasi Turnitin API (Professional - Recommended jika ada lisensi)

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   Member/User   │────▶│   Laravel App    │────▶│   Turnitin API  │
│   Dashboard     │     │   (Controller)   │     │   (LTI 1.3)     │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                               │
                               ▼
                        ┌──────────────────┐
                        │   Database       │
                        │   (History &     │
                        │   Certificates)  │
                        └──────────────────┘
```

**Kebutuhan:**
- Lisensi Turnitin (via UNIDA/Dikti)
- API Key (Consumer Key + Shared Secret)
- LTI Launch URL

**Library:**
- `celtic-project/LTI-PHP` atau custom Laravel implementation

### Option B: Integrasi Copyleaks API (Self-hosted alternatif)
```
Copyleaks menyediakan REST API yang lebih mudah diintegrasikan:
- https://copyleaks.com/
- Pricing berdasarkan credit/page
- API Documentation yang lengkap
```

### Option C: Open Source Solution (Budget-friendly)

Menggunakan kombinasi tools open source:
1. **Plagiarism-checker-python** + Laravel Queue
2. **Sherlock** untuk text comparison  
3. Custom algorithm dengan cosine similarity

---

## 🗄️ Database Schema

### Tabel: `plagiarism_checks`

```php
Schema::create('plagiarism_checks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->onDelete('cascade');
    $table->foreignId('thesis_submission_id')->nullable()->constrained()->onDelete('set null');
    
    // File Info
    $table->string('original_filename');
    $table->string('file_path');
    $table->string('file_type'); // pdf, docx
    $table->unsignedInteger('file_size'); // bytes
    $table->unsignedInteger('word_count')->nullable();
    $table->unsignedInteger('page_count')->nullable();
    
    // Check Result
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->decimal('similarity_score', 5, 2)->nullable(); // 0.00 - 100.00
    $table->json('similarity_sources')->nullable(); // Array of matched sources
    $table->json('detailed_report')->nullable(); // Full report data
    
    // Provider Info
    $table->string('provider')->default('internal'); // turnitin, copyleaks, internal
    $table->string('external_id')->nullable(); // ID from external service
    
    // Certificate
    $table->string('certificate_number')->nullable()->unique();
    $table->string('certificate_path')->nullable();
    $table->timestamp('certificate_generated_at')->nullable();
    
    // Processing
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->text('error_message')->nullable();
    
    $table->timestamps();
    
    $table->index(['member_id', 'status']);
    $table->index('certificate_number');
});
```

### Tabel: `plagiarism_settings` (Admin Config)

```php
Schema::create('plagiarism_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();
});

// Settings:
// - provider: turnitin|copyleaks|internal
// - turnitin_consumer_key
// - turnitin_shared_secret  
// - turnitin_launch_url
// - copyleaks_api_key
// - max_file_size_mb
// - allowed_extensions: pdf,docx
// - similarity_threshold_warning: 25
// - similarity_threshold_danger: 40
```

---

## 📁 File Structure

```
app/
├── Models/
│   └── PlagiarismCheck.php
├── Http/Controllers/
│   └── Opac/
│       └── PlagiarismController.php
├── Services/
│   └── Plagiarism/
│       ├── PlagiarismService.php
│       ├── Providers/
│       │   ├── TurnitinProvider.php
│       │   ├── CopyleaksProvider.php
│       │   └── InternalProvider.php
│       └── CertificateGenerator.php
├── Jobs/
│   └── ProcessPlagiarismCheck.php
├── Filament/
│   └── Resources/
│       └── PlagiarismCheckResource.php
resources/views/
├── opac/
│   └── member/
│       ├── plagiarism/
│       │   ├── index.blade.php      # Daftar check
│       │   ├── create.blade.php     # Upload form
│       │   ├── show.blade.php       # Detail & result
│       │   └── certificate.blade.php # View certificate
routes/
└── web.php (tambah routes)
```

---

## 🖥️ User Interface Design

### Homepage Button (Sebelah Unggah Mandiri)

```html
<!-- Existing Unggah -->
<a href="..." class="bg-gradient-to-r from-purple-500 to-indigo-600 ...">
    Unggah Tugas Akhir
</a>

<!-- NEW: Cek Plagiasi -->
<a href="{{ route('opac.member.plagiarism.create') }}" 
   class="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-xl p-4 text-white ...">
    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-shield-check text-xl"></i>
    </div>
    <div>
        <h3 class="font-bold">Cek Plagiasi</h3>
        <p class="text-teal-200 text-xs">Scan dokumen & dapatkan sertifikat</p>
    </div>
</a>
```

### Member Dashboard Quick Action

Tombol ditambahkan di sebelah "Unggah Tugas Akhir" (warna teal/hijau):

```html
<a href="{{ route('opac.member.plagiarism.create') }}" 
   class="block bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl p-4 text-white 
          shadow-lg shadow-teal-500/30 hover:shadow-xl transition-all group">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-search-plus text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-sm lg:text-base">Cek Plagiasi</h3>
            <p class="text-teal-200 text-xs lg:text-sm">Dapatkan sertifikat plagiasi</p>
        </div>
        <i class="fas fa-chevron-right text-white/60 group-hover:translate-x-1 transition"></i>
    </div>
</a>
```

### Upload Form (Create Page)

```
┌──────────────────────────────────────────────────────────────┐
│  🛡️ Cek Plagiasi Dokumen                                    │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐│
│  │  Informasi Pengaju (Auto-filled from Member)            ││
│  │  ┌────────────────┐  ┌────────────────┐                 ││
│  │  │ Nama:          │  │ NIM:           │                 ││
│  │  │ Ahmad Fulan    │  │ 2020xxxx       │                 ││
│  │  └────────────────┘  └────────────────┘                 ││
│  │  ┌────────────────┐  ┌────────────────┐                 ││
│  │  │ Prodi:         │  │ Fakultas:      │                 ││
│  │  │ Informatika    │  │ Sains & Tech   │                 ││
│  │  └────────────────┘  └────────────────┘                 ││
│  └─────────────────────────────────────────────────────────┘│
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐│
│  │        📄 Upload Dokumen                                 ││
│  │                                                          ││
│  │    ┌─────────────────────────────────────────┐          ││
│  │    │                                         │          ││
│  │    │     📄 Drag & Drop file di sini         │          ││
│  │    │     atau klik untuk memilih             │          ││
│  │    │                                         │          ││
│  │    │     Format: PDF, DOCX (Max 20MB)       │          ││
│  │    │                                         │          ││
│  │    └─────────────────────────────────────────┘          ││
│  └─────────────────────────────────────────────────────────┘│
│                                                              │
│  [ ] Saya menyatakan dokumen ini adalah karya saya sendiri  │
│                                                              │
│           ┌────────────────────────────────────┐            │
│           │   🔍 Mulai Pengecekan Plagiasi     │            │
│           └────────────────────────────────────┘            │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### Result Page (Show)

```
┌──────────────────────────────────────────────────────────────┐
│  📊 Hasil Pengecekan Plagiasi                                │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────┐    ┌─────────────────────────────────┐ │
│  │                 │    │  📄 Nama_File_Skripsi.pdf       │ │
│  │      12%       │    │  Diupload: 10 Des 2024, 14:30   │ │
│  │  ────────────   │    │  Ukuran: 2.5 MB | 85 halaman   │ │
│  │  Similarity    │    │                                  │ │
│  │                 │    │  Status: ✅ Selesai             │ │
│  └─────────────────┘    └─────────────────────────────────┘ │
│                                                              │
│  ┌─ Similarity Score Legend ────────────────────────────────┐│
│  │ 🟢 0-15%  : Rendah (Baik)                               ││
│  │ 🟡 16-25% : Sedang (Perlu Review)                       ││
│  │ 🟠 26-40% : Tinggi (Perlu Perbaikan)                    ││
│  │ 🔴 >40%   : Sangat Tinggi (Revisi Mayor)               ││
│  └──────────────────────────────────────────────────────────┘│
│                                                              │
│  ┌─ Top Matched Sources ────────────────────────────────────┐│
│  │ 1. Repository UNIDA - Skripsi 2019      : 4%            ││
│  │ 2. Journal of Islamic Education         : 3%            ││
│  │ 3. Wikipedia - "Islamic Finance"        : 2%            ││
│  └──────────────────────────────────────────────────────────┘│
│                                                              │
│  ┌───────────────────────────────────────────────────────┐  │
│  │ 📜 SERTIFIKAT PLAGIASI                                │  │
│  │    No: PLAG-UNIDA-2024-00123                          │  │
│  │                                                        │  │
│  │    ┌─────────────────┐   ┌─────────────────┐          │  │
│  │    │ 👁️ Lihat        │   │ ⬇️ Unduh PDF    │          │  │
│  │    └─────────────────┘   └─────────────────┘          │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## 🎖️ Sertifikat Plagiasi Design

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   [LOGO UNIDA]        UNIVERSITAS DARUSSALAM GONTOR             │
│                       PERPUSTAKAAN                               │
│                                                                 │
│   ════════════════════════════════════════════════════════════  │
│                                                                 │
│                  SERTIFIKAT HASIL CEK PLAGIASI                  │
│                  No: PLAG-UNIDA-2024-00123                     │
│                                                                 │
│   ════════════════════════════════════════════════════════════  │
│                                                                 │
│   Yang bertanda tangan di bawah ini menerangkan bahwa:         │
│                                                                 │
│   Nama        : Ahmad Fulan                                     │
│   NIM         : 2020123456                                      │
│   Program Studi: Teknik Informatika                             │
│   Fakultas    : Sains dan Teknologi                             │
│                                                                 │
│   Telah melakukan pengecekan plagiasi terhadap dokumen:         │
│                                                                 │
│   Judul       : "Implementasi Sistem Informasi ..."             │
│   Tanggal     : 10 Desember 2024                                │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │                                                         │   │
│   │              HASIL: 12% SIMILARITY                      │   │
│   │              ✅ DINYATAKAN LOLOS                        │   │
│   │                                                         │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   Sertifikat ini dikeluarkan secara otomatis oleh sistem        │
│   Perpustakaan UNIDA Gontor dan dapat diverifikasi di:          │
│   https://library.unida.gontor.ac.id/verify/PLAG-UNIDA-xxxxx   │
│                                                                 │
│                        Ponorogo, 10 Desember 2024               │
│                                                                 │
│                        [QR CODE]                                │
│                                                                 │
│                        Kepala Perpustakaan                      │
│                        _____________________                    │
│                        Nama Kepala Perpus                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔧 Technical Implementation

### 1. Routes

```php
// routes/web.php
Route::middleware(['auth:member'])->prefix('member')->name('opac.member.')->group(function () {
    // Plagiarism Check
    Route::prefix('plagiarism')->name('plagiarism.')->group(function () {
        Route::get('/', [PlagiarismController::class, 'index'])->name('index');
        Route::get('/create', [PlagiarismController::class, 'create'])->name('create');
        Route::post('/', [PlagiarismController::class, 'store'])->name('store');
        Route::get('/{check}', [PlagiarismController::class, 'show'])->name('show');
        Route::get('/{check}/certificate', [PlagiarismController::class, 'certificate'])->name('certificate');
        Route::get('/{check}/certificate/download', [PlagiarismController::class, 'downloadCertificate'])->name('certificate.download');
    });
});

// Public verify
Route::get('/verify/{certificate}', [PlagiarismController::class, 'verify'])->name('plagiarism.verify');
```

### 2. Controller

```php
class PlagiarismController extends Controller
{
    public function create()
    {
        $member = auth('member')->user();
        
        // Get member info (auto-filled)
        return view('opac.member.plagiarism.create', [
            'member' => $member,
            // Optionally get thesis submission list for linking
            'submissions' => $member->thesisSubmissions()->latest()->get(),
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx|max:20480', // 20MB
            'thesis_submission_id' => 'nullable|exists:thesis_submissions,id',
            'agreement' => 'required|accepted',
        ]);
        
        $member = auth('member')->user();
        $file = $request->file('file');
        
        // Store file
        $path = $file->store('plagiarism-checks', 'local');
        
        // Create check record
        $check = PlagiarismCheck::create([
            'member_id' => $member->id,
            'thesis_submission_id' => $request->thesis_submission_id,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);
        
        // Dispatch background job
        ProcessPlagiarismCheck::dispatch($check);
        
        return redirect()->route('opac.member.plagiarism.show', $check)
            ->with('success', 'Dokumen berhasil diupload. Pengecekan sedang diproses.');
    }
}
```

### 3. Background Job

```php
class ProcessPlagiarismCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(public PlagiarismCheck $check) {}
    
    public function handle(PlagiarismService $service): void
    {
        $this->check->update(['status' => 'processing', 'started_at' => now()]);
        
        try {
            $result = $service->check($this->check);
            
            $this->check->update([
                'status' => 'completed',
                'similarity_score' => $result['score'],
                'similarity_sources' => $result['sources'],
                'detailed_report' => $result['report'],
                'completed_at' => now(),
            ]);
            
            // Generate certificate
            $service->generateCertificate($this->check);
            
        } catch (\Exception $e) {
            $this->check->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
```

---

## 📌 Implementation Phases

### Phase 1: Basic Infrastructure (Week 1)
- [ ] Database migrations
- [ ] Models & relationships
- [ ] Basic routes & controllers
- [ ] File upload functionality
- [ ] Simple UI (upload + result page)

### Phase 2: Internal Checking (Week 2)
- [ ] Basic text extraction (PDF/DOCX)
- [ ] Compare with existing e-thesis database
- [ ] Simple similarity calculation
- [ ] Result display

### Phase 3: Certificate System (Week 3)
- [ ] Certificate number generator
- [ ] PDF certificate generation
- [ ] QR code verification
- [ ] Public verification page

### Phase 4: External Integration (Future)
- [ ] Turnitin API integration (if license available)
- [ ] OR Copyleaks integration
- [ ] Enhanced similarity detection

---

## 📝 Notes

### Turnitin API Requirements:
1. **Lisensi Turnitin** - Biasanya melalui institusi atau dikti
2. **Consumer Key & Shared Secret** - Dari admin Turnitin
3. **LTI 1.3 preferred** - Lebih secure dengan OAuth 2.0

### Alternative: Simple Internal Check
Jika tidak ada lisensi Turnitin, gunakan pendekatan internal:
1. Extract text dari PDF/DOCX
2. Compare dengan database e-thesis yang sudah ada
3. Gunakan algorithm cosine similarity atau Jaccard
4. Hasilnya tidak seakurat Turnitin tapi cukup untuk internal

### Recommended Provider Hierarchy:
1. **Turnitin** (jika ada lisensi institusi)
2. **Copyleaks** (alternatif berbayar, API friendly)
3. **Internal** (free, basic comparison)

---

## ✅ Action Items

1. **Confirm dengan user:**
   - Apakah UNIDA memiliki lisensi Turnitin?
   - Jika tidak, apakah ingin menggunakan Copyleaks (berbayar)?
   - Atau mulai dengan internal checking dulu?

2. **Next Steps setelah approval:**
   - Create migrations
   - Build models
   - Implement UI
   - Setup queue worker
   - Test & deploy

---

*Dokumen ini dibuat: 10 Desember 2024*
*Status: Draft - Menunggu Approval*
