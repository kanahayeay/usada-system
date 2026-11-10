# Usada Bali

Sistem informasi berbasis web untuk mendokumentasikan dan menyediakan akses terhadap pengetahuan pengobatan tradisional Bali berdasarkan naskah *Tenung Wraspatikalpa*, koleksi IIIa.249/8 dari UPTD Gedong Kirtya Singaraja.

## Fitur Utama

- Pencarian informasi berdasarkan kalender Pawukon.
- Penyajian informasi penyakit, penyebab, ritual, dan pengobatan berdasarkan naskah.
- Glosarium istilah dalam naskah.

## Teknologi

- PHP 8.3
- CodeIgniter 4.7.6
- MySQL
- Bootstrap 5
- HTML, CSS, dan JavaScript

## Instalasi

### 1. Clone Repository
git clone https://github.com/tutsukardi/usada-system.git

### 2. Masuk ke Direktori Proyek
cd usada-system

### 3. Install Dependencies
composer install

### 4. Buat Database

Buat database MySQL dengan nama:
CREATE DATABASE usada_bali;

Database dapat dibuat melalui phpMyAdmin atau MySQL.

### 5. Konfigurasi Environment

Salin file konfigurasi bawaan `env` menjadi `.env`.

Untuk Windows PowerShell:
Copy-Item env .env

Untuk Linux/macOS:
cp env .env

Kemudian buka file `.env` dan sesuaikan konfigurasi berikut:

```ini
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = usada_bali
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Sesuaikan konfigurasi database dengan lingkungan lokal masing-masing.

**Catatan:** File `.env` tidak disertakan dalam repository. Setiap developer perlu membuat file tersebut secara mandiri berdasarkan template `env`.

### 6. Jalankan Aplikasi
php spark serve

### 7. Akses Aplikasi

Buka browser dan akses:

http://localhost:8080

## Status Pengembangan

Dalam tahap pengembangan.