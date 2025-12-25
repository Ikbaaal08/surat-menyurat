# 🔐 SISTEM SURAT MENYURAT DINAS PUPR v2.0
## Dengan Pembatasan Akses Berbasis Peran & Bidang Kerja

---

## 🎯 Apa yang Baru di Versi 2.0?

Sistem ini telah ditingkatkan dengan **pembatasan akses yang lebih ketat** untuk memastikan:

✅ **Keamanan Data** - Setiap user hanya bisa akses data mereka sendiri  
✅ **Kejelasan Tanggung Jawab** - Setiap surat tercatat siapa yang input  
✅ **Transparansi Kinerja** - Admin bisa monitoring kinerja per user  
✅ **Isolasi Data** - User tidak bisa lihat data user lain meski satu bidang  

---

## 📚 Dokumentasi Lengkap

Sistem ini dilengkapi dengan dokumentasi komprehensif:

### 1. 📖 **DOKUMENTASI_SISTEM.md**
   Penjelasan lengkap tentang:
   - Konsep sistem
   - Peran pengguna (Admin vs User Bidang)
   - Hak akses masing-masing peran
   - Mekanisme keamanan
   - Struktur database
   - Workflow penggunaan

### 2. 🔧 **PANDUAN_IMPLEMENTASI.md**
   Langkah-langkah teknis untuk:
   - Perubahan yang sudah dilakukan
   - Perubahan yang perlu dilakukan manual
   - Testing sistem
   - Checklist implementasi

### 3. 📋 **RINGKASAN.md**
   Ringkasan singkat berisi:
   - Konsep utama
   - Mekanisme keamanan
   - Contoh skenario
   - Testing checklist

### 4. 🎨 **DIAGRAM.md**
   Visualisasi sistem dengan:
   - Diagram struktur organisasi
   - Alur data surat
   - Filtering berdasarkan peran
   - Relasi database (ERD)

---

## 🚀 Quick Start

### Instalasi Awal
```bash
# Clone repository
git clone [repository-url]

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Seed data awal (admin, config, bidang)
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ConfigSeeder
php artisan db:seed --class=BidangSeeder

# (Opsional) Seed data dummy
php artisan db:seed

# Jalankan aplikasi
php artisan serve
```

### Login Default
| Email           | Password | Role  |
|-----------------|----------|-------|
| admin@admin.com | admin    | Admin |

---

## 🔑 Perbedaan Akses: Admin vs User Bidang

### 👨‍💼 ADMIN
```
✅ Lihat SEMUA surat dari SEMUA bidang
✅ Kelola pengguna (CRUD)
✅ Kelola bidang (CRUD)
✅ Kelola referensi (klasifikasi, status)
✅ Pengaturan sistem
✅ Statistik menyeluruh
✅ Monitoring kinerja per bidang & per user
```

### 👤 USER BIDANG
```
✅ Input surat masuk/keluar (otomatis ke bidang sendiri)
✅ Edit/hapus surat yang MEREKA INPUT SENDIRI
✅ Buat disposisi
✅ Lihat agenda surat dari bidang sendiri
✅ Statistik PRIBADI (hanya data mereka)

❌ TIDAK bisa lihat surat user lain
❌ TIDAK bisa ubah bidang surat
❌ TIDAK bisa akses menu admin
```

---

## 📊 Contoh Skenario Penggunaan

### Skenario: Bidang Bina Marga

```
Bidang Bina Marga
├── User A (Pak Budi)
│   ├── Input: Surat Masuk #001, #002
│   └── Lihat: Hanya #001, #002
│
├── User B (Bu Ani)
│   ├── Input: Surat Masuk #003, #004
│   └── Lihat: Hanya #003, #004
│
└── Admin
    └── Lihat: Semua (#001, #002, #003, #004)
```

**Kesimpulan:**
- Pak Budi TIDAK bisa lihat surat Bu Ani
- Bu Ani TIDAK bisa lihat surat Pak Budi
- Admin bisa lihat semua surat

---

## 🛠️ Fitur Utama

### Untuk Admin
- 📊 **Dashboard Menyeluruh** - Statistik semua bidang
- 👥 **Manajemen Pengguna** - CRUD user, assign bidang
- 🏢 **Manajemen Bidang** - CRUD bidang kerja
- 📁 **Manajemen Referensi** - Klasifikasi & status surat
- ⚙️ **Pengaturan Sistem** - Konfigurasi aplikasi
- 📈 **Monitoring** - Kinerja per bidang & per user

### Untuk User Bidang
- 📥 **Input Surat Masuk** - Otomatis masuk ke bidang user
- 📤 **Input Surat Keluar** - Otomatis masuk ke bidang user
- 📝 **Disposisi Surat** - Buat catatan disposisi
- 📅 **Agenda Surat** - Filter berdasarkan tanggal
- 🖼️ **Galeri Lampiran** - Lihat & download lampiran
- 📊 **Dashboard Pribadi** - Statistik data sendiri

---

## 🔐 Keamanan

Sistem ini menerapkan multiple layer security:

1. **Autentikasi** - Login dengan email & password
2. **Middleware Role** - Pembatasan akses route
3. **Model Scope** - Filter otomatis di query
4. **Controller Validation** - Validasi kepemilikan data
5. **Auto-assign Bidang** - User tidak bisa pilih bidang lain
6. **Edit Protection** - User tidak bisa ubah bidang surat

---

## 🗂️ Struktur Database

### Tabel Utama
- **bidangs** - Data bidang kerja
- **users** - Data pengguna (dengan bidang_id)
- **letters** - Data surat (dengan user_id & bidang_id)
- **dispositions** - Data disposisi surat
- **attachments** - Lampiran surat

### Relasi Penting
- User **belongsTo** Bidang
- Letter **belongsTo** User (yang input)
- Letter **belongsTo** Bidang
- Disposition **belongsTo** Letter & User

---

## 🧪 Testing

### Test Admin
```bash
1. Login sebagai admin
2. Buka /reference/bidang
3. Tambah bidang baru
4. Buat user dan assign ke bidang
5. Lihat dashboard - harus tampil semua data
6. Lihat surat - harus tampil semua surat
```

### Test User Bidang
```bash
1. Login sebagai user bidang
2. Input surat masuk & keluar
3. Lihat dashboard - hanya data sendiri
4. Lihat surat - hanya surat sendiri
5. Coba akses /reference/bidang - harus ditolak (403)
```

### Test Isolasi Data
```bash
1. Buat User A dan User B di bidang sama
2. Login User A, input surat
3. Login User B - tidak bisa lihat surat User A ✅
4. Login Admin - bisa lihat surat User A dan B ✅
```

---

## 📝 Catatan Penting

### ⚠️ Sebelum Digunakan
1. Pastikan sudah menjalankan semua migration
2. Seed data bidang dengan `BidangSeeder`
3. Setiap user bidang HARUS memiliki `bidang_id`
4. Update manual di `Letter.php` dan `Disposition.php` (lihat PANDUAN_IMPLEMENTASI.md)

### 🔧 Konfigurasi
- Ubah `APP_LOCALE=id` di `.env` untuk bahasa Indonesia
- Sesuaikan `PAGE_SIZE` di pengaturan sistem
- Set `DEFAULT_PASSWORD` untuk password user baru

---

## 📞 Support & Kontribusi

### Dokumentasi
- Baca `DOKUMENTASI_SISTEM.md` untuk detail lengkap
- Baca `PANDUAN_IMPLEMENTASI.md` untuk langkah teknis
- Baca `RINGKASAN.md` untuk overview cepat
- Baca `DIAGRAM.md` untuk visualisasi sistem

### Troubleshooting
- Periksa `storage/logs/laravel.log` untuk error
- Pastikan semua migration sudah dijalankan
- Clear cache: `php artisan cache:clear`
- Clear config: `php artisan config:clear`

---

## 📜 Lisensi

Berlisensi di bawah [MIT License](LICENSE).

---

## 🎨 Template

Proyek ini menggunakan template admin [Sneat](https://github.com/themeselection/sneat-html-admin-template-free).

---

**Dibuat dengan ❤️ untuk Dinas PUPR**  
**Versi 2.0 - Sistem Surat Menyurat dengan Pembatasan Akses Berbasis Peran & Bidang Kerja**

---

## 📸 Screenshot

![Dashboard](docs/laravel-surat-menyurat-v1.png)

---

## 🔄 Changelog

### v2.0 (Desember 2025)
- ✅ Implementasi pembatasan akses berbasis peran
- ✅ Implementasi pembatasan akses berbasis bidang
- ✅ User hanya bisa lihat surat yang mereka input sendiri
- ✅ Dashboard berbeda untuk admin dan user bidang
- ✅ Manajemen bidang untuk admin
- ✅ Auto-assign bidang saat input surat
- ✅ Proteksi edit bidang untuk user
- ✅ Dokumentasi lengkap sistem

### v1.0
- ✅ Fitur dasar surat masuk/keluar
- ✅ Disposisi surat
- ✅ Agenda & galeri
- ✅ Manajemen user
- ✅ Pengaturan sistem
