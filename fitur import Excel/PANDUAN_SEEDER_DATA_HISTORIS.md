# 📋 Panduan Seeder Data Historis Kegiatan Lapangan PLN Galesong

## 📌 Informasi Seeder

Seeder ini akan membuat data historis kegiatan lapangan untuk 3 kelompok dari periode **Januari 2024 sampai 26 November 2025**.

### Kelompok yang Akan Dibuat:
1. **Kelompok 1: Hairil & Fajar** (Shift 1)
2. **Kelompok 2: Budi & Andi** (Shift 1)
3. **Kelompok 3: Agus & Ani** (Shift 2)

## 🚀 Cara Menjalankan Seeder

### Langkah 1: Pastikan Kelompok Sudah Ada
Jalankan seeder kelompok terlebih dahulu:
```bash
php artisan db:seed --class=PlnGalesongSeeder
php artisan db:seed --class=PerbaikanMeteranKelompok1ManualSeeder
```

### Langkah 2: Jalankan Seeder Data Historis
```bash
php artisan db:seed --class=LaporanKaryawanHistorisSeeder
```

### Atau jalankan semua seeder sekaligus:
Pastikan `DatabaseSeeder.php` sudah dikonfigurasi untuk memanggil semua seeder.

## 📊 Format Data yang Dihasilkan

Setiap baris laporan memiliki format berikut:

| Kolom | Keterangan |
|-------|------------|
| Hari/Tanggal | Hari dalam bahasa Indonesia dan tanggal |
| Kelompok | Nama kelompok |
| Nama | Nama karyawan (anggota kelompok) |
| Instansi | Instansi PLN |
| Alamat Tujuan | Alamat lokasi pekerjaan |
| Perbaikan KWH | Detail perbaikan KWH meter (jika ada) |
| Pemeliharaan Pengkabelan | Detail pemeliharaan pengkabelan (jika ada) |
| Pengecekan Gardu | Detail pengecekan gardu (jika ada) |
| Penanganan Gangguan | Detail penanganan gangguan (jika ada) |
| Lokasi | Lokasi pekerjaan (A-J) |
| Waktu (jam) | Waktu penyelesaian (2-6 jam) |
| Dokumentasi | Nama file dokumentasi foto |

## ⚙️ Karakteristik Data

### Aturan Generate Data:
1. **Satu baris = Satu kegiatan** (hanya 1 jenis kegiatan per baris)
2. **Satu hari bisa ada 1-3 kegiatan** (multiple rows dengan tanggal sama)
3. **Hari Minggu tidak ada kegiatan** (weekend)
4. **Waktu penyelesaian**: 2-6 jam per kegiatan
5. **Lokasi**: Rotasi konsisten (Lokasi A sampai J)
6. **Dokumentasi**: Format foto1.jpg, foto2.jpg, dst (konsisten)

### Jenis Kegiatan:
- Perbaikan KWH Meter
- Pemeliharaan Pengkabelan
- Pengecekan Gardu
- Penanganan Gangguan

### Data Realistis:
- 10 instansi PLN berbeda
- 15 alamat tujuan berbeda
- 10 lokasi pekerjaan (Lokasi A-J)
- Variasi detail kegiatan yang realistis

## 📈 Perkiraan Jumlah Data

- **Periode**: 694 hari (Jan 2024 - 26 Nov 2025)
- **Hari kerja**: ~595 hari (exclude Minggu)
- **Kegiatan per hari**: 1-3 kegiatan
- **Total perkiraan**: ~1,200 - 5,000 laporan per kelompok
- **Total keseluruhan**: ~3,600 - 15,000 laporan

## ⚠️ Catatan Penting

1. **Waktu proses**: Seeder ini akan memakan waktu beberapa menit karena jumlah data yang besar
2. **Progress bar**: Akan muncul progress bar saat proses berjalan
3. **Idempotent**: Jika kelompok sudah ada, tidak akan dibuat duplikat
4. **Data konsisten**: Lokasi dan dokumentasi menggunakan pola konsisten

## 🔍 Melihat Data yang Terbuat

Setelah seeder selesai, Anda dapat melihat data di:
- Halaman **Pemantauan Laporan** (untuk admin/atasan)
- Halaman **Input Laporan** (untuk karyawan)
- Atau query langsung ke database:
```php
use App\Models\LaporanKaryawan;
$laporans = LaporanKaryawan::with('kelompok')->get();
```

## 🛠️ Troubleshooting

### Error: Kelompok tidak ditemukan
**Solusi**: Jalankan `PlnGalesongSeeder` terlebih dahulu

### Error: Memory limit
**Solusi**: Tingkatkan memory limit di `php.ini` atau jalankan dengan:
```bash
php -d memory_limit=512M artisan db:seed --class=LaporanKaryawanHistorisSeeder
```

### Proses terlalu lambat
**Solusi**: Normal untuk jumlah data besar. Biarkan proses selesai.

## 📝 Catatan

- Data yang dihasilkan adalah **data dummy/test** untuk keperluan pengembangan
- Data realistis namun bukan data nyata
- Periode data: 1 Januari 2024 - 26 November 2025
- Total ~694 hari dengan estimasi ribuan laporan





