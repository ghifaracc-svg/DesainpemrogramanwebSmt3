# SIMPUS-Mini — Jobsheet 11: Keamanan Web Dasar

Melanjutkan Jobsheet 10 (Autentikasi & Manajemen Sesi). Audit 5 kerentanan; detail di [`docs/security-checklist.md`](docs/security-checklist.md).

## Berkas baru / berubah
- `includes/helpers.php` — **baru**, fungsi `e()` (anti-XSS)
- `includes/csrf.php` — **baru**, `csrf_token()`, `csrf_field()`, `csrf_verify()`
- `includes/header.php` — require helpers & csrf, nama petugas dibungkus `e()`
- `auth/proses_login.php` — `csrf_verify()` + `session_regenerate_id(true)`
- Semua form POST — `csrf_field()`; semua `proses_*.php` & `hapus.php` — `csrf_verify()`
- Semua output data — dibungkus `e()`; `id` di-cast `(int)`

## Menjalankan
```
psql -d simpus_mini -f sql/01_buku_anggota.sql
psql -d simpus_mini -f sql/02_users.sql
php -S localhost:8000
```

## Cara menguji
1. **CSRF**: login lewat browser, lalu `curl -X POST http://localhost:8000/buku/proses_tambah.php -d "judul=x"` (dengan cookie sesi, tanpa `csrf_token`) → HTTP 403.
2. **XSS**: tambah buku berjudul `<script>alert(1)</script>` → Daftar Buku menampilkan teksnya, tanpa pop-up.
3. **Urutan guard**: login → buka Tambah Buku → logout → buka lagi Tambah Buku → diarahkan ke Login.
4. **SQL injection**: login dengan username `' OR '1'='1` → tetap "Username atau password salah".
