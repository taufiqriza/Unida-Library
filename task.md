Saya ingin Anda membangunkan dan mengimplementasikan sebuah sistem manajemen tugas internal ala Jira untuk project perpus ini. Sistem ini digunakan untuk perpustakaan saya yang memiliki banyak divisi kerja seperti Sirkulasi, Referensi, Digital Library, IT, Katalogisasi, dan lainnya. Buatkan seluruh struktur dan fungsi lengkap dari backend, frontend, database, logic, workflow, dan integrasi UI. dan sesuaikan dengan konsep yang sudah berjalan, multi brach terutamanya.

Berikut kebutuhan dan konsep sistemnya:

1. SISTEM ROLE & PERMISSION
Gunakan Spatie Permission atau Laravel Policies untuk membuat role dan izin:
- Gunakan role yang sudah ada.

Aturan:
- Admin mengatur semuanya.
- Superadmin dapat melihat semua laporan & overview.
- Koordinator hanya untuk divisinya.
- Staf hanya tugas yang terkait dengannya.

2. ENTITI DAN MODEL YANG DIPERLUKAN
Buat lengkap Model, Migration, Factory (jika perlu), Seeder:
- Division (nama divisi)
- Project (nama proyek, division_id)
- Task (judul, deskripsi, project_id, division_id, assigned_to, reported_by, status, priority, type, tags, due_date, attachments)
- TaskStatus (untuk workflow custom per proyek)
- Comment (komentar per task)
- ActivityLog (rekod perubahan: assignee, status, due date, dll.)
- TaskTemplate (untuk tugas rutin harian/mingguan)
- Attachment (fail lampiran task)

3. FITUR UTAMA SISTEM
Implementasikan:
- Dashboard ringkasan tugas (by status, overdue, per divisi)
- Kanban Board ala Jira, drag & drop antar status
- Workflow status: Backlog, To Do, In Progress, Review, Done
- Bisa custom status per project
- Form tambah tugas lengkap
- Notifikasi ketika tugas dibuat, di-assign, atau due date mendekati
- Activity Log otomatis
- Komentar real-time
- Upload lampiran (Gambar, PDF, ZIP)
- Filter tugas per divisi / staf / proyek / status
- API Endpoint (Laravel Sanctum)
- Export laporan PDF dan Excel

4. TUGAS RUTIN (RECURRING TASK)
Gunakan Laravel Scheduler:
- TaskTemplate (daily, weekly, monthly)
- Sistem auto-generate task baru berdasarkan template
- Contoh template: cek backup server, rekap sirkulasi harian, cek log cloudflare, dll.

5. KANBAN & UI FRONTEND
Gunakan Livewire / Inertia (pilih yang terbaik) untuk:
- Board drag & drop (ubah status task)
- Modal popup untuk task detail
- Comment section interaktif
- Realtime update bila ada pengguna lain mengubah sesuatu

6. NOTIFIKASI
Gunakan Laravel Notifications:
- Email + Database notifications
- Notifikasi bila:
  - Task baru assign
  - Status berubah ke Review
  - Due date H-3 dan H-1

7. EXPORT & LAPORAN
Tambahkan:
- Export Excel (maatwebsite/excel)
- Export PDF (dompdf/snappy)
- Laporan statistik:
  - Task selesai per divisi
  - Task overdue
  - Top performer staf
  - Laporan bulanan

8. API ENDPOINT
Buatkan REST API untuk akses mobile:
- login / token Sanctum
- get tasks, update status
- create comment
- list projects per division
- upload attachment

9. IMPLEMENTASI
Saya ingin Anda:
- Membuat semua migration lengkap
- Membuat semua model beserta relasi
- Membuat controller, service, dan policy
- Membuat UI dasar (kanban view, task table, task modal)
- Mengimplementasikan filter, drag & drop, activity log, recurring task
- Memperbaiki project saya secara langsung (generate file dan tulis dalam folder yang benar)

Gunakan standar coding yang rapi, modular, SOLID, dan mudah dikembangkan. Pastikan sistem berjalan harmonis seperti Jira tetapi lebih ringan, simpel, dan fokus untuk manajemen tugas internal perpustakaan saya.

Setelah selesai, berikan juga:
- Panduan instalasi + command artisan
- Struktur folder final
- Cara menambah divisi, proyek, dan workflow baru
- Testing unit bila perlu

Mulakan sekarang dan implementasikan semuanya ke dalam project Laravel saya.
Buat posisinya dibawah dashboard filament.
