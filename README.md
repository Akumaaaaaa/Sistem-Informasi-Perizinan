# Sistem Informasi Surat Izin (SiPerizin)

Aplikasi manajemen surat izin karyawan berbasis web untuk PT PLN Indonesia Power Semarang PGU. Memungkinkan karyawan mengajukan izin meninggalkan kantor, atasan menyetujui/menolak pengajuan, dan superadmin mengelola data pengguna.

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net/)
![Copyright: Akmal](https://img.shields.io/badge/Copyright-Akmal-green.svg)

---

## Fitur

| Peran | Fitur |
|---|---|
| **Superadmin** | Kelola akun pengguna (tambah, edit, hapus), kelola data karyawan |
| **Atasan** | Lihat pengajuan izin pending bawahan, setujui atau tolak |
| **Karyawan** | Ajukan surat izin, lihat riwayat pengajuan, cetak surat (PDF) |

---

## Teknologi

- **Backend:** PHP 8.x, MySQLi
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5.2, DataTables 1.13, Boxicons 2.0
- **PDF:** FPDF 1.85

---

## Persyaratan

- PHP >= 8.0 (fungsi `str_starts_with` dibutuhkan)
- MySQL / MariaDB
- Web server (Apache / Nginx)
- FPDF 1.85 — ekstrak `karyawan/fpdf185.rar` ke folder `karyawan/fpdf185/`

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/Akumaaaaaa/Sistem-Informasi-Perizinan.git
```

### 2. Ekstrak library PDF

Ekstrak file `karyawan/fpdf185.rar` sehingga menghasilkan folder `karyawan/fpdf185/`.

### 3. Buat database dan import schema

```sql
CREATE DATABASE suratizin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
mysql -u root -p suratizin < suratizin.sql
```

> **Catatan:** Nama database di `suratizin.sql` adalah `suratizin`, bukan `db_siperizin` seperti di README lama.

### 4. Konfigurasi koneksi database

Edit file `db_login.php`:

```php
$dbhost = "localhost";
$dbuser = "root";      // sesuaikan
$dbpwd  = "";          // sesuaikan
$dbname = "suratizin";
```

### 5. Jalankan aplikasi

Tempatkan folder proyek di root web server (misal `htdocs/SiPerizin`) lalu buka:

```
http://localhost/SiPerizin/
```

---

## Akun Demo

| Email | Password | Peran |
|---|---|---|
| superadmin@mail.com | 1234 | Superadmin |
| koro@mail.com | 1234 | Atasan |
| atasan@mail.com | 1234 | Atasan |
| karyawan@mail.com | 1234 | Karyawan |

> Password demo disimpan sebagai plain text di `suratizin.sql`. Saat login pertama kali, sistem otomatis mengenkripsi password menggunakan bcrypt dan menyimpannya kembali ke database — tidak perlu langkah migrasi manual.

---

## Struktur Direktori

```
Sistem-Informasi-Perizinan/
├── asset/               # Favicon dan logo PLN
├── atasan/              # Modul dashboard atasan
│   ├── atasan.php       # Dashboard & persetujuan izin
│   ├── bawahan.php      # Daftar karyawan bawahan
│   └── edit_profil.php  # Edit profil atasan
├── karyawan/            # Modul dashboard karyawan
│   ├── karyawan.php     # Dashboard & riwayat izin
│   ├── formizin.php     # Form pengajuan izin
│   ├── printable.php    # Generate PDF surat izin
│   └── edit_profil.php  # Edit profil karyawan
├── superadmin/          # Modul dashboard superadmin
│   ├── superadmin.php   # Manajemen user
│   ├── datakaryawan.php # Manajemen karyawan
│   ├── add_user.php     # Form tambah user
│   └── add_karyawan.php # Form tambah karyawan
├── db_login.php         # Konfigurasi koneksi database
├── index.php            # Halaman login
├── logout.php           # Proses logout
└── suratizin.sql        # Schema database
```

---

## Skema Database

| Tabel | Deskripsi |
|---|---|
| `tb_user` | Akun login (email, password bcrypt, peran) |
| `tb_karyawan` | Data karyawan (NIP, nama, jabatan, bidang, NIP atasan) |
| `tb_jabatan` | Master jabatan |
| `tb_bidang` | Master bidang/departemen |
| `tb_jenis_izin` | Jenis izin: Pribadi / Dinas |
| `tb_surat_izin` | Pengajuan izin (pk_id auto-increment, NIP atasan, status verifikasi) |

---

## Alur Penggunaan

```
1. Karyawan login → klik "Form Izin" → isi formulir → submit
2. Atasan login → lihat pengajuan pending → klik Approve / Refuse
3. Karyawan login → lihat status terbaru di dashboard → cetak PDF jika disetujui
```

---

## Keamanan

- Password dienkripsi dengan **bcrypt** (`password_hash` PHP, auto-migrasi saat login pertama)
- Login menggunakan **prepared statement** (mencegah SQL injection)
- Operasi database lain menggunakan `real_escape_string` dan `intval`
- Session di-regenerate setelah login sukses (mencegah session fixation)
- Output database di-escape dengan `htmlspecialchars` (mencegah XSS)
- `exit()` dipanggil setelah setiap `header()` redirect

---

## Lisensi

Proyek akademik — PT PLN Indonesia Power Semarang PGU © 2023
