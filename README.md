# SistemSlipGajiKemenag

Sistem Informasi Slip Gaji Kementerian Agama Provinsi Lampung

## About

Sistem Slip Gaji Kemenag Lampung adalah aplikasi web untuk mengelola data pegawai dan slip gaji di lingkungan Kementerian Agama Provinsi Lampung. Aplikasi ini dibangun menggunakan Laravel dengan fitur-fitur berikut:

- **Manajemen Pegawai**: CRUD data pegawai dengan import dari Excel
- **Slip Gaji**: Generate dan kelola slip gaji bulanan
- **Import Excel**: Import data pegawai dan gaji dari file Excel
- **Role-based Access**: Admin dan Pegawai dengan hak akses berbeda
- **API Endpoints**: RESTful API untuk integrasi dengan React frontend

## Tech Stack

- **Backend**: Laravel 11
- **Database**: MySQL
- **Frontend**: Blade Templates + Tailwind CSS
- **Excel Import**: Maatwebsite Excel
- **Authentication**: Laravel Sanctum (untuk API)

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

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Setup database:
```bash
php artisan migrate
php artisan db:seed
```

6. Create storage link:
```bash
php artisan storage:link
```

7. Start development server:
```bash
php artisan serve
```

## API Documentation

Lihat file [API_DOCUMENTATION.md](API_DOCUMENTATION.md) untuk dokumentasi API lengkap.

## License

Proprietary - Kementerian Agama Provinsi Lampung

## Developer

Developed by Jana Rohman