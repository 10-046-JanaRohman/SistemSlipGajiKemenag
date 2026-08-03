# Sistem Slip Gaji Kemenag

Sistem Informasi Slip Gaji Kementerian Agama Provinsi Lampung untuk mengelola data pegawai, proses impor gaji, dan distribusi slip gaji.

## Tentang Proyek

Sistem Slip Gaji Kemenag Lampung adalah aplikasi web yang membantu pengelolaan data pegawai dan slip gaji di lingkungan Kementerian Agama Provinsi Lampung.

- **Manajemen Pegawai**: CRUD data pegawai dan impor dari Excel.
- **Slip Gaji**: Membuat, mengelola, dan melihat slip gaji bulanan.
- **Impor Excel**: Review serta impor data gaji dari file Excel.
- **Akses Berbasis Peran**: Hak akses untuk Super Admin, Admin, dan Pegawai.
- **REST API**: API untuk integrasi frontend React.

## Teknologi

- **Backend**: Laravel 11 dan PHP
- **Frontend**: React 19, Vite, dan Tailwind CSS
- **Database**: SQLite (default) atau MySQL
- **Excel Import**: Maatwebsite Excel
- **Autentikasi API**: Laravel Sanctum

## Prasyarat

Pastikan perangkat telah memiliki:

- PHP 8.2 atau versi yang kompatibel dengan Laravel 11
- Composer
- Node.js 20+ dan npm
- MySQL 8+ bila tidak menggunakan SQLite

## Instalasi

## Features

### Admin
- Dashboard dengan statistik
- Manajemen data pegawai (CRUD)
- Import data gaji dari Excel
- Kelola slip gaji
- Riwayat slip gaji

### Pegawai
- Dashboard pribadi
- Lihat slip gaji saya
- Riwayat slip gaji
- Profil pegawai
- Ganti password

## Installation

1. Clone repository:
```bash
git clone https://github.com/10-046-JanaRohman/SistemSlipGajiKemenag.git
cd SistemSlipGajiKemenag
```

2. Install dependency backend:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key dan siapkan database SQLite:
```bash
php artisan key:generate
php -r "file_put_contents('database/database.sqlite', '');"
```

5. Jalankan migrasi dan seeder:
```bash
php artisan migrate
php artisan db:seed
```

6. Create storage link:
```bash
php artisan storage:link
```

7. Jalankan backend:
```bash
php artisan serve
```

Backend berjalan secara default di `http://localhost:8000`.

## Konfigurasi Environment

Secara default, proyek menggunakan SQLite. Pastikan `.env` memuat konfigurasi berikut:

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
DB_CONNECTION=sqlite
```

Untuk MySQL, ubah bagian database di `.env` menjadi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

## Menjalankan Frontend React

Buka terminal baru dari direktori proyek, lalu jalankan:

```bash
cd frontend
npm install
npm run dev
```

Frontend berjalan secara default di `http://localhost:5173`. Pastikan backend Laravel telah berjalan sebelum menggunakan aplikasi.

## Build dan Pengujian

Build frontend untuk production:

```bash
cd frontend
npm run build
```

Jalankan test backend:

```bash
php artisan test
```

## API Documentation

Lihat file [API_DOCUMENTATION.md](API_DOCUMENTATION.md) untuk dokumentasi API lengkap.

## License

Proprietary - Kementerian Agama Provinsi Lampung

## Developer

Proyek ini dikembangkan secara kolaboratif oleh:

| Nama | Peran | Tanggung Jawab |
| --- | --- | --- |
| [Jana Rohman Wasiso](https://github.com/10-046-JanaRohman) | Backend Developer | Laravel, API, database, dan logika aplikasi |
| [Adelia Ramadani](https://github.com/07-183-AdeliaRamadani) | Frontend Developer | Antarmuka React dan pengalaman pengguna |
