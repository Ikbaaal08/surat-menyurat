# 🔧 Panduan Implementasi Pembatasan Akses

## Perubahan yang Sudah Dilakukan

### 1. ✅ Model Bidang
- File: `app/Models/Bidang.php`
- Relasi dengan User sudah ada
- Relasi dengan Letter sudah ada

### 2. ✅ Migration Bidang
- Tabel `bidangs` sudah dibuat
- Kolom `bidang_id` sudah ditambahkan ke tabel `users`
- Kolom `bidang_id` sudah ditambahkan ke tabel `letters`

### 3. ✅ Controller Bidang
- File: `app/Http/Controllers/BidangController.php` (BARU)
- CRUD untuk manajemen bidang oleh admin

### 4. ✅ PageController - Dashboard
- Statistik sudah difilter berdasarkan peran
- Admin: melihat semua data
- User bidang: hanya melihat data mereka sendiri

### 5. ✅ IncomingLetterController & OutgoingLetterController
- Sudah ada validasi bidang_id saat store
- Sudah ada pembatasan edit bidang_id untuk non-admin

## Perubahan yang Perlu Dilakukan Manual

### 1. 📝 Update Model Letter - scopeRender

Buka file: `app/Models/Letter.php`

Cari method `scopeRender` (sekitar baris 113-129) dan ubah menjadi:

```php
public function scopeRender($query, $search)
{
    // User bidang hanya bisa lihat surat yang mereka input sendiri
    if (auth()->check() && auth()->user()->role != \App\Enums\Role::ADMIN->status()) {
        $query->where('user_id', auth()->id());
    }

    return $query
        ->with(['attachments', 'classification'])
        ->search($search)
        ->latest('letter_date')
        ->paginate((int)(Config::getValueByCode(ConfigEnum::PAGE_SIZE) ?? 15))
        ->appends([
            'search' => $search,
        ]);
}
```

**Perubahan:** Ganti `where('bidang_id', $bidangId)` menjadi `where('user_id', auth()->id())`

### 2. 📝 Update Model Disposition - Tambah Scope

Buka file: `app/Models/Disposition.php`

Tambahkan method scope berikut:

```php
public function scopeToday($query)
{
    return $query->whereDate('created_at', now());
}

public function scopeYesterday($query)
{
    return $query->whereDate('created_at', now()->addDays(-1));
}
```

### 3. 📝 Tambahkan Route untuk Bidang

Buka file: `routes/web.php`

Tambahkan route berikut di dalam group middleware auth dan role admin (sekitar baris 59-62):

```php
Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
    Route::resource('classification', \App\Http\Controllers\ClassificationController::class)->except(['show', 'create', 'edit']);
    Route::resource('status', \App\Http\Controllers\LetterStatusController::class)->except(['show', 'create', 'edit']);
    Route::resource('bidang', \App\Http\Controllers\BidangController::class)->except(['show', 'create', 'edit']); // TAMBAHKAN INI
});
```

### 4. 📝 Buat View untuk Manajemen Bidang

Buat folder dan file baru: `resources/views/pages/bidang/index.blade.php`

Contoh konten (sesuaikan dengan template Sneat yang digunakan):

```blade
@extends('layouts.app')

@section('title', 'Manajemen Bidang')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Referensi /</span> Manajemen Bidang
    </h4>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Bidang</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bx bx-plus me-1"></i> Tambah Bidang
            </button>
        </div>

        <div class="card-body">
            <!-- Search -->
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari bidang..." value="{{ $search }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bx bx-search"></i> Cari
                    </button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bidang</th>
                            <th>Jumlah Pengguna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $bidang)
                        <tr>
                            <td>{{ $data->firstItem() + $index }}</td>
                            <td>{{ $bidang->nama_bidang }}</td>
                            <td>
                                <span class="badge bg-info">{{ $bidang->users_count }} pengguna</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit{{ $bidang->id }}">
                                    <i class="bx bx-edit"></i> Edit
                                </button>
                                
                                @if($bidang->users_count == 0)
                                <form action="{{ route('reference.bidang.destroy', $bidang->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus bidang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $bidang->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Bidang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('reference.bidang.update', $bidang->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Bidang</label>
                                                <input type="text" name="nama_bidang" class="form-control" 
                                                       value="{{ $bidang->nama_bidang }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data bidang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Bidang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reference.bidang.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bidang</label>
                        <input type="text" name="nama_bidang" class="form-control" 
                               placeholder="Contoh: Bidang Bina Marga" required>
                        <small class="text-muted">Masukkan nama bidang kerja di Dinas PUPR</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

### 5. 📝 Update View User (Tambah Dropdown Bidang)

Buka file: `resources/views/pages/user.blade.php`

Pada form tambah/edit user, tambahkan field untuk memilih bidang:

```blade
<div class="mb-3">
    <label class="form-label">Bidang</label>
    <select name="bidang_id" class="form-select" required>
        <option value="">Pilih Bidang</option>
        @foreach($bidangs as $bidang)
            <option value="{{ $bidang->id }}" {{ old('bidang_id', $user->bidang_id ?? '') == $bidang->id ? 'selected' : '' }}>
                {{ $bidang->nama_bidang }}
            </option>
        @endforeach
    </select>
</div>
```

### 6. 📝 Update Seeder untuk Data Dummy

Buat seeder untuk bidang: `database/seeders/BidangSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    public function run()
    {
        $bidangs = [
            ['nama_bidang' => 'Bidang Bina Marga'],
            ['nama_bidang' => 'Bidang Cipta Karya'],
            ['nama_bidang' => 'Bidang Sumber Daya Air'],
            ['nama_bidang' => 'Bidang Penataan Bangunan'],
            ['nama_bidang' => 'Sekretariat'],
        ];

        foreach ($bidangs as $bidang) {
            Bidang::create($bidang);
        }
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=BidangSeeder
```

## Testing Sistem

### Test sebagai Admin:
1. Login sebagai admin
2. Buka menu Referensi > Bidang
3. Tambah beberapa bidang
4. Buat user dan assign ke bidang
5. Lihat dashboard - harus menampilkan semua data
6. Lihat surat masuk/keluar - harus menampilkan semua surat

### Test sebagai User Bidang:
1. Login sebagai user bidang
2. Input beberapa surat masuk/keluar
3. Lihat dashboard - harus hanya menampilkan data yang user input sendiri
4. Lihat surat masuk/keluar - harus hanya menampilkan surat yang user input sendiri
5. Coba akses menu admin (bidang, user, settings) - harus ditolak (403)

### Test Isolasi Data:
1. Buat 2 user di bidang yang sama
2. Login sebagai user 1, input surat
3. Logout, login sebagai user 2
4. User 2 TIDAK BOLEH melihat surat yang diinput user 1
5. Login sebagai admin
6. Admin HARUS bisa melihat surat dari user 1 dan user 2

## Checklist Implementasi

- [x] Model Bidang
- [x] Migration Bidang
- [x] BidangController
- [x] Update PageController (Dashboard)
- [x] Update IncomingLetterController
- [x] Update OutgoingLetterController
- [ ] Update Letter Model - scopeRender (MANUAL)
- [ ] Update Disposition Model - add scopes (MANUAL)
- [ ] Tambah Route Bidang (MANUAL)
- [ ] Buat View Bidang (MANUAL)
- [ ] Update View User (MANUAL)
- [ ] Buat BidangSeeder (MANUAL)
- [ ] Testing

## Catatan Penting

⚠️ **PENTING:** Pastikan setiap user bidang memiliki `bidang_id` yang valid sebelum mereka bisa input surat.

⚠️ **KEAMANAN:** Sistem ini memastikan:
- User bidang HANYA bisa lihat surat yang mereka input sendiri
- User bidang TIDAK bisa mengubah bidang_id surat
- Admin bisa melihat dan mengelola semua data

⚠️ **PERFORMA:** Jika jumlah surat sangat banyak, pertimbangkan menambahkan index pada kolom:
- `letters.user_id`
- `letters.bidang_id`
- `users.bidang_id`

```sql
CREATE INDEX idx_letters_user_id ON letters(user_id);
CREATE INDEX idx_letters_bidang_id ON letters(bidang_id);
CREATE INDEX idx_users_bidang_id ON users(bidang_id);
```
