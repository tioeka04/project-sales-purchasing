# Sales & Purchasing

Aplikasi Sales + Purchasing cycle berbasis **Laravel 12** dan template **Gentelella** (Bootstrap 3), dengan hak akses per role memakai `spatie/laravel-permission`.

## Fitur

| Grup | Menu | Keterangan |
|------|------|------------|
| Master Data | Products, Suppliers, Customers | CRUD + pencarian + paginasi. Hapus ditolak bila data sudah dipakai transaksi. |
| Purchasing | Purchase Orders | Header + banyak line (baris produk dinamis). Alur `draft → approved → completed / cancelled`. Receive menambah stok. |
| Sales | Sales Orders | Pola sama dengan Purchasing. Qty tidak boleh melebihi stok (dicek saat simpan **dan** saat deliver). Deliver mengurangi stok. |
| Settings | Users, Roles & Permissions | Kelola user + role, centang permission per role. |

Rancangan teknis:

- **Thin controller** → validasi di Form Request, hitungan & simpan di `app/Services`, semua dalam `DB::transaction()` dengan row lock.
- Total line dan `grand_total` **selalu dihitung di server**; uang disimpan `decimal(15,2)`.
- Status memakai enum `App\Enums\OrderStatus`.
- Keamanan 3 lapis: middleware route (`auth`, `permission:*`), `authorize()` di Form Request, dan `@can` di Blade.
- Semua label lewat `__()` dengan `lang/en.json`.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000 lalu login.

Default database adalah **SQLite** (`database/database.sqlite`, otomatis dibuat Laravel). Untuk **MySQL (XAMPP / phpMyAdmin)** buat database `laravel_purchase_order_3`, lalu di `.env` ubah `DB_CONNECTION=mysql` dan isi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

> Aset Gentelella, Bootstrap, jQuery, Font Awesome sudah ada di `public/vendor`, jadi tidak perlu `npm install` untuk menjalankan aplikasi.

## Akun awal (hasil seeder)

| Email | Password | Role |
|-------|----------|------|
| `admin@example.com` | `password` | Admin |

**Ganti password ini sebelum aplikasi dipakai di luar lingkungan lokal.** Role lain (Purchasing Staff, Sales Staff, Manager) dibuat otomatis; buat usernya lewat menu *Settings → Users*.

## Matriks role

| Permission | Admin | Purchasing Staff | Sales Staff | Manager |
|------------|:----:|:----:|:----:|:----:|
| product / supplier / customer `.view` | ✔ | ✔ | ✔ | ✔ |
| product `.create/edit/delete` | ✔ | | | |
| supplier `.create/edit` | ✔ | ✔ | | |
| customer `.create/edit` | ✔ | | ✔ | |
| purchase-order `.view` | ✔ | ✔ | | ✔ |
| purchase-order `.create/edit` | ✔ | ✔ | | |
| purchase-order `.approve` | ✔ | | | ✔ |
| sales-order `.view` | ✔ | | ✔ | ✔ |
| sales-order `.create/edit` | ✔ | | ✔ | |
| sales-order `.approve` | ✔ | | | ✔ |
| `.delete` (supplier/customer/PO/SO), `user.manage`, `role.manage` | ✔ | | | |

`receive` / `deliver` / `cancel` memakai permission `*.edit` milik modulnya.

## Struktur

```
app/Enums/OrderStatus.php
app/Http/Controllers/{Auth,Master,Purchasing,Sales,Settings}
app/Http/Requests/{Master,Purchasing,Sales,Settings}
app/Models  app/Services
resources/views/{layouts,partials,components,master,purchasing,sales,settings}
database/{migrations,factories,seeders}
lang/en.json
```

## Test

```bash
php artisan test
```

Mencakup perhitungan total di server, alur status + perubahan stok, cek stok Sales, aturan validasi, matriks permission (403 saat akses URL langsung), dan login/logout.
