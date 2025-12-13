# Migrasi dari iThenticate ke Turnitin

## Overview

iThenticate dan Turnitin menggunakan **API yang sama** yaitu **Turnitin Core API (TCA)**. Migrasi hanya memerlukan perubahan konfigurasi, tidak perlu perubahan kode.

## Langkah Migrasi

### 1. Dapatkan Kredensial Turnitin Baru

1. Login ke **Turnitin Admin Console** dengan akun administrator
2. Buka menu **Integrations**
3. Klik **Generate TCA Scope** atau pilih integration yang sudah ada
4. Buat API Key baru:
   - Klik **Create API Key**
   - Beri nama (misal: "Library-Portal")
   - Klik **Create and View**
   - **PENTING**: Salin Secret Key (hanya ditampilkan sekali!)

### 2. Update Konfigurasi di Admin Panel

1. Login ke Admin Panel (`/admin`)
2. Buka **Pengaturan Aplikasi** → Tab **Plagiasi**
3. Di bagian **Turnitin Core API (TCA)**:
   - **API Base URL**: Ganti ke tenant Turnitin baru
     - Format: `https://[nama-tenant].turnitin.com`
     - Contoh: `https://unidagontor.turnitin.com`
   - **API Secret Key**: Masukkan Secret Key dari langkah 1
   - **Integration Name**: Opsional, untuk identifikasi
4. Klik **Test Koneksi API** untuk verifikasi
5. Simpan pengaturan

### 3. Verifikasi

- Submit dokumen test untuk memastikan integrasi berfungsi
- Cek hasil similarity report

## Perbedaan iThenticate vs Turnitin

| Aspek | iThenticate | Turnitin |
|-------|-------------|----------|
| Target User | Researcher, Publisher | Akademik (Universitas) |
| Database | Sama | Sama |
| API | TCA (sama) | TCA (sama) |
| Fitur | Similarity check | Similarity + LMS integration |

## Troubleshooting

### Error: "Invalid authorization header"
- Pastikan Secret Key benar
- Secret Key digunakan langsung sebagai Bearer token

### Error: "Connection timeout"
- Cek koneksi internet ke server Turnitin
- Coba ping: `ping [tenant].turnitin.com`

### Error: "Quota exceeded"
- Kuota submission habis
- Hubungi Turnitin untuk renewal/upgrade

## Referensi

- [Turnitin Core API Documentation](https://developers.turnitin.com/docs)
- [TCA OpenAPI Spec](https://github.com/turnitin/turnitin-core-api)
- [TCA FAQ](https://developers.turnitin.com/turnitin-core-api/faq)
