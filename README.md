# Sistem Pendukung Keputusan (SPK) TOPSIS untuk Pemilihan E-Wallet Terbaik

Aplikasi web untuk membantu pemilihan E-Wallet terbaik menggunakan metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution).

## 📋 Fitur Utama

### 1. Dashboard Interaktif
- Overview statistik lengkap
- Status kelengkapan data
- Top 3 E-Wallet terbaik
- Quick actions untuk akses cepat

### 2. Manajemen Kriteria
- CRUD (Create, Read, Update, Delete) kriteria
- Tipe kriteria: Benefit dan Cost
- Pengaturan bobot kriteria
- Pagination dan pencarian
- Validasi total bobot

### 3. Manajemen Alternatif
- CRUD alternatif E-Wallet
- Informasi lengkap setiap alternatif
- Pagination dan pencarian
- Deskripsi alternatif

### 4. Input Penilaian
- Matriks penilaian interaktif
- Input nilai untuk setiap alternatif-kriteria
- Panduan penilaian
- Referensi bobot kriteria
- Validasi data lengkap

### 5. Perhitungan TOPSIS
- Validasi kelengkapan data
- Proses perhitungan otomatis
- Tampilan langkah-langkah detail:
  - Matriks keputusan (X)
  - Matriks ternormalisasi (R)
  - Matriks ternormalisasi terbobot (Y)
  - Solusi ideal positif dan negatif (A+, A-)
  - Jarak dan nilai preferensi

### 6. Hasil & Ranking
- Podium top 3 alternatif
- Tabel ranking lengkap
- Analisis dan interpretasi
- Export ke CSV
- Fungsi cetak
- Visualisasi hasil

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: SQLite 3
- **Frontend**: HTML5, Tailwind CSS (CDN)
- **Icons**: Font Awesome 6
- **Arsitektur**: MVC Pattern

## 📦 Struktur Direktori

```
topsis-ewallet/
├── classes/
│   └── TOPSIS.php              # Class perhitungan TOPSIS
├── config/
│   └── database.php            # Konfigurasi database
├── database/
│   ├── init.sql                # Schema dan data awal
│   └── topsis.db               # File database SQLite (auto-generated)
├── includes/
│   ├── functions.php           # Helper functions
│   ├── header.php              # Layout header
│   └── footer.php              # Layout footer
├── index.php                   # Dashboard
├── criteria.php                # Manajemen kriteria
├── alternatives.php            # Manajemen alternatif
├── ratings.php                 # Input penilaian
├── calculate.php               # Perhitungan TOPSIS
├── results.php                 # Hasil & ranking
└── README.md                   # Dokumentasi
```

## 🚀 Instalasi

### Persyaratan Sistem

- PHP >= 7.4
- SQLite3 extension (biasanya sudah terinstall)
- Web server (Apache/Nginx) atau PHP built-in server

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   # Download project ke direktori web server
   cd /path/to/webserver/htdocs
   ```

2. **Ekstrak File**
   Ekstrak semua file ke folder `topsis-ewallet`

3. **Set Permissions**
   ```bash
   chmod -R 755 topsis-ewallet
   chmod -R 777 topsis-ewallet/database
   ```

4. **Jalankan Web Server**

   **Opsi 1: PHP Built-in Server (Development)**
   ```bash
   cd topsis-ewallet
   php -S localhost:8000
   ```
   Akses: http://localhost:8000

   **Opsi 2: Apache/Nginx (Production)**
   - Pastikan DocumentRoot mengarah ke folder `topsis-ewallet`
   - Akses: http://localhost/topsis-ewallet

5. **Inisialisasi Database**
   - Database akan otomatis dibuat saat pertama kali diakses
   - Data default (kriteria dan alternatif) sudah terisi

## 📊 Data Default

### Kriteria
1. **C1**: Kemudahan Penggunaan (Benefit, Weight: 0.2567)
2. **C2**: Keamanan Transaksi (Benefit, Weight: 0.1567)
3. **C3**: Kecepatan Proses Transaksi (Benefit, Weight: 0.0900)
4. **C4**: Kelengkapan Fitur (Benefit, Weight: 0.0400)
5. **C5**: Biaya Admin Transaksi (Cost, Weight: 0.4567)

### Alternatif E-Wallet
1. **A1**: Dana
2. **A2**: OVO
3. **A3**: GoPay
4. **A4**: ShopeePay
5. **A5**: LinkAja
6. **A6**: Flip
7. **A7**: Kantong Saya (My Pocket)
8. **A8**: Dokumen
9. **A9**: i.saku (i.pocket)

## 🎯 Cara Penggunaan

### 1. Akses Dashboard
- Buka aplikasi di browser
- Lihat overview dan status data

### 2. Kelola Kriteria
- Menu: **Kriteria**
- Tambah/Edit/Hapus kriteria
- Pastikan total bobot = 1
- Tentukan tipe (Benefit/Cost)

### 3. Kelola Alternatif
- Menu: **Alternatif**
- Tambah/Edit/Hapus E-Wallet
- Isi informasi lengkap

### 4. Input Penilaian
- Menu: **Penilaian**
- Isi matriks penilaian
- Masukkan nilai sesuai panduan
- Simpan semua data

### 5. Hitung TOPSIS
- Menu: **Perhitungan**
- Cek kelengkapan data
- Klik "Hitung Menggunakan Metode TOPSIS"
- Lihat proses perhitungan detail

### 6. Lihat Hasil
- Menu: **Hasil & Ranking**
- Lihat ranking final
- Analisis hasil
- Export/Print hasil

## 🔧 Kustomisasi

### Mengubah Kriteria
Edit di menu Kriteria atau langsung di database:
```sql
UPDATE criteria SET weight = 0.3 WHERE code = 'C1';
```

### Menambah Alternatif Baru
Bisa melalui UI atau SQL:
```sql
INSERT INTO alternatives (code, name, description)
VALUES ('A10', 'DANA Syariah', 'E-Wallet berbasis syariah');
```

### Mengubah Skala Penilaian
Edit panduan di `ratings.php` dan sesuaikan input form.

## 📝 Metode TOPSIS

### Langkah Perhitungan

1. **Matriks Keputusan (X)**
   - Tabel nilai awal semua alternatif dan kriteria

2. **Normalisasi Matriks (R)**
   - Formula: r_ij = x_ij / √(Σx_ij²)

3. **Matriks Ternormalisasi Terbobot (Y)**
   - Formula: y_ij = w_j × r_ij

4. **Solusi Ideal**
   - A+ (Positif): Max untuk benefit, Min untuk cost
   - A- (Negatif): Min untuk benefit, Max untuk cost

5. **Jarak Euclidean**
   - D+ = √Σ(y_ij - A+)²
   - D- = √Σ(y_ij - A-)²

6. **Nilai Preferensi**
   - V = D- / (D+ + D-)
   - Ranking: Nilai V tertinggi = terbaik

## 🐛 Troubleshooting

### Database tidak terbuat
```bash
# Buat folder database manual
mkdir database
chmod 777 database

# Atau jalankan init.sql manual
sqlite3 database/topsis.db < database/init.sql
```

### Error SQLite not found
```bash
# Install SQLite3 untuk PHP
# Ubuntu/Debian
sudo apt-get install php-sqlite3

# CentOS/RHEL
sudo yum install php-sqlite3

# Restart web server
sudo service apache2 restart
```

### Bobot tidak valid
Pastikan total bobot semua kriteria = 1.0000

### Data tidak lengkap
Pastikan sudah ada:
- Minimal 1 kriteria
- Minimal 1 alternatif
- Semua alternatif memiliki nilai untuk semua kriteria

## 📱 Responsive Design

Aplikasi sudah responsive dan dapat diakses dari:
- Desktop (1920px+)
- Laptop (1366px - 1920px)
- Tablet (768px - 1366px)
- Mobile (< 768px)

## 🔒 Keamanan

- Input sanitization
- SQL Injection protection (PDO prepared statements)
- XSS protection
- CSRF token (dapat ditambahkan)

## 📈 Pengembangan Lebih Lanjut

Fitur yang dapat ditambahkan:
1. User authentication & authorization
2. Multi-user support
3. History perhitungan
4. Visualisasi grafik (Chart.js)
5. Export PDF
6. Bobot dinamis (ROC method)
7. Comparison matrix
8. Sensitivity analysis
9. API endpoints
10. Report generator

## 📄 Lisensi

Project ini dibuat untuk keperluan edukasi dan penelitian.

## 👥 Kontributor

Rivan Permana

## 📧 Kontak

Untuk pertanyaan dan saran, silakan hubungi akun GitHub pengembang.

## 🙏 Acknowledgments

- Metode TOPSIS by Yoon & Hwang (1981)
- Tailwind CSS Framework
- Font Awesome Icons
- SQLite Database

---

**Versi**: 1.0.0
**Tanggal**: 2025
**Status**: Production Ready
