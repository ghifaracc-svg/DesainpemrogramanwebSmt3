# Security Checklist — Jobsheet 11

Audit keamanan terhadap kode Jobsheet 7–10 (SIMPUS-Mini).

| # | Kerentanan | Ditemukan di | Sebelum | Sesudah (perbaikan) |
|---|------------|--------------|---------|---------------------|
| 1 | SQL Injection | `buku/`, `anggota/`, `auth/` | Semua query sudah memakai prepared statement sejak jobsheet-08 | Diaudit ulang, sudah aman — tidak ada satupun query yang menyisipkan `$_POST`/`$_GET` langsung ke string SQL. Diuji input `' OR '1'='1` di form login → tidak berhasil bypass. |
| 2 | XSS | `buku/list.php`, `buku/edit.php`, `anggota/list.php`, `anggota/edit.php`, `includes/header.php`, flash message di semua halaman | Sebagian output memakai `htmlspecialchars()` tanpa `ENT_QUOTES`/UTF-8, sebagian lain (`flash['type']`) dicetak mentah | Semua output data dari database/`$_GET`/`$_SESSION` dibungkus `e()` (`includes/helpers.php`). Uji: simpan judul `<script>alert(1)</script>` → tampil sebagai teks, tidak ada pop-up. |
| 3 | CSRF | Semua form `method="post"` dan semua `proses_*.php` / `hapus.php` | Hanya dicek `REQUEST_METHOD === 'POST'` | `csrf_field()` ditambahkan ke semua form POST; `csrf_verify()` dipanggil sebelum menyentuh database. Uji: `curl -X POST .../buku/proses_tambah.php -d "judul=x"` tanpa token → HTTP 403. |
| 4 | Validasi & Sanitasi Input | `buku/proses_*.php`, `anggota/proses_*.php`, `auth/proses_register.php` | Validasi wajib-isi (`=== ''`) dan tipe (`is_numeric`) sudah ada | Ditambah type casting eksplisit `(int)` untuk `id` (dari `$_GET`/`$_POST`) dan saat mencetak `id`/tahun/stok di form; `id <= 0` ditolak. |
| 5 | Session Fixation | `auth/proses_login.php` | Sesi dipakai terus sebelum dan sesudah login | `session_regenerate_id(true)` dipanggil tepat setelah `password_verify()` berhasil, sebelum `$_SESSION['user_id']` diisi. |

## Catatan Implementasi

- Guard `includes/auth.php` selalu dijalankan **sebelum** `includes/csrf.php` di halaman proses — pengguna yang belum login langsung di-redirect ke login, tanpa pengecekan CSRF.
- Di `hapus.php`, pengecekan `REQUEST_METHOD` (redirect untuk GET) dilakukan sebelum `csrf_verify()`.
- Form pencarian (`method="get"`) sengaja **tidak** diberi token CSRF: hanya membaca data, tidak mengubah apa pun.
- Kolom `tahun` dan `stok` (INTEGER) dicetak tanpa `e()` karena nilainya selalu angka murni.
- `auth/logout.php` tidak diubah (`session_destroy()` sudah mengakhiri sesi).
