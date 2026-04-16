# 📋 Aplikasi Absensi Mobile — Backend API

Sistem backend untuk **aplikasi absensi mobile** berbasis lokasi (geofencing) yang dirancang untuk lingkungan sekolah. Dibangun menggunakan **Laravel 12**, **Filament 5**, dan **Laravel Sanctum 4**.

## ✨ Fitur Utama

### 📱 Mobile App (REST API)
- **Login** via NIS atau Email dengan token-based authentication (Sanctum)
- **Absensi** dengan foto selfie + validasi GPS (geofencing)
- **Pembatasan waktu absensi** otomatis berdasarkan jadwal agenda
- **Pembatasan kelas** — siswa hanya bisa absen di jadwal kelasnya
- **Pencegahan absen ganda** per hari per jadwal
- **Filter agenda** berdasarkan jenis kelamin dan agama
- **Riwayat kehadiran** (terkini & lengkap)
- **Update foto profil** dengan kompresi otomatis ke WebP
- **Force change password** untuk siswa baru hasil import
- **Statistik kehadiran** (hadir & tidak hadir)

### 🖥️ Admin Panel (`/admin`)
- **Dashboard** — Statistik total guru, siswa, dan kelas aktif
- **Data Siswa** — CRUD + Import CSV + Bulk Action (Luluskan, Floating, Pindah Kelas)
- **Data Guru** — CRUD + Import CSV + View detail kelas bimbingan
- **Data Kelas** — Auto-generate nama kelas + Salin formasi kelas antar tahun ajaran
- **Agenda** — Template kegiatan dengan pengaturan waktu absen & target peserta (gender/agama)
- **Jadwal** — Buat jadwal per tanggal + Push Notification (FCM) ke siswa
- **Kehadiran** — Monitoring real-time + Edit status langsung + Export Excel (XLSX)
- **Tahun Ajaran** — Manajemen tahun ajaran (hanya 1 aktif)
- **Pengaturan** — Konfigurasi geofencing (koordinat & radius) + branding aplikasi

### 👨‍🏫 Teacher Panel (`/teacher`)
- **Dashboard** — Statistik kelas yang diampu
- **Kehadiran** — Monitoring kehadiran siswa di kelas bimbingan
- **Kelas** — Lihat data kelas yang diampu

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 12 |
| Admin Panel | Filament 5 |
| Authentication | Laravel Sanctum 4 |
| Database | MySQL |
| Image Processing | Intervention Image |
| Push Notification | Firebase Cloud Messaging (FCM) v1 |
| API Documentation | Scramble (OpenAPI) |
| Export | Filament Export (XLSX) |
| PDF | DomPDF (opsional) |

## 📦 Instalasi & Setup

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- (Opsional) Firebase project untuk push notification

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd absensi-app-api

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di file .env
# DB_DATABASE=siasata_app_api
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migrasi
php artisan migrate

# 6. Buat symbolic link untuk storage
php artisan storage:link

# 7. Build assets
npm run build

# 8. Jalankan server
composer dev
# Atau manual:
php artisan serve
```

### Setup Firebase (Opsional — untuk Push Notification)

1. Buat project di [Firebase Console](https://console.firebase.google.com)
2. Download file `firebase_credential.json` dari Project Settings → Service Accounts
3. Simpan file di `storage/app/firebase_credential.json`

## 🔑 API Endpoints

### Authentication

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `POST` | `/api/auth/login` | Login via NIS/Email | ❌ |
| `POST` | `/api/auth/logout` | Logout (revoke token) | ✅ |
| `POST` | `/api/auth/change-password` | Ganti password & email | ✅ |

### User

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `GET` | `/api/me` | Data profil + jadwal hari ini + statistik | ✅ |
| `GET` | `/api/user/activity/latest` | 5 riwayat absensi terkini | ✅ |
| `GET` | `/api/user/activity/all` | Seluruh riwayat absensi | ✅ |
| `POST` | `/api/user/update/profile` | Update foto profil | ✅ |

### Attendance

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `POST` | `/api/attendance` | Submit absensi (foto + GPS) | ✅ |

### Contoh Request Login

```json
POST /api/auth/login
{
    "login": "P.12345",
    "password": "password123"
}
```

### Contoh Request Absensi

```
POST /api/attendance
Content-Type: multipart/form-data

schedule_id: 1
latitude: "-7.390022"
longtitude: "110.518086"
photo: [file]
```

### Dokumentasi API Lengkap

Setelah server berjalan, akses dokumentasi API otomatis di:
```
http://localhost:8000/docs/api
```

## 👤 Role & Akses

| Role | Akses |
|------|-------|
| `admin` | Admin Panel (`/admin`) — Full akses semua fitur |
| `teacher` | Teacher Panel (`/teacher`) — View kehadiran & kelas bimbingan |
| `student` | Mobile App (REST API) — Absensi & profil |

## 📖 Panduan Admin

### 🆕 Setup Pertama Kali

Lakukan langkah-langkah berikut **secara berurutan**:

1. **Pengaturan** (`/admin/manage-settings`)
   - Set koordinat sekolah (Latitude & Longitude)
   - Set radius absen (dalam meter)
   - Set nama aplikasi, URL, dan favicon

2. **Tahun Ajaran** (`/admin/academic-years`)
   - Buat tahun ajaran (contoh: `2025/2026`)
   - Aktifkan dengan toggle

3. **Data Guru** (`/admin/teachers`)
   - Unduh template CSV → isi data → Import
   - Atau tambah manual satu per satu
   - Password default guru: `guru123!`

4. **Data Kelas** (`/admin/school-classes`)
   - Buat kelas (contoh: grade=10, jurusan=RPL, urutan=1 → `10 RPL 1`)
   - Assign guru pembimbing
   - Pilih tahun ajaran aktif

5. **Data Siswa** (`/admin/users`)
   - Unduh template CSV → isi data → Import
   - Atau tambah manual satu per satu
   - Assign setiap siswa ke kelasnya
   - Password default siswa: NIS masing-masing
   - Siswa baru wajib ganti password saat login pertama

6. **Agenda** (`/admin/agendas`)
   - Buat template kegiatan (contoh: `Sholat Dzuhur`)
   - Atur jam mulai & batas akhir absensi
   - Set target peserta (semua/laki-laki/perempuan, semua agama/spesifik)

7. **Jadwal** (`/admin/schedules`)
   - Buat jadwal: pilih agenda + tanggal + kelas yang ikut
   - Push notification otomatis terkirim ke siswa terkait

### 🔄 Pergantian Tahun Ajaran

1. **Buat Tahun Ajaran Baru** → Aktifkan (TA lama otomatis non-aktif)
2. **Salin Kelas** → Klik "Salin Kelas" di halaman Kelas, pilih dari TA lama ke TA baru
3. **Luluskan Kelas 12** → Select siswa kelas 12 → Bulk Action "Luluskan Siswa Terpilih"
4. **Set Floating Kelas 10 & 11** → Bulk Action "Set Jadi Floating (Naik Kelas)"
5. **Pindahkan ke Kelas Baru** → Filter "Siswa Belum Ada Kelas" → Bulk Action "Pindahkan ke Kelas"
6. **Import Siswa Baru** (kelas 10) → Assign ke kelas di TA baru
7. **Update Guru Pembimbing** jika ada perubahan

## 📁 Struktur Project

```
absensi-app-api/
├── app/
│   ├── Filament/
│   │   ├── Exports/          # Exporter (Kehadiran XLSX)
│   │   ├── Imports/           # Importer (Siswa & Guru CSV)
│   │   ├── Pages/             # Halaman Pengaturan
│   │   ├── Resources/         # Resource Admin Panel
│   │   │   ├── AcademicYears/ # Tahun Ajaran
│   │   │   ├── Agendas/       # Agenda Kegiatan
│   │   │   ├── Attendances/   # Data Kehadiran
│   │   │   ├── Schedules/     # Jadwal
│   │   │   ├── SchoolClasses/ # Data Kelas
│   │   │   ├── Teachers/      # Data Guru
│   │   │   └── Users/         # Data Siswa
│   │   ├── Teacher/           # Resource Teacher Panel
│   │   └── Widgets/           # Dashboard Widgets
│   ├── Http/
│   │   ├── Controllers/api/   # API Controllers
│   │   └── Middleware/        # Custom Middleware
│   ├── Models/                # Eloquent Models
│   └── Providers/             # Service & Panel Providers
├── database/
│   └── migrations/            # Database Migrations
├── routes/
│   └── api.php                # API Routes
└── storage/
    └── app/
        └── firebase_credential.json  # (manual, tidak di-commit)
```

## 🗃️ Entity Relationship

```
AcademicYear 1──N SchoolClass N──1 User (teacher)
                      │
                      N
                      │
              ScheduleClass (pivot)
                 │         │
                 N         N
                 │         │
          FridaySchedule   Attendance──N──1 User (student)
                 │
                 N──1
                 │
               Agenda──N──1 User (teacher/imam)
```

## 📄 Lisensi

Project ini dikembangkan untuk keperluan internal sekolah.
