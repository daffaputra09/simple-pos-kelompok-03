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