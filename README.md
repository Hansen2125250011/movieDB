# Flower Advisor - Movie App

Aplikasi Movie App yang dibangun dengan Laravel 11, menggunakan OMDb API untuk pencarian data film.

## Fitur

- **Pencarian Film:** Cari film berdasarkan Judul, Tahun, dan Tipe.
- **Detail Film:** Informasi lengkap mengenai film yang dipilih.
- **Sistem Favorit:** Simpan film favorit ke dalam database pengguna.
- **Autentikasi:** Fitur Registrasi dan Login pengguna.
- **Multi-bahasa:** Mendukung bahasa Inggris dan Indonesia.
- **Optimasi UI:** Menggunakan Infinite Scroll dan Lazy Loading untuk gambar.

## Prasyarat

Sebelum menginstal, pastikan lingkungan pengembangan Anda memenuhi syarat berikut:

- **PHP** >= 8.2
- **Composer**
- **Node.js & NPM**
- **MySQL** atau database engine pilihan Anda

## Langkah-langkah Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan project setelah melakukan `git pull` atau `git clone`:

### 1. Install Dependency

Instal semua package PHP dan JavaScript yang dibutuhkan:

```bash
composer install
npm install
```

### 2. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` dan atur konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flower_advisor
DB_USERNAME=root
DB_PASSWORD=
```

Tambahkan juga **OMDb API Key** Anda (Dapatkan di [omdbapi.com](https://www.omdbapi.com/)):

```env
OMDB_API_KEY=isi_api_key_di_sini
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Migrasi Database

Pastikan database sudah dibuat di MySQL, lalu jalankan perintah:

```bash
php artisan migrate
```

Jika ingin menyertakan data dummy (user tes):

```bash
php artisan db:seed
```

### 5. Kompilasi Aset Frontend

```bash
npm run build
```

Atau jika ingin menjalankan dalam mode development:

```bash
npm run dev
```

### 6. Jalankan Server Lokal

```bash
php artisan serve
```

Akses aplikasi melalui: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Akun Uji Coba (Jika sudah menjalankan seeder)

- **Username:** `aldmic`
- **Password:** `123abc123`
