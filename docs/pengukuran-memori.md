# Hasil Pengukuran Penggunaan Memori

## 1. Tujuan

Pengukuran ini dilakukan untuk membandingkan penggunaan memori antara metode `get()` dan `lazyById()` pada Laravel Eloquent ketika membaca data transaksi dalam jumlah besar.

## 2. Lingkungan Pengujian

- Framework: Laravel
- PHP: 8.5.9
- Jumlah data transaksi: 52.500 baris
- Batas memori PHP: 128 MB

## 3. Hasil Pengukuran

| Metode | Jumlah Data | Peak Memory | Status |
|---|---:|---:|---|
| `lazyById()` | 52.500 baris | 70,9 MB | Berhasil |
| `get()` | 52.500 baris | Tidak berhasil diukur | Gagal karena batas memori |

Pada pengujian `get()`, PHP menghasilkan pesan `Allowed memory size of 134217728 bytes exhausted`. Hal ini menunjukkan bahwa proses membaca seluruh data sekaligus kehabisan memori karena melewati batas 128 MB yang tersedia. Oleh karena itu, nilai peak memory untuk metode `get()` tidak berhasil dicatat.

## 4. Analisis dan Kesimpulan

Berdasarkan hasil pengujian, metode `lazyById()` berhasil membaca 52.500 transaksi dengan peak memory sebesar 70,9 MB. Sementara itu, metode `get()` mengalami kegagalan karena batas memori PHP terlampaui.

Metode `lazyById()` lebih sesuai untuk membaca data dalam jumlah besar pada lingkungan pengujian ini karena data diproses secara bertahap. Sebaliknya, `get()` mengambil seluruh data sekaligus sehingga berisiko membutuhkan memori yang lebih besar.

**Kesimpulan kelompok:**

Berdasarkan hasil pengujian, kelompok menyimpulkan bahwa metode `lazyById()` lebih sesuai untuk pemrosesan data transaksi dalam jumlah besar pada lingkungan pengujian ini. Metode tersebut berhasil membaca 52.500 baris dengan penggunaan peak memory sebesar 70,9 MB, sedangkan `get()` gagal karena melewati batas memori PHP sebesar 128 MB.