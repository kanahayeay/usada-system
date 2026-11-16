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
Jalankan perintah berikut untuk menginstal seluruh dependencies:

composer install


### 4. Buat Database

Buat database MySQL dengan nama `usada_bali` melalui phpMyAdmin atau MySQL.

CREATE DATABASE usada_bali;


### 5. Konfigurasi Environment

Salin file konfigurasi bawaan `env` menjadi `.env`.

**Windows PowerShell:**

Copy-Item env .env


**Linux/macOS:**

cp env .env


Kemudian buka file `.env` dan sesuaikan konfigurasi berikut:

CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = usada_bali
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306


Sesuaikan konfigurasi database dengan lingkungan lokal masing-masing.

**Catatan:** File `.env` tidak disertakan dalam repository. Setiap developer perlu membuat file tersebut secara mandiri berdasarkan template `env`.

### 6. Jalankan Migration
Migration digunakan untuk membuat struktur tabel beserta relasi antar tabel pada database.

Jalankan perintah:
php spark migrate


Migration akan membuat tabel berikut:
- `wuku`
- `saptawara`
- `pancawara`
- `entri_usada`
- `pengobatan`

Tabel `migrations` juga akan dibuat secara otomatis oleh CodeIgniter untuk mencatat riwayat migration.


### 7. Jalankan Seeder
Seeder digunakan untuk memasukkan data awal ke dalam database secara otomatis.

Jalankan perintah:
php spark db:seed UsadaSeeder

Seeder akan membaca file CSV yang tersedia di:
app/
└── Database/
    └── Seeds/
        ├── UsadaSeeder.php
        └── data/
            ├── wuku_master_import.csv
            ├── hari_master_import.csv
            ├── pancawara_master_import.csv
            ├── entri_usada_import.csv
            └── pengobatan_import.csv


Data awal yang dimasukkan meliputi:

| Tabel | Jumlah Data |
|---|---:|
| `wuku` | 30 |
| `saptawara` | 7 |
| `pancawara` | 5 |
| `entri_usada` | 210 |
| `pengobatan` | 943 |
| **Total** | **1.195** |

**Catatan penting:**
- Pastikan seluruh file CSV tetap tersedia di folder `app/Database/Seeds/data/`.
- Seeder hanya dapat dijalankan pada tabel yang masih kosong.
- Jangan menjalankan Seeder kembali pada database yang sudah berisi data karena Seeder memiliki pemeriksaan untuk mencegah duplikasi.
- Data Glosarium belum termasuk dalam Seeder ini dan akan ditambahkan setelah penyusunan datanya selesai.


### 8. Jalankan Aplikasi
Setelah proses instalasi, migration, dan Seeder selesai, jalankan aplikasi dengan perintah:
php spark serve

### 9. Akses Aplikasi

Buka browser dan akses:
[http://localhost:8080](http://localhost:8080)

## Status Pengembangan

Dalam tahap pengembangan.