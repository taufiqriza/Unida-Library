AUDIT PROMPT: LARAVEL 12 + FILAMENT 3 + LIVEWIRE

Saya ingin Anda bertindak sebagai auditor arsitektur sistem, pakar keamanan web Laravel, analis performa database, dan konsultan skalabilitas tingkat enterprise.

Saya akan memberikan struktur dan kode proyek Laravel 12 + Filament 3 + Livewire.
Tugas Anda adalah melakukan audit lengkap, mendalam, dan terstruktur terhadap seluruh aspek sistem ini.

1. SECURITY ANALYSIS (FULL OWASP + LARAVEL-SPECIFIC)

Analisis semua potensi kerentanan berikut:
- SQL Injection (terutama raw query, search, filter, pagination)
- Mass Assignment vulnerabilities (fillable dan guarded)
- Cross-site scripting (XSS) pada Blade, Livewire, dan input publik
- Cross-site request forgery (CSRF)
- Authentication misconfigurations dan password policy
- Authorization bug (Gate, Policy, Filament permissions)
- Session hijacking dan session fixation
- File upload vulnerability untuk PDF, e-book, e-thesis
- Remote Code Execution (RCE)
- Directory traversal risk
- Exposed .env file
- Sensitive data exposure (password, token, secret)
- CORS misconfiguration
- Insecure redirects
- Livewire component security (public properties, action injection)
- Filament admin panel exposure
- API endpoint exposure
- Rate limiting deficiencies
- Logging data sensitif
- Storage visibility dan symlink risk

Periksa juga apakah ada package dengan CVE vulnerability.

Berikan rekomendasi security hardening untuk:
- Authentication dan Authorization
- File uploads dan validation
- Session dan cookie configuration
- CSRF dan XSS mitigation
- Server security headers (HSTS, CSP, X-Frame-Options, dll)
- Encryption best practices
- S3/MinIO/Cloudflare R2 bucket hardening
- PDF malware prevention
- Anti-enumeration (login dan search)

2. PERFORMANCE & QUERY OPTIMIZATION

Audit performa database dan Livewire:
- N+1 query detection
- Eager loading yang tidak digunakan
- Index database yang diperlukan
- Struktur tabel katalog, ebook, e-thesis, berita
- Bottleneck pada global search
- Rekomendasi MySQL fulltext vs Meilisearch/Elasticsearch
- Optimasi pagination
- Redis caching
- Query caching
- Optimasi Livewire untuk mengurangi re-render
- Filament table performance improvement
- Feasibility Laravel Scout dan Meilisearch
- Kelayakan penggunaan Laravel Octane (Swoole/RoadRunner)

3. ARCHITECTURE REVIEW

Audit arsitektur sistem:
- Struktur folder
- Modularitas
- Penempatan business logic
- Controller bloat
- Service layer usage
- Repository pattern feasibility
- Event-driven architecture
- Observers usage
- API design review
- Maintainability jangka panjang
- Scalability untuk data besar (katalog, ebook, e-thesis)

Berikan rekomendasi:
- Folder restructuring
- Domain-driven design (opsional)
- Separation of concerns

4. FILE STORAGE SYSTEM AUDIT

Audit keamanan dan performa penyimpanan file besar:
- Akses aman untuk file e-thesis, ebook, dan dokumen besar
- Signed URLs
- Anti-hotlink
- MIME validation
- Antivirus scanning suggestion
- Storage permission audit
- PDF preview security
- Bandwidth control
- Direct-download vs streaming

5. PUBLIC PAGE SECURITY AUDIT

Audit seluruh UI publik:
- Search bar
- Global search Livewire
- OPAC katalog
- Detail item
- News module
- Login
- Guest API endpoints

Periksa:
- XSS dari query user
- Filter yang tidak terproteksi
- Unauthenticated data exposure
- Broken access control
- Cache poisoning
- Error leakage

6. FINAL REPORT REQUIREMENTS

Buat laporan akhir dengan struktur:

A. Critical issues (High severity) dan solusi wajib
B. Medium severity issues dan rekomendasi
C. Low severity issues dan perbaikan opsional
D. Architecture improvement summary
E. Performance optimization checklist
F. Security hardening checklist (Laravel, Livewire, Filament, API, Storage, Server)
G. Production readiness summary

Setelah membaca struktur proyek yang saya berikan, lakukan audit menyeluruh, sangat mendalam, dan berikan laporan profesional selengkap mungkin.
