# PHP URL Shortener (PDO, MySQL/PostgreSQL)

Mini URL shortener dengan redirect & click counter. Support MySQL/PG melalui `.env`, UI Bootstrap, QR, Copy, dan pagination.

## Fitur
- Tambah URL, list, short redirect `/u/index.php?id=ID`
- Hit counter atomic (+1 per klik)
- Pagination + responsive table
- Tombol Copy + QR
- Switch **MySQL/PG** via `.env` tanpa ubah kode

## Setup
1. Import schema:
   - MySQL: `sql/schema.mysql.sql`
   - PostgreSQL: `sql/schema.pgsql.sql`
2. Copy `.env.example` → `.env` dan isi:
DB_DRIVER=mysql # atau pgsql
DB_HOST=localhost
DB_NAME=short-urls
DB_USER=root
DB_PASS=Kacir2211
BASE_URL=http://localhost/short-urls
3. Jalankan di XAMPP: `http://localhost/short-urls/public/index.php`

## Struktur
- `public/` : halaman utama & redirect
- `sql/` : schema MySQL/PG
- `config.php` : koneksi PDO + helper short link

## Roadmap
- Base62 shortcode (`/u/abc123`)
- .htaccess routing bersih
- Analytics (top clicks, range tanggal)

## Lisensi
MIT
