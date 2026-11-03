# Simple POS — Sistem Informasi Kasir

## Anggota Kelompok 03 — SIB 3C

| Nama Mahasiswa | NIM |
| :--- | :--- |
| Daffa Putra Prasetya | 244107060088 |
| Radith Ferdian Hibawan | 244107060091 |
| Nazwa Azahra Audina | 244107060146 |
| Hernanda Rizka Utami | 244107060075 |

---

## Analisis Keamanan Transaksi

### Pertanyaan

Apakah validasi `numeric` pada field `total` sudah cukup untuk mencegah manipulasi total transaksi? Jelaskan alasannya.

### Pembahasan

Validasi `numeric` saja belum cukup untuk mencegah manipulasi total transaksi. Aturan ini hanya memastikan bahwa nilai yang diterima berupa angka, bukan memastikan bahwa nilainya benar sesuai dengan harga produk dan jumlah barang yang dibeli.

Sebagai contoh, pengguna dapat mengubah nilai `total` melalui permintaan HTTP menggunakan cURL atau Postman. Jika sistem hanya memeriksa apakah nilainya berupa angka, nilai yang sudah diubah tersebut tetap dapat lolos validasi.

Pada aplikasi Simple POS yang diperiksa, perhitungan total dilakukan kembali di sisi server. Sistem mengambil harga produk dari database, kemudian menghitung subtotal berdasarkan harga produk dan jumlah barang (`qty`). Seluruh subtotal selanjutnya dijumlahkan untuk mendapatkan total transaksi.

Dengan cara ini, sistem tidak bergantung pada nilai total yang dikirim oleh pengguna. Perhitungan menggunakan harga produk yang tersimpan di database sehingga risiko manipulasi nilai transaksi dapat dikurangi.

### Kesimpulan

Validasi `numeric` tidak cukup untuk memastikan bahwa total transaksi benar. Oleh karena itu, total perlu dihitung di sisi server berdasarkan harga produk dalam database dan jumlah barang yang dibeli. Mekanisme ini membantu menjaga keakuratan nilai transaksi serta mencegah pengguna menentukan total secara sepihak.

## Skenario Uji Manual (Autentikasi, Otorisasi, dan RBAC)

Berikut adalah skenario pengujian manual untuk memastikan proses autentikasi dan pembatasan hak akses berjalan sesuai dengan peran pengguna pada aplikasi Simple POS.

### 1. Peran: Admin

* **Langkah:**
  1. Login menggunakan akun `admin@pos.test` dengan password `password`.
  2. Buka URL `/products`.
  3. Buka URL `/transactions`.

* **Hasil yang Diharapkan:**
  * Admin berhasil login ke aplikasi.
  * Halaman `/products` dapat diakses dengan normal.
  * Halaman `/transactions` dapat diakses dengan normal.

### 2. Peran: Kasir

* **Langkah:**
  1. Login menggunakan akun `kasir@pos.test` dengan password `password`.
  2. Buka URL `/pos`.
  3. Buka URL `/products`.

* **Hasil yang Diharapkan:**
  * Kasir berhasil login ke aplikasi.
  * Halaman `/pos` dapat diakses sesuai hak akses kasir.
  * Halaman `/products` menampilkan respons **403 Forbidden** karena kasir tidak memiliki izin untuk mengakses halaman tersebut.

### 3. Peran: Manager

* **Langkah:**
  1. Login menggunakan akun manager yang telah disiapkan.
  2. Buka URL `/transactions`.
  3. Buka URL `/products`.

* **Hasil yang Diharapkan:**
  * Manager berhasil login ke aplikasi.
  * Halaman `/transactions` dapat diakses dengan normal.
  * Halaman `/products` menampilkan respons **403 Forbidden** karena manager tidak memiliki izin untuk mengakses halaman tersebut.

### 4. Peran: Tamu (Belum Login)

* **Langkah:**
  1. Logout dari aplikasi.
  2. Buka halaman informasi yang hanya dapat diakses oleh tamu.
  3. Login menggunakan salah satu akun yang tersedia.
  4. Coba akses kembali halaman informasi tersebut.

* **Hasil yang Diharapkan:**
  * Pengguna yang belum login dapat membuka halaman informasi khusus tamu.
  * Pengguna yang sudah login diarahkan ke `/pos` ketika mencoba membuka halaman khusus tamu.

### Kesimpulan Pengujian

Skenario pengujian ini digunakan untuk memastikan bahwa setiap peran memiliki hak akses sesuai ketentuan. Admin dapat mengakses halaman produk dan transaksi, sedangkan kasir dan manager dibatasi berdasarkan hak aksesnya. Selain itu, halaman khusus tamu hanya dapat diakses oleh pengguna yang belum login.

## Pengolahan Data

### Pertanyaan
Untuk laporan 100.000 transaksi, format mana yang kalian pilih (CSV, Excel, atau PDF), dan kenapa?

### Pembahasan
CSV. Alasannya, ekspor CSV menggunakan metode *streaming* yang membaca dan mengalirkan data baris demi baris, sehingga penggunaan memori server tetap rendah dan stabil meskipun memproses 100.000 transaksi. Sebaliknya, format PDF memuat seluruh dokumen ke dalam memori sekaligus, sehingga sangat tidak cocok dan dapat membuat sistem kelebihan beban jika dipaksa merender puluhan ribu baris data.

## Identifikasi Proses untuk Queued Job

### Pertanyaan
Identifikasi satu proses lain di Simple POS yang menurutmu layak dipindah ke queued job, dan jelaskan alasannya.

### Pembahasan
Proses yang paling layak dipindahkan ke *queued job* adalah **Pengiriman Struk Transaksi Digital via Email** (misalnya class job `SendTransactionReceiptJob`).

Pada alur kasir POS (`TransactionController::store`), setelah data transaksi penjualan dan rincian item (`TransactionDetail`) berhasil divalidasi serta disimpan ke dalam database, proses pengiriman struk belanja digital ke alamat email pelanggan sebaiknya tidak dijalankan secara langsung (sinkron), melainkan dikirimkan ke dalam antrean antarmuka (*queued job*).

### Alasan dan Manfaat:

1. **Mengurangi Latensi Kasir (Non-blocking I/O):**
   Proses komunikasi ke server email atau penyedia SMTP pihak ketiga (seperti Mailgun, SES, atau SMTP server) membutuhkan jabat tangan jaringan (*network round-trip*) yang memakan waktu antara 1 hingga 3 detik. Jika dijalankan secara sinkron, halaman kasir akan mengalami *loading/freeze* sebelum bisa melayani pelanggan berikutnya. Dengan memindahkannya ke antrean *job*, respons transaksi kasir kembali instan (sub-detik) sehingga antrean pelanggan di kasir tidak tersendat.

2. **Ketahanan Sistem dan Percobaan Ulang Otomatis (Fault Tolerance & Retry):**
   Koneksi internet atau server penyedia email rentan mengalami gangguan sewaktu-waktu (*network timeout*). Jika proses pengiriman email gagal di tengah jalan, transaksi utama penjualan di database tetap tersimpan dengan aman dan tidak boleh dibatalkan (*rollback*). Job pengiriman email dapat dikonfigurasi dengan batas percobaan ulang (misalnya `$tries = 3`) dan jeda waktu (`$backoff = [10, 60]`) agar *worker* mencoba mengirim kembali secara otomatis di latar belakang tanpa mengganggu alur aplikasi kasir.

3. **Pemisahan Tanggung Jawab Aplikasi (Decoupling):**
   Tugas inti transaksi kasir (menyimpan riwayat transaksi dan memotong stok barang) harus bersifat transaksional dan konsisten (*ACID*). Sedangkan pengiriman struk digital atau notifikasi merupakan proses sekunder (*side-effect*). Memisahkan keduanya membuat logika *controller* tetap bersih, terisolasi, dan mudah dipelihara.

### Kesimpulan
Memindahkan proses pengiriman struk email ke *queued job* meningkatkan performa waktu respons aplikasi kasir secara signifikan, menjaga kelancaran antrean kasir, serta memberikan ketahanan terhadap potensi kegagalan jaringan pihak ketiga melalui mekanisme antrean otomatis.