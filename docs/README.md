# 📚 Dokumentasi Sistem Perpustakaan UNIDA

## Index Dokumentasi

### Overview & Deployment
| Dokumen | Deskripsi |
|---------|-----------|
| [PROJECT_CONTEXT.md](./PROJECT_CONTEXT.md) | Overview proyek & tech stack |
| [DEPLOYMENT.md](./DEPLOYMENT.md) | Panduan deployment ke production |
| [STAFF_PORTAL_ARCHITECTURE.md](./STAFF_PORTAL_ARCHITECTURE.md) | Arsitektur staff portal |

### Fitur & Konfigurasi
| Dokumen | Deskripsi |
|---------|-----------|
| [member-registration.md](./member-registration.md) | Registrasi member dengan OTP verification |
| [staff-registration.md](./staff-registration.md) | Registrasi staff dengan approval workflow |
| [email-configuration.md](./email-configuration.md) | Setup SMTP untuk kirim email |
| [email.md](./email.md) | Daftar trusted email domains UNIDA |

### Integrasi External
| Dokumen | Deskripsi |
|---------|-----------|
| [PLAGIARISM_CHECK.md](./PLAGIARISM_CHECK.md) | Integrasi cek plagiasi |
| [OJS_INTEGRATION.md](./OJS_INTEGRATION.md) | Integrasi Open Journal Systems |
| [REPO_INTEGRATION.md](./REPO_INTEGRATION.md) | Integrasi Repository UNIDA |
| [KUBUKU_API_REQUIREMENTS.md](./KUBUKU_API_REQUIREMENTS.md) | Integrasi E-Book Kubuku |

### Migration
| Dokumen | Deskripsi |
|---------|-----------|
| [MIGRATION/SLIMS_MIGRATION.md](./MIGRATION/SLIMS_MIGRATION.md) | Migrasi data dari SLiMS |

---

## Quick Start

### Setup Email (Wajib untuk OTP)
1. Buka `/admin` → App Settings → Tab Email
2. Isi konfigurasi SMTP
3. Test dengan tombol "Kirim Test Email"

### Staff Approval
1. Staff daftar via `/register` → Tab Staff
2. Admin approve di `/staff/control`
