# 🚀 Quick Start Guide - SPK TOPSIS E-Wallet

## Cara Cepat Memulai (5 Menit)

### Langkah 1: Buka Project
```bash
cd topsis-ewallet
```

### Langkah 2: Jalankan Server
```bash
php -S localhost:8000
```

### Langkah 3: Akses di Browser
```
http://localhost:8000
```

### Langkah 4: Mulai Menggunakan
1. Lihat Dashboard
2. Cek data default (Kriteria & Alternatif sudah ada)
3. Masuk ke menu "Penilaian" untuk input nilai
4. Klik "Perhitungan" untuk hitung TOPSIS
5. Lihat "Hasil & Ranking" untuk melihat E-Wallet terbaik

## 📊 Data Sudah Tersedia

### ✅ Kriteria (5 kriteria)
- Kemudahan Penggunaan
- Keamanan Transaksi
- Kecepatan Proses Transaksi
- Kelengkapan Fitur
- Biaya Admin Transaksi

### ✅ Alternatif (9 E-Wallet)
- Dana, OVO, GoPay, ShopeePay, LinkAja, Flip, My Pocket, Dokumen, i.saku

### ✅ Penilaian Default
Semua nilai penilaian sudah diisi berdasarkan artikel penelitian!

## ⚡ Langsung Hitung!

Karena data sudah lengkap, Anda bisa langsung:

1. Buka menu **Perhitungan**
2. Klik tombol **"Hitung Menggunakan Metode TOPSIS"**
3. Lihat proses perhitungan step-by-step
4. Buka **Hasil & Ranking** untuk melihat E-Wallet terbaik

## 🎯 Hasil yang Diharapkan

**E-Wallet Terbaik (Berdasarkan Artikel):**
- **LinkAja** dengan nilai preferensi: **0.7518**

## 🔧 Modifikasi Data

### Ubah Penilaian
Menu: **Penilaian** → Edit nilai → Simpan

### Tambah E-Wallet Baru
Menu: **Alternatif** → Tambah Alternatif → Isi form → Simpan

### Ubah Bobot Kriteria
Menu: **Kriteria** → Edit → Ubah bobot → Simpan

### Hitung Ulang
Menu: **Perhitungan** → Klik tombol hitung

## 📱 Fitur Tambahan

- **Pencarian**: Cari data di setiap halaman
- **Pagination**: Navigasi data yang banyak
- **Export CSV**: Export hasil ke file CSV
- **Print**: Cetak hasil perhitungan
- **Responsive**: Akses dari mobile/tablet

## ❓ Troubleshooting Cepat

**Port 8000 sudah digunakan?**
```bash
php -S localhost:8080
# Akses di http://localhost:8080
```

**Database error?**
```bash
chmod -R 777 database/
```

**Tampilan tidak muncul?**
Cek koneksi internet (Tailwind CSS pakai CDN)

## 📚 Dokumentasi Lengkap

Lihat file **README.md** untuk dokumentasi detail.

## 💡 Tips

1. Data default sudah lengkap, langsung coba hitung saja!
2. Ekspor hasil ke CSV untuk dokumentasi
3. Ubah-ubah nilai untuk eksperimen
4. Total bobot kriteria harus selalu = 1.0

---

**Selamat Mencoba! 🎉**
