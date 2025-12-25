# 📋 Dokumentasi Sistem Surat Menyurat Dinas PUPR

## 🎯 Konsep Sistem

Sistem surat menyurat ini menerapkan **pembatasan akses berbasis peran (Role-Based Access Control)** dan **berbasis bidang kerja** untuk memastikan keamanan data, kejelasan tanggung jawab, serta transparansi kinerja dalam pengelolaan surat di lingkungan Dinas PUPR.

## 👥 Peran Pengguna

### 1. **Admin**
Admin memiliki kewenangan penuh dalam sistem:

#### Hak Akses Admin:
- ✅ **Manajemen Pengguna**
  - Membuat akun pengguna baru
  - Menentukan bidang kerja untuk setiap pengguna
  - Mengubah data pengguna
  - Menonaktifkan/mengaktifkan akun pengguna
  - Mereset password pengguna
  - Menghapus pengguna

- ✅ **Manajemen Bidang**
  - Membuat bidang kerja baru
  - Mengubah nama bidang
  - Menghapus bidang (jika tidak ada pengguna terkait)

- ✅ **Manajemen Referensi**
  - Mengelola klasifikasi surat
  - Mengelola status sifat surat

- ✅ **Pengaturan Sistem**
  - Konfigurasi kata sandi default
  - Mengatur jumlah data per halaman
  - Mengubah identitas aplikasi & institusi

- ✅ **Akses Data Menyeluruh**
  - Melihat **SEMUA** surat masuk dari semua bidang
  - Melihat **SEMUA** surat keluar dari semua bidang
  - Melihat **SEMUA** disposisi dari semua bidang
  - Melihat statistik lengkap per bidang
  - Melihat statistik lengkap per pengguna
  - Monitoring kinerja seluruh bidang dan pengguna

- ✅ **Manajemen Surat Lintas Bidang**
  - Dapat menginput surat untuk bidang manapun
  - Dapat mengubah bidang pemilik surat
  - Dapat memproses surat dari bidang manapun

### 2. **User Bidang (Staff)**
User bidang memiliki akses terbatas hanya pada data yang mereka tangani sendiri:

#### Hak Akses User Bidang:
- ✅ **Manajemen Surat Pribadi**
  - Menginput surat masuk (otomatis masuk ke bidang mereka)
  - Menginput surat keluar (otomatis masuk ke bidang mereka)
  - Mengubah surat yang mereka input sendiri
  - Menghapus surat yang mereka input sendiri
  - Melihat detail surat yang mereka tangani

- ✅ **Manajemen Disposisi**
  - Membuat disposisi untuk surat di bidang mereka
  - Mengubah disposisi yang mereka buat
  - Menghapus disposisi yang mereka buat

- ✅ **Statistik Individual**
  - Melihat jumlah surat masuk yang mereka input
  - Melihat jumlah surat keluar yang mereka input
  - Melihat jumlah disposisi yang mereka buat
  - Statistik hanya menampilkan data mereka sendiri

- ✅ **Agenda & Galeri**
  - Melihat agenda surat dari bidang mereka
  - Mencetak agenda surat dari bidang mereka
  - Melihat galeri lampiran surat dari bidang mereka

- ✅ **Profil Pribadi**
  - Mengubah data profil (nama, email, telepon)
  - Mengganti foto profil
  - Menonaktifkan akun sendiri

#### Pembatasan User Bidang:
- ❌ **TIDAK BISA** melihat surat dari bidang lain
- ❌ **TIDAK BISA** melihat surat yang diinput user lain (meskipun satu bidang)
- ❌ **TIDAK BISA** mengubah bidang surat
- ❌ **TIDAK BISA** melihat statistik user lain
- ❌ **TIDAK BISA** mengelola pengguna
- ❌ **TIDAK BISA** mengakses pengaturan sistem
- ❌ **TIDAK BISA** mengelola referensi data

## 🏢 Konsep Bidang Kerja

### Apa itu Bidang?
Bidang adalah unit kerja atau divisi dalam Dinas PUPR, misalnya:
- Bidang Bina Marga
- Bidang Cipta Karya
- Bidang Sumber Daya Air
- Bidang Penataan Bangunan
- dll.

### Relasi Bidang dengan Data

```
Bidang
  ├── User 1 (Staff)
  │   ├── Surat Masuk A
  │   ├── Surat Keluar B
  │   └── Disposisi C
  │
  ├── User 2 (Staff)
  │   ├── Surat Masuk D
  │   ├── Surat Keluar E
  │   └── Disposisi F
  │
  └── User 3 (Staff)
      ├── Surat Masuk G
      └── Surat Keluar H
```

### Aturan Penting:
1. **Setiap user bidang** harus terhubung dengan **satu bidang**
2. **Setiap surat** otomatis terhubung dengan **bidang dari user yang menginput**
3. **User bidang** hanya bisa melihat surat yang **mereka sendiri input**
4. **Admin** bisa melihat dan mengelola **semua surat dari semua bidang**

## 🔐 Mekanisme Keamanan Data

### 1. Autentikasi
- Login menggunakan email dan password
- Session-based authentication
- Logout otomatis setelah periode inaktif

### 2. Otorisasi Berbasis Peran
```php
// Middleware role untuk membatasi akses
Route::middleware(['role:admin'])->group(function () {
    // Hanya admin yang bisa akses
});

Route::middleware(['role:staff'])->group(function () {
    // Hanya staff yang bisa akses
});
```

### 3. Filtering Data Otomatis
Sistem secara otomatis memfilter data berdasarkan peran:

```php
// Di Model Letter (scopeRender)
if (auth()->user()->role != 'admin') {
    // User bidang hanya lihat surat dari bidang mereka
    $query->where('bidang_id', auth()->user()->bidang_id);
    // Tambahan: hanya surat yang mereka input sendiri
    $query->where('user_id', auth()->user()->id);
}
```

### 4. Validasi Kepemilikan
Sebelum mengubah atau menghapus data, sistem memvalidasi:
- Apakah user adalah admin? → Boleh akses semua
- Apakah user adalah pemilik data? → Boleh akses
- Selain itu → Ditolak (403 Forbidden)

## 📊 Dashboard & Statistik

### Dashboard Admin
Menampilkan statistik menyeluruh:
- Total surat masuk hari ini (semua bidang)
- Total surat keluar hari ini (semua bidang)
- Total disposisi hari ini (semua bidang)
- Total transaksi surat hari ini
- Jumlah pengguna aktif
- Persentase perubahan dibanding kemarin
- **Grafik per bidang** (opsional)
- **Tabel kinerja per user** (opsional)

### Dashboard User Bidang
Menampilkan statistik individual:
- Surat masuk yang **saya input** hari ini
- Surat keluar yang **saya input** hari ini
- Disposisi yang **saya buat** hari ini
- Total transaksi **saya** hari ini
- Persentase perubahan **kinerja saya** dibanding kemarin

> **Penting:** Setiap user bidang melihat dashboard yang berbeda sesuai data mereka masing-masing.

## 🗂️ Struktur Database

### Tabel: `bidangs`
```sql
- id (PK)
- nama_bidang (string)
- created_at
- updated_at
```

### Tabel: `users`
```sql
- id (PK)
- name
- email (unique)
- password
- phone
- role (admin/staff)
- is_active (boolean)
- profile_picture
- bidang_id (FK -> bidangs.id, nullable untuk admin)
- created_at
- updated_at
```

### Tabel: `letters`
```sql
- id (PK)
- reference_number
- agenda_number
- from
- to
- letter_date
- received_date
- description
- note
- type (incoming/outgoing)
- classification_code (FK)
- user_id (FK -> users.id) -- User yang menginput
- bidang_id (FK -> bidangs.id) -- Bidang pemilik surat
- created_at
- updated_at
```

### Relasi:
- `User` belongsTo `Bidang`
- `Bidang` hasMany `User`
- `Letter` belongsTo `User` (yang menginput)
- `Letter` belongsTo `Bidang`
- `User` hasMany `Letter`

## 🚀 Workflow Penggunaan

### Workflow Admin

1. **Login** sebagai admin
2. **Buat Bidang** (misal: Bidang Bina Marga)
3. **Buat User** dan assign ke bidang
4. **Monitor** semua aktivitas surat dari semua bidang
5. **Lihat laporan** kinerja per bidang atau per user
6. **Kelola** pengaturan sistem

### Workflow User Bidang

1. **Login** sebagai user bidang
2. **Input Surat Masuk** → Otomatis masuk ke bidang user
3. **Input Surat Keluar** → Otomatis masuk ke bidang user
4. **Buat Disposisi** untuk surat yang ada
5. **Lihat Dashboard** → Hanya statistik pribadi
6. **Cetak Agenda** → Hanya surat dari bidang sendiri

## 🎨 Fitur Tambahan

### 1. Pencarian & Filter
- Pencarian berdasarkan nomor surat, pengirim, penerima
- Filter berdasarkan tanggal
- Filter berdasarkan klasifikasi
- **Admin:** Bisa filter per bidang
- **User:** Otomatis terfilter ke data sendiri

### 2. Agenda Surat
- Lihat surat berdasarkan rentang tanggal
- Cetak agenda dalam format PDF
- **Admin:** Agenda semua bidang
- **User:** Agenda bidang sendiri

### 3. Galeri Lampiran
- Lihat semua lampiran surat (PDF, JPG, PNG)
- Download lampiran
- **Admin:** Lampiran semua surat
- **User:** Lampiran surat sendiri

### 4. Disposisi Surat
- Tambah catatan disposisi
- Tracking disposisi surat
- Hapus disposisi

## 🔧 Konfigurasi Sistem

### File `.env`
```env
APP_NAME="Sistem Surat Menyurat PUPR"
APP_LOCALE=id
DB_DATABASE=surat_menyurat
DB_USERNAME=root
DB_PASSWORD=
```

### Pengaturan Aplikasi
Admin dapat mengatur:
- **DEFAULT_PASSWORD**: Password default untuk user baru
- **PAGE_SIZE**: Jumlah data per halaman (pagination)
- **APP_NAME**: Nama aplikasi
- **INSTITUTION_NAME**: Nama institusi
- **INSTITUTION_ADDRESS**: Alamat institusi
- **INSTITUTION_PHONE**: Telepon institusi
- **INSTITUTION_EMAIL**: Email institusi

## 📝 Catatan Penting

### Keamanan
1. ✅ Setiap user hanya bisa akses data mereka sendiri
2. ✅ Admin memiliki akses penuh untuk monitoring
3. ✅ Password di-hash menggunakan bcrypt
4. ✅ CSRF protection aktif
5. ✅ Validasi input di sisi server
6. ✅ File upload dibatasi (PDF, JPG, PNG)

### Transparansi
1. ✅ Setiap surat tercatat siapa yang input
2. ✅ Timestamp created_at dan updated_at
3. ✅ Admin bisa tracking kinerja per user
4. ✅ Statistik real-time

### Tanggung Jawab
1. ✅ Setiap user bertanggung jawab atas data yang mereka input
2. ✅ Data terisolasi per user (kecuali admin)
3. ✅ Audit trail tersedia melalui user_id di setiap surat

## 🆘 Troubleshooting

### User tidak bisa input surat
- Pastikan user sudah di-assign ke bidang
- Pastikan akun user aktif (is_active = true)
- Pastikan user sudah login

### User bisa lihat surat user lain
- Periksa implementasi scope di Model Letter
- Pastikan middleware role berfungsi
- Periksa query di controller

### Admin tidak bisa lihat semua surat
- Pastikan role = 'admin' di database
- Periksa kondisi di scopeRender
- Clear cache: `php artisan cache:clear`

## 📞 Kontak & Support

Untuk pertanyaan atau bantuan lebih lanjut, hubungi:
- **Email**: admin@pupr.go.id
- **Telepon**: (021) xxx-xxxx

---

**Dibuat dengan ❤️ untuk Dinas PUPR**
**Versi: 2.0**
**Tanggal: Desember 2025**
