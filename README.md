# Presensi Siswa

Aplikasi UKK presensi siswa dengan PHP native, PDO, OOP, dan MariaDB.

## Struktur proyek

```text
Presensi RFID/
├── app/
│   ├── Core/                 # Koneksi database
│   │   └── Database.php
│   └── Models/               # Model OOP untuk tabel
│       ├── BaseModel.php
│       ├── Kelas.php
│       ├── Presensi.php
│       ├── Siswa.php
│       └── User.php
├── assets/
│   └── css/style.css         # Gaya untuk halaman HTML dan PHP
├── config/
│   └── database.php          # Pengaturan koneksi MariaDB
├── database/
│   ├── schema.sql            # Database dan struktur tabel
│   └── seed.sql              # Data awal kelas
├── includes/                 # Layout bersama dan pemeriksaan login
│   ├── auth_check.php
│   ├── footer.php
│   └── header.php
├── pages/
│   ├── kelas/index.php
│   ├── presensi/index.php
│   ├── siswa/index.php
│   ├── dashboard.php
│   ├── login.php
│   └── logout.php
├── index.php                 # Pintu masuk aplikasi
└── index.html                # Purwarupa tampilan awal
```

## Menjalankan di Laragon

1. Jalankan Apache dan MariaDB.
2. Import `database/schema.sql` melalui phpMyAdmin atau HeidiSQL.
3. Jalankan `database/seed.sql` untuk menambahkan kelas awal.
4. Sesuaikan host, nama database, username, dan password di `config/database.php`.
5. Buka `http://localhost/Presensi%20RFID/`.

File dalam `pages/` masih berupa kerangka. Implementasikan fitur satu per satu
dan gunakan prepared statement untuk semua input pengguna. Password administrator
harus dibuat dengan `password_hash()` dan diperiksa menggunakan `password_verify()`.
