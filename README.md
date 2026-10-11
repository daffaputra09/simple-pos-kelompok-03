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