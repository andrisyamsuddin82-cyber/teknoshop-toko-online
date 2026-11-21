# Teknoshop – Website Toko Online Dinamis (PHP Native + MySQL)

Proyek mata kuliah Pemrograman Web. Toko online lengkap dengan sisi pelanggan dan panel admin,
tanpa framework dan tanpa pustaka luar (HTML, CSS, JavaScript, dan PHP murni dengan PDO).

## Cara menjalankan (XAMPP)
1. Salin folder `toko` ke `htdocs`, misalnya `C:\xampp\htdocs\teknoshop`.
2. Aktifkan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka phpMyAdmin → menu **Import** → pilih `database.sql` → Go.
   (Database `toko_online` dibuat otomatis. File ini menghapus dan membuat ulang tabelnya.)
4. Buka `http://localhost/teknoshop/` di browser.

Jika user/sandi MySQL Anda berbeda, ubah `inc/config.php`. Jika alamat situs tidak terbaca otomatis,
isi `BASE_URL_MANUAL` (contoh `"/teknoshop"`). Kebutuhan: PHP 8.0+ dengan ekstensi `pdo_mysql` dan `fileinfo`.

## Akun contoh
| Peran | Email | Kata sandi |
|---|---|---|
| Admin | admin@teknoshop.test | admin123 |
| Pelanggan | budi@contoh.test | pelanggan123 |

Pelanggan lain: siti@, rizky@, dewi@contoh.test (sandi sama). Kupon contoh: `HEMAT10`, `MAHASISWA`, `DISKON25K`.
Setelah dipakai sungguhan, set `MODE_DEMO` ke `false` di `inc/config.php` dan ganti sandi admin.

## Fitur
**Pelanggan**
- Beranda (hero, kategori, produk unggulan dan terbaru, kupon), katalog dengan pencarian, filter kategori dan harga, pengurutan, dan paginasi
- Detail produk: stok, diskon, rating, ulasan pembeli terverifikasi, produk terkait, wishlist
- Keranjang (sesi) dengan batas stok dan kupon; checkout dengan pilihan kurir dan metode bayar, total langsung berubah
- Pesanan saya: linimasa status, instruksi pembayaran, konfirmasi bayar, pembatalan (stok dikembalikan), konfirmasi terima, invoice cetak
- Daftar, masuk, keluar, profil, ganti kata sandi

**Admin** (`/admin/`)
- Dasbor: pendapatan, jumlah pesanan, pelanggan, grafik penjualan 14 hari, produk terlaris, stok menipis
- CRUD produk dengan unggah gambar; CRUD kategori dan kupon; daftar pelanggan
- Pesanan: filter dan cari, alur status (verifikasi → proses → kirim dengan resi → selesai), batalkan, ekspor CSV

## Aturan bisnis
- Gratis ongkir kurir Reguler jika subtotal ≥ Rp500.000.
- Stok dikurangi atomik saat pesanan dibuat (tidak bisa oversell); pembatalan mengembalikan stok dan kuota kupon.
- COD langsung berstatus *Diproses*; transfer dan e-wallet mulai dari *Menunggu Pembayaran*.
- Produk yang sudah pernah dipesan tidak dihapus, hanya dinonaktifkan agar riwayat pesanan tetap utuh.
- Ulasan hanya bagi pelanggan yang pesanannya sudah *Selesai*, satu ulasan per produk.
- Nomor rekening dan e-wallet di `config.php` hanya contoh fiktif.

## Keamanan
Prepared statement (PDO), `password_hash`, `session_regenerate_id`, token CSRF di setiap form POST,
escape keluaran (`e()`), validasi unggahan (tipe MIME, ukuran, nama acak, folder `uploads` tanpa eksekusi PHP),
cek peran admin di setiap halaman admin, pengalihan hanya ke alamat internal, pembatasan percobaan login,
dan penjaga injeksi rumus pada ekspor CSV.

## Struktur
```
index.php katalog.php produk.php        halaman toko
keranjang*.php checkout.php             keranjang dan checkout
pesanan*.php invoice.php                pesanan pelanggan
login.php daftar.php logout.php akun.php wishlist*.php ulasan_aksi.php
admin/                                   panel admin
inc/                                     config, db, auth, keranjang, pesanan, layout, komponen
assets/ css js img/produk                aset (foto produk lokal di assets/img/produk)
uploads/produk/                          gambar unggahan admin
database.sql                             skema dan data contoh
```


## Foto produk
Semua 16 gambar produk contoh tersedia secara lokal di `assets/img/produk/`.
Gambar tidak membutuhkan koneksi internet. Untuk database yang sudah terpasang,
impor `UPDATE_GAMBAR_PRODUK.sql` melalui phpMyAdmin setelah memilih database
`toko_online`. Gambar merupakan foto katalog ilustratif yang dibuat khusus
berdasarkan jenis barang; nama/merek pada katalog adalah nama contoh Teknoshop.
Foto yang diunggah admin tetap dapat disimpan secara lokal di `uploads/produk/`.
