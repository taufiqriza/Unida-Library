# Firebase Setup Guide - UNIDA Library Mobile App

## 1. Membuat Project Firebase

### Langkah-langkah:
1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Klik "Add project" / "Tambah project"
3. Nama project: `unida-library` atau `perpustakaan-unida`
4. Disable Google Analytics (opsional, untuk mempercepat)
5. Klik "Create project"

## 2. Menambahkan Aplikasi

### Android App:
1. Di Firebase Console, klik ikon Android
2. Isi detail:
   - Package name: `ac.id.unida.gontor.library`
   - App nickname: `UNIDA Library`
   - SHA-1: (dapatkan dari Flutter project nanti)
3. Download `google-services.json`
4. Simpan di `android/app/google-services.json`

### iOS App:
1. Di Firebase Console, klik ikon iOS
2. Isi detail:
   - Bundle ID: `ac.id.unida.gontor.library`
   - App nickname: `UNIDA Library`
3. Download `GoogleService-Info.plist`
4. Simpan di `ios/Runner/GoogleService-Info.plist`

## 3. Mendapatkan Server Key untuk Backend

### Firebase Admin SDK (Recommended):
1. Di Firebase Console → Project Settings → Service Accounts
2. Klik "Generate new private key"
3. Download JSON file
4. Simpan sebagai `firebase-service-account.json`
5. **JANGAN commit file ini ke git!**

### Legacy Server Key (Alternative):
1. Di Firebase Console → Project Settings → Cloud Messaging
2. Copy "Server key" (Legacy)
3. Simpan di `.env` sebagai `FCM_SERVER_KEY`

## 4. Konfigurasi Laravel Backend

### Install Package:
```bash
composer require kreait/laravel-firebase
```

### Environment Variables (.env):
```env
# Firebase Configuration
FIREBASE_CREDENTIALS=firebase-service-account.json
FIREBASE_PROJECT_ID=unida-library

# Alternative: Legacy FCM (jika tidak pakai Admin SDK)
FCM_SERVER_KEY=your_legacy_server_key_here
```

### Config File (config/firebase.php):
```php
<?php
return [
    'credentials' => [
        'file' => storage_path('app/firebase/' . env('FIREBASE_CREDENTIALS', 'firebase-service-account.json')),
    ],
    'project_id' => env('FIREBASE_PROJECT_ID'),
];
```

## 5. Struktur File

```
perpustakaan/
├── storage/
│   └── app/
│       └── firebase/
│           └── firebase-service-account.json  (JANGAN COMMIT!)
├── .env
└── config/
    └── firebase.php
```

## 6. Testing FCM

### Via Firebase Console:
1. Firebase Console → Cloud Messaging → Compose notification
2. Masukkan title dan body
3. Pilih target: Single device → paste FCM token
4. Send test message

### Via Laravel Tinker:
```php
$service = app(\App\Services\FirebaseService::class);
$service->sendToDevice('fcm_token_here', 'Test Title', 'Test Body');
```

## 7. Notification Types

| Type | Trigger | Data |
|------|---------|------|
| `loan_due_reminder` | 3 hari sebelum jatuh tempo | loan_id, book_title |
| `loan_due_today` | Hari jatuh tempo | loan_id, book_title |
| `loan_overdue` | Setelah jatuh tempo | loan_id, book_title, days_overdue |
| `reservation_ready` | Buku reservasi tersedia | book_id, book_title |
| `submission_status` | Status submission berubah | submission_id, status |
| `plagiarism_complete` | Cek plagiasi selesai | check_id, score |
| `clearance_approved` | Surat bebas pustaka disetujui | letter_id |

## 8. Security Notes

- **JANGAN** commit `firebase-service-account.json` ke repository
- Tambahkan ke `.gitignore`:
  ```
  storage/app/firebase/*.json
  firebase-service-account.json
  ```
- Gunakan environment variables untuk credentials
- Rotate server key secara berkala

---

*Last updated: 1 Januari 2026*
