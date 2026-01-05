# 📚 Perpustakaan UNIDA - Documentation

Dokumentasi teknis untuk sistem perpustakaan digital UNIDA Gontor.

## 📁 Struktur Dokumentasi

```
docs/
├── README.md                      # File ini
├── ARCHITECTURE_REFACTORING.md    # Rencana refactoring arsitektur
├── API_MOBILE.md                  # Dokumentasi REST API untuk mobile app
└── old/                           # Arsip dokumentasi lama
    ├── MIGRATION/                 # Dokumentasi migrasi
    ├── API_SPECIFICATION.md       # Spesifikasi API lama
    ├── FIREBASE_SETUP.md          # Setup push notification
    └── ...                        # Dokumentasi lainnya
```

## 🔗 Quick Links

### Dokumentasi Aktif
- [Architecture Refactoring Plan](./ARCHITECTURE_REFACTORING.md) - Rencana peningkatan arsitektur
- [Mobile API Documentation](./API_MOBILE.md) - REST API untuk aplikasi mobile

### Arsip (Reference Only)
- [Firebase Setup](./old/FIREBASE_SETUP.md) - Konfigurasi push notification
- [Deployment Guide](./old/deployment-guide.md) - Panduan deployment
- [SLIMS Migration](./old/MIGRATION/SLIMS_MIGRATION.md) - Dokumentasi migrasi dari SLiMS

## ⚠️ Catatan

Folder `docs/` di-exclude dari git (`.gitignore`) untuk menghindari sync ke production server. Dokumentasi ini hanya untuk development reference.

---

*Last updated: 5 Januari 2026*
