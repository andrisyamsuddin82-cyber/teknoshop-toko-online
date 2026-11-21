-- ============================================================
--  Teknoshop - database toko online
--  Import file ini lewat phpMyAdmin (menu Import) atau:
--    mysql -u root < database.sql
--  PERHATIAN: file ini menghapus lalu membuat ulang semua tabel
--  di database `toko_online`, jadi data lama di database itu hilang.
--
--  Akun contoh
--    Admin     admin@teknoshop.test   / admin123
--    Pelanggan budi@contoh.test       / pelanggan123
-- ============================================================

CREATE DATABASE IF NOT EXISTS toko_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE toko_online;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS ulasan;
DROP TABLE IF EXISTS riwayat_status;
DROP TABLE IF EXISTS pesanan_item;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS kupon;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS pengguna;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE pengguna (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telepon VARCHAR(20) NULL,
  alamat TEXT NULL,
  password VARCHAR(255) NOT NULL,
  peran VARCHAR(20) NOT NULL DEFAULT 'pelanggan',
  dibuat DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE kategori (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama VARCHAR(80) NOT NULL,
  slug VARCHAR(100) NOT NULL,
  ikon VARCHAR(30) NOT NULL,
  deskripsi VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produk (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kategori_id INT UNSIGNED NOT NULL,
  nama VARCHAR(150) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  deskripsi TEXT NOT NULL,
  harga INT UNSIGNED NOT NULL,
  harga_coret INT UNSIGNED NULL,
  stok INT UNSIGNED NOT NULL,
  gambar VARCHAR(255) NULL,
  unggulan TINYINT UNSIGNED NOT NULL DEFAULT 0,
  aktif TINYINT UNSIGNED NOT NULL DEFAULT 1,
  terjual INT UNSIGNED NOT NULL DEFAULT 0,
  dibuat DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (slug),
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE kupon (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(30) NOT NULL,
  jenis VARCHAR(10) NOT NULL,
  nilai INT UNSIGNED NOT NULL,
  min_belanja INT UNSIGNED NOT NULL DEFAULT 0,
  maks_diskon INT UNSIGNED NULL,
  kuota INT UNSIGNED NULL,
  terpakai INT UNSIGNED NOT NULL DEFAULT 0,
  berlaku_sampai DATE NULL,
  aktif TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pesanan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(30) NOT NULL,
  pengguna_id INT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL,
  nama_penerima VARCHAR(100) NOT NULL,
  telepon VARCHAR(20) NOT NULL,
  alamat TEXT NOT NULL,
  kurir VARCHAR(20) NOT NULL,
  ongkir INT UNSIGNED NOT NULL DEFAULT 0,
  metode_bayar VARCHAR(20) NOT NULL,
  subtotal INT UNSIGNED NOT NULL,
  diskon INT UNSIGNED NOT NULL DEFAULT 0,
  total INT UNSIGNED NOT NULL,
  kupon_kode VARCHAR(30) NULL,
  catatan VARCHAR(255) NULL,
  resi VARCHAR(50) NULL,
  dibuat DATETIME NOT NULL,
  diperbarui DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (kode),
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pesanan_item (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id INT UNSIGNED NOT NULL,
  produk_id INT UNSIGNED NOT NULL,
  nama VARCHAR(150) NOT NULL,
  harga INT UNSIGNED NOT NULL,
  jumlah INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE riwayat_status (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id INT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL,
  catatan VARCHAR(255) NULL,
  dibuat DATETIME NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ulasan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  produk_id INT UNSIGNED NOT NULL,
  pengguna_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL DEFAULT 0,
  komentar TEXT NULL,
  dibuat DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (produk_id, pengguna_id),
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE,
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlist (
  pengguna_id INT UNSIGNED NOT NULL,
  produk_id INT UNSIGNED NOT NULL,
  dibuat DATETIME NOT NULL,
  PRIMARY KEY (pengguna_id, produk_id),
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE CASCADE,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kategori (id, nama, slug, ikon, deskripsi) VALUES
(1, 'Audio', 'audio', 'headset', 'Headphone, speaker, dan earbuds untuk belajar dan hiburan'),
(2, 'Periferal', 'periferal', 'keyboard', 'Keyboard, mouse, webcam, dan perlengkapan meja kerja'),
(3, 'Penyimpanan', 'penyimpanan', 'drive', 'Flashdisk, SSD portabel, hub, dan kabel'),
(4, 'Aksesori', 'aksesori', 'briefcase', 'Tas, stand laptop, power bank, lampu, dan charger');
INSERT INTO produk (id, kategori_id, nama, slug, deskripsi, harga, harga_coret, stok, gambar, unggulan, aktif, terjual, dibuat) VALUES
(1, 1, 'Nova Studio Headphone X2', 'nova-studio-headphone-x2', 'Headphone over-ear dengan driver 40 mm yang menghasilkan bass padat dan vokal jernih untuk belajar, bekerja, maupun hiburan.
- Koneksi Bluetooth 5.3 dan kabel 3,5 mm
- Baterai hingga 40 jam pemakaian
- Bantalan busa memori yang nyaman dipakai lama
- Mikrofon bawaan untuk kuliah daring', 549000, 699000, 25, 'assets/img/produk/urban-laptop-backpack-15.jpg', 1, 1, 142, '2026-10-08 09:00:00'),
(2, 1, 'Vexa PocketBeat Speaker', 'vexa-pocketbeat-speaker', 'Speaker Bluetooth portabel dengan suara 360 derajat dan bodi tahan percikan air.
- Daya 10 W dengan bass pasif
- Tahan percikan air standar IPX5
- Baterai 12 jam dan pengisian USB-C
- Bisa dipasangkan dua unit untuk suara stereo', 289000, NULL, 40, 'assets/img/produk/charger-gan-65w.jpg', 0, 1, 88, '2026-10-09 09:00:00'),
(3, 1, 'Kiro Air Buds Pro', 'kiro-air-buds-pro', 'Earbuds nirkabel dengan peredam bising aktif dan casing pengisi daya yang ringkas.
- Peredam bising aktif dan mode transparansi
- Total baterai 30 jam bersama casing
- Koneksi cepat Bluetooth 5.3
- Tahan keringat untuk olahraga', 399000, 499000, 60, 'assets/img/produk/nova-studio-headphone-x2.jpg', 1, 1, 205, '2026-10-10 09:00:00'),
(4, 2, 'Aurora Mechanical Keyboard K87', 'aurora-mechanical-keyboard-k87', 'Keyboard mekanik 87 tombol dengan switch taktil, backlight, dan rangka kokoh untuk mengetik dan ngoding berjam-jam.
- Hot-swap switch, mudah diganti tanpa solder
- Backlight putih dengan 8 efek
- Koneksi kabel USB-C dan Bluetooth
- Keycap PBT yang tidak mudah mengkilap', 649000, NULL, 18, 'assets/img/produk/rockssd-portable-512gb.jpg', 1, 1, 64, '2026-10-11 09:00:00'),
(5, 2, 'Glide Wireless Mouse M3', 'glide-wireless-mouse-m3', 'Mouse nirkabel senyap dengan sensor presisi dan bentuk ergonomis untuk tangan kanan.
- Sensor 1600 DPI dengan 3 level kecepatan
- Klik senyap, nyaman dipakai di perpustakaan
- Baterai AA tahan hingga 12 bulan
- Receiver USB mini', 159000, 199000, 75, 'assets/img/produk/clearcam-webcam-hd-1080.jpg', 0, 1, 310, '2026-10-12 09:00:00'),
(6, 2, 'ClearCam Webcam HD 1080', 'clearcam-webcam-hd-1080', 'Webcam Full HD dengan fokus otomatis dan dua mikrofon peredam bising, cocok untuk kuliah daring dan presentasi.
- Resolusi 1080p 30 fps
- Fokus otomatis dan koreksi cahaya redup
- Penutup lensa untuk privasi
- Plug and play, tanpa instalasi driver', 329000, NULL, 30, 'assets/img/produk/kiro-air-buds-pro.jpg', 0, 1, 97, '2026-10-13 09:00:00'),
(7, 2, 'Terra Mousepad XL', 'terra-mousepad-xl', 'Mousepad ekstra besar 80 x 30 cm dengan permukaan halus dan alas karet anti selip.
- Ukuran 800 x 300 x 3 mm
- Tepi dijahit agar tidak mudah terkelupas
- Muat keyboard dan mouse sekaligus
- Mudah dibersihkan', 89000, NULL, 120, 'assets/img/produk/kabelku-usb-c-braided-1m.jpg', 0, 1, 420, '2026-10-14 09:00:00'),
(8, 3, 'SwiftDrive Flashdisk 64GB', 'swiftdrive-flashdisk-64gb', 'Flashdisk USB 3.2 berkapasitas 64 GB dengan kecepatan baca tinggi untuk memindahkan tugas dan materi kuliah.
- Kapasitas 64 GB
- Kecepatan baca hingga 150 MB/s
- Bodi logam ringkas dengan lubang gantungan
- Garansi 2 tahun', 79000, NULL, 200, 'assets/img/produk/swiftdrive-flashdisk-64gb.jpg', 0, 1, 515, '2026-10-15 09:00:00'),
(9, 3, 'RockSSD Portable 512GB', 'rockssd-portable-512gb', 'SSD eksternal kecil dan kencang untuk cadangan data, proyek video, dan penyimpanan tambahan laptop.
- Kapasitas 512 GB
- Kecepatan baca hingga 1.000 MB/s lewat USB-C
- Tahan guncangan hingga 1,5 meter
- Dilengkapi kabel USB-C ke USB-C dan USB-C ke USB-A', 899000, 1049000, 22, 'assets/img/produk/glide-wireless-mouse-m3.jpg', 1, 1, 71, '2026-10-16 09:00:00'),
(10, 3, 'HubPlus USB-C 7in1', 'hubplus-usb-c-7in1', 'Hub USB-C 7 port untuk menambah HDMI, USB, dan pembaca kartu pada laptop tipis.
- HDMI 4K 30 Hz
- 2 port USB 3.0 dan 1 USB 2.0
- Pembaca kartu SD dan microSD
- Port pengisian daya USB-C hingga 100 W', 249000, NULL, 45, 'assets/img/produk/aurora-mechanical-keyboard-k87.jpg', 0, 1, 133, '2026-10-17 09:00:00'),
(11, 3, 'KabelKu USB-C Braided 1m', 'kabelku-usb-c-braided-1m', 'Kabel USB-C dengan lapisan nilon anyaman yang kuat dan tahan kusut.
- Panjang 1 meter
- Mendukung pengisian cepat hingga 60 W
- Transfer data 480 Mbps
- Konektor logam yang kokoh', 49000, NULL, 300, 'assets/img/produk/terra-mousepad-xl.jpg', 0, 1, 760, '2026-10-18 09:00:00'),
(12, 4, 'Urban Laptop Backpack 15"', 'urban-laptop-backpack-15', 'Tas punggung dengan kompartemen laptop berlapis busa, tahan percikan air, dan banyak saku untuk perlengkapan kuliah.
- Muat laptop hingga 15,6 inci
- Bahan tahan percikan air
- Port USB eksternal untuk power bank
- Tali bahu empuk dan sandaran punggung bernapas', 359000, 429000, 35, 'assets/img/produk/volt-powerbank-20000mah.jpg', 0, 1, 119, '2026-10-19 09:00:00'),
(13, 4, 'Elevate Laptop Stand Aluminium', 'elevate-laptop-stand-aluminium', 'Stand laptop aluminium yang bisa dilipat dan diatur ketinggiannya agar posisi leher lebih nyaman.
- Tinggi dapat diatur 6 tingkat
- Muat laptop 11 sampai 17 inci
- Lipat ringkas, bobot hanya 280 g
- Celah udara untuk sirkulasi panas', 219000, NULL, 28, 'assets/img/produk/hubplus-usb-c-7in1.jpg', 0, 1, 84, '2026-10-20 09:00:00'),
(14, 4, 'Volt PowerBank 20.000mAh', 'volt-powerbank-20000mah', 'Power bank berkapasitas besar dengan pengisian cepat dua arah untuk ponsel dan tablet.
- Kapasitas 20.000 mAh
- Output USB-C PD 22,5 W
- 2 port USB-A dan 1 port USB-C
- Indikator daya digital', 379000, NULL, 50, 'assets/img/produk/vexa-pocketbeat-speaker.jpg', 1, 1, 233, '2026-10-21 09:00:00'),
(15, 4, 'Lumi LED Desk Lamp', 'lumi-led-desk-lamp', 'Lampu meja LED dengan tiga suhu warna dan kecerahan yang bisa diatur, lembut di mata saat belajar malam.
- 3 mode warna dan 5 level kecerahan
- Lengan fleksibel dan kepala lampu dapat diputar
- Lampu hemat daya 8 W
- Pengisian ponsel nirkabel di dasar lampu', 189000, NULL, 33, 'assets/img/produk/lumi-led-desk-lamp.jpg', 0, 1, 58, '2026-10-22 09:00:00'),
(16, 4, 'Charger GaN 65W', 'charger-gan-65w', 'Charger GaN kecil bertenaga 65 W yang bisa mengisi laptop, tablet, dan ponsel sekaligus.
- 2 port USB-C dan 1 port USB-A
- Output hingga 65 W
- Ukuran 40% lebih kecil dari charger biasa
- Proteksi suhu, arus, dan tegangan', 299000, 349000, 55, 'assets/img/produk/elevate-laptop-stand-aluminium.jpg', 0, 1, 176, '2026-10-23 09:00:00');
INSERT INTO pengguna (id, nama, email, telepon, alamat, password, peran, dibuat) VALUES
(1, 'Administrator', 'admin@teknoshop.test', '081200000001', 'Jl. Contoh Raya No. 1, Makassar, Sulawesi Selatan 90245', '$2y$10$8.kKRlnxrdWAGrt80srDm.0UG4cd3L0xvWbXoyP3rl6vtj6TX8w8y', 'admin', '2026-10-02 08:00:00'),
(2, 'Budi Santoso', 'budi@contoh.test', '081234567801', 'Jl. Pengayoman No. 21, Panakkukang, Makassar, Sulawesi Selatan 90231', '$2y$10$BqKeYCgAlvi62TytsCsPq.V89lPy7A5Nh8C8n/5dea66kD9CowqbC', 'pelanggan', '2026-10-02 08:00:00'),
(3, 'Siti Rahma', 'siti@contoh.test', '081234567802', 'Jl. Perintis Kemerdekaan Km. 10 No. 5, Tamalanrea, Makassar 90245', '$2y$10$InWK9i7WN.RKrjwqMM2ny.p.CTzZmxnEKjErEXoMutHLdNqZcKmTO', 'pelanggan', '2026-10-02 08:00:00'),
(4, 'Rizky Ramadhan', 'rizky@contoh.test', '081234567803', 'Jl. Sultan Alauddin No. 88, Rappocini, Makassar 90222', '$2y$10$G5QCvQq9jMrptZ2KVq3oDe3I5Tdr0VUMHEimQxOhtcqL.wTiLrGGm', 'pelanggan', '2026-10-02 08:00:00'),
(5, 'Dewi Kartika', 'dewi@contoh.test', '081234567804', 'Jl. Veteran Selatan No. 14, Mamajang, Makassar 90134', '$2y$10$FjxgptHpfZ/0YtkoU5nsheTZ8/dmZZqF1VhH9cD0bdDyLEeDMMV5m', 'pelanggan', '2026-10-02 08:00:00');
INSERT INTO kupon (id, kode, jenis, nilai, min_belanja, maks_diskon, kuota, terpakai, berlaku_sampai, aktif) VALUES
(1, 'HEMAT10', 'persen', 10, 200000, 50000, 100, 1, '2027-12-31', 1),
(2, 'MAHASISWA', 'persen', 15, 300000, 100000, 50, 1, '2027-12-31', 1),
(3, 'DISKON25K', 'nominal', 25000, 250000, NULL, NULL, 0, NULL, 1),
(4, 'GAJIAN', 'persen', 20, 500000, 150000, 20, 0, '2026-11-30', 1);
INSERT INTO pesanan (id, kode, pengguna_id, status, nama_penerima, telepon, alamat, kurir, ongkir, metode_bayar, subtotal, diskon, total, kupon_kode, catatan, resi, dibuat, diperbarui) VALUES
(1, 'INV-20261103-00001', 2, 'selesai', 'Budi Santoso', '081234567801', 'Jl. Pengayoman No. 21, Panakkukang, Makassar, Sulawesi Selatan 90231', 'reguler', 0, 'transfer', 707000, 0, 707000, NULL, NULL, 'TKS1007919', '2026-11-03 10:12:00', '2026-11-07 10:52:00'),
(2, 'INV-20261104-00002', 3, 'selesai', 'Siti Rahma', '081234567802', 'Jl. Perintis Kemerdekaan Km. 10 No. 5, Tamalanrea, Makassar 90245', 'ekspres', 30000, 'ewallet', 399000, 0, 429000, NULL, NULL, 'TKS1015838', '2026-11-04 14:40:00', '2026-11-08 15:20:00'),
(3, 'INV-20261105-00003', 4, 'selesai', 'Rizky Ramadhan', '081234567803', 'Jl. Sultan Alauddin No. 88, Rappocini, Makassar 90222', 'reguler', 0, 'transfer', 997000, 50000, 947000, 'HEMAT10', NULL, 'TKS1023757', '2026-11-05 09:05:00', '2026-11-09 09:45:00'),
(4, 'INV-20261107-00004', 2, 'selesai', 'Budi Santoso', '081234567801', 'Jl. Pengayoman No. 21, Panakkukang, Makassar, Sulawesi Selatan 90231', 'hemat', 9000, 'cod', 248000, 0, 257000, NULL, NULL, 'TKS1031676', '2026-11-07 19:30:00', '2026-11-11 20:10:00'),
(5, 'INV-20261108-00005', 5, 'selesai', 'Dewi Kartika', '081234567804', 'Jl. Veteran Selatan No. 14, Mamajang, Makassar 90134', 'reguler', 15000, 'transfer', 359000, 0, 374000, NULL, NULL, 'TKS1039595', '2026-11-08 11:22:00', '2026-11-12 12:02:00'),
(6, 'INV-20261109-00006', 3, 'selesai', 'Siti Rahma', '081234567802', 'Jl. Perintis Kemerdekaan Km. 10 No. 5, Tamalanrea, Makassar 90245', 'reguler', 0, 'transfer', 808000, 100000, 708000, 'MAHASISWA', NULL, 'TKS1047514', '2026-11-09 16:48:00', '2026-11-13 17:28:00'),
(7, 'INV-20261110-00007', 4, 'dikirim', 'Rizky Ramadhan', '081234567803', 'Jl. Sultan Alauddin No. 88, Rappocini, Makassar 90222', 'ekspres', 30000, 'ewallet', 678000, 0, 708000, NULL, NULL, 'TKS1055433', '2026-11-10 08:15:00', '2026-11-11 09:15:00'),
(8, 'INV-20261111-00008', 2, 'dikirim', 'Budi Santoso', '081234567801', 'Jl. Pengayoman No. 21, Panakkukang, Makassar, Sulawesi Selatan 90231', 'reguler', 15000, 'transfer', 468000, 0, 483000, NULL, NULL, 'TKS1063352', '2026-11-11 13:37:00', '2026-11-12 14:37:00'),
(9, 'INV-20261112-00009', 5, 'diproses', 'Dewi Kartika', '081234567804', 'Jl. Veteran Selatan No. 14, Mamajang, Makassar 90134', 'reguler', 15000, 'transfer', 478000, 0, 493000, NULL, NULL, NULL, '2026-11-12 20:02:00', '2026-11-13 01:02:00'),
(10, 'INV-20261113-00010', 3, 'diproses', 'Siti Rahma', '081234567802', 'Jl. Perintis Kemerdekaan Km. 10 No. 5, Tamalanrea, Makassar 90245', 'hemat', 9000, 'cod', 329000, 0, 338000, NULL, NULL, NULL, '2026-11-13 10:50:00', '2026-11-13 10:50:00'),
(11, 'INV-20261114-00011', 2, 'menunggu_verifikasi', 'Budi Santoso', '081234567801', 'Jl. Pengayoman No. 21, Panakkukang, Makassar, Sulawesi Selatan 90231', 'reguler', 0, 'transfer', 948000, 0, 948000, NULL, NULL, NULL, '2026-11-14 15:25:00', '2026-11-14 16:55:00'),
(12, 'INV-20261115-00012', 4, 'menunggu_pembayaran', 'Rizky Ramadhan', '081234567803', 'Jl. Sultan Alauddin No. 88, Rappocini, Makassar 90222', 'reguler', 15000, 'ewallet', 286000, 0, 301000, NULL, NULL, NULL, '2026-11-15 12:10:00', '2026-11-15 12:10:00'),
(13, 'INV-20261115-00013', 5, 'dibatalkan', 'Dewi Kartika', '081234567804', 'Jl. Veteran Selatan No. 14, Mamajang, Makassar 90134', 'reguler', 0, 'transfer', 899000, 0, 899000, NULL, NULL, NULL, '2026-11-15 17:45:00', '2026-11-16 17:05:00'),
(14, 'INV-20261116-00014', 3, 'menunggu_pembayaran', 'Siti Rahma', '081234567802', 'Jl. Perintis Kemerdekaan Km. 10 No. 5, Tamalanrea, Makassar 90245', 'ekspres', 30000, 'transfer', 299000, 0, 329000, NULL, NULL, NULL, '2026-11-16 08:30:00', '2026-11-16 08:30:00');
INSERT INTO pesanan_item (id, pesanan_id, produk_id, nama, harga, jumlah) VALUES
(1, 1, 1, 'Nova Studio Headphone X2', 549000, 1),
(2, 1, 8, 'SwiftDrive Flashdisk 64GB', 79000, 2),
(3, 2, 3, 'Kiro Air Buds Pro', 399000, 1),
(4, 3, 9, 'RockSSD Portable 512GB', 899000, 1),
(5, 3, 11, 'KabelKu USB-C Braided 1m', 49000, 2),
(6, 4, 5, 'Glide Wireless Mouse M3', 159000, 1),
(7, 4, 7, 'Terra Mousepad XL', 89000, 1),
(8, 5, 12, 'Urban Laptop Backpack 15"', 359000, 1),
(9, 6, 4, 'Aurora Mechanical Keyboard K87', 649000, 1),
(10, 6, 5, 'Glide Wireless Mouse M3', 159000, 1),
(11, 7, 14, 'Volt PowerBank 20.000mAh', 379000, 1),
(12, 7, 16, 'Charger GaN 65W', 299000, 1),
(13, 8, 10, 'HubPlus USB-C 7in1', 249000, 1),
(14, 8, 13, 'Elevate Laptop Stand Aluminium', 219000, 1),
(15, 9, 2, 'Vexa PocketBeat Speaker', 289000, 1),
(16, 9, 15, 'Lumi LED Desk Lamp', 189000, 1),
(17, 10, 6, 'ClearCam Webcam HD 1080', 329000, 1),
(18, 11, 1, 'Nova Studio Headphone X2', 549000, 1),
(19, 11, 3, 'Kiro Air Buds Pro', 399000, 1),
(20, 12, 8, 'SwiftDrive Flashdisk 64GB', 79000, 3),
(21, 12, 11, 'KabelKu USB-C Braided 1m', 49000, 1),
(22, 13, 9, 'RockSSD Portable 512GB', 899000, 1),
(23, 14, 16, 'Charger GaN 65W', 299000, 1);
INSERT INTO riwayat_status (id, pesanan_id, status, catatan, dibuat) VALUES
(1, 1, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-03 10:12:00'),
(2, 1, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-03 11:12:00'),
(3, 1, 'diproses', 'Pembayaran terverifikasi', '2026-11-03 15:12:00'),
(4, 1, 'dikirim', 'Pesanan dikirim', '2026-11-04 11:12:00'),
(5, 1, 'selesai', 'Pesanan diterima pelanggan', '2026-11-07 10:52:00'),
(6, 2, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-04 14:40:00'),
(7, 2, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-04 15:40:00'),
(8, 2, 'diproses', 'Pembayaran terverifikasi', '2026-11-04 19:40:00'),
(9, 2, 'dikirim', 'Pesanan dikirim', '2026-11-05 15:40:00'),
(10, 2, 'selesai', 'Pesanan diterima pelanggan', '2026-11-08 15:20:00'),
(11, 3, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-05 09:05:00'),
(12, 3, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-05 10:05:00'),
(13, 3, 'diproses', 'Pembayaran terverifikasi', '2026-11-05 14:05:00'),
(14, 3, 'dikirim', 'Pesanan dikirim', '2026-11-06 10:05:00'),
(15, 3, 'selesai', 'Pesanan diterima pelanggan', '2026-11-09 09:45:00'),
(16, 4, 'diproses', 'Pesanan dibuat (bayar di tempat)', '2026-11-07 19:30:00'),
(17, 4, 'dikirim', 'Pesanan dikirim', '2026-11-08 20:30:00'),
(18, 4, 'selesai', 'Pesanan diterima pelanggan', '2026-11-11 20:10:00'),
(19, 5, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-08 11:22:00'),
(20, 5, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-08 12:22:00'),
(21, 5, 'diproses', 'Pembayaran terverifikasi', '2026-11-08 16:22:00'),
(22, 5, 'dikirim', 'Pesanan dikirim', '2026-11-09 12:22:00'),
(23, 5, 'selesai', 'Pesanan diterima pelanggan', '2026-11-12 12:02:00'),
(24, 6, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-09 16:48:00'),
(25, 6, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-09 17:48:00'),
(26, 6, 'diproses', 'Pembayaran terverifikasi', '2026-11-09 21:48:00'),
(27, 6, 'dikirim', 'Pesanan dikirim', '2026-11-10 17:48:00'),
(28, 6, 'selesai', 'Pesanan diterima pelanggan', '2026-11-13 17:28:00'),
(29, 7, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-10 08:15:00'),
(30, 7, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-10 09:15:00'),
(31, 7, 'diproses', 'Pembayaran terverifikasi', '2026-11-10 13:15:00'),
(32, 7, 'dikirim', 'Pesanan dikirim', '2026-11-11 09:15:00'),
(33, 8, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-11 13:37:00'),
(34, 8, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-11 14:37:00'),
(35, 8, 'diproses', 'Pembayaran terverifikasi', '2026-11-11 18:37:00'),
(36, 8, 'dikirim', 'Pesanan dikirim', '2026-11-12 14:37:00'),
(37, 9, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-12 20:02:00'),
(38, 9, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-12 21:02:00'),
(39, 9, 'diproses', 'Pembayaran terverifikasi', '2026-11-13 01:02:00'),
(40, 10, 'diproses', 'Pesanan dibuat (bayar di tempat)', '2026-11-13 10:50:00'),
(41, 11, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-14 15:25:00'),
(42, 11, 'menunggu_verifikasi', 'Pelanggan mengonfirmasi pembayaran', '2026-11-14 16:55:00'),
(43, 12, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-15 12:10:00'),
(44, 13, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-15 17:45:00'),
(45, 13, 'dibatalkan', 'Pesanan dibatalkan', '2026-11-16 17:05:00'),
(46, 14, 'menunggu_pembayaran', 'Pesanan dibuat', '2026-11-16 08:30:00');
INSERT INTO ulasan (id, produk_id, pengguna_id, rating, komentar, dibuat) VALUES
(1, 1, 2, 5, 'Suaranya jernih dan bass-nya pas. Nyaman dipakai kuliah daring seharian.', '2026-11-07 20:15:00'),
(2, 8, 2, 4, 'Kencang untuk memindahkan tugas, bodinya kokoh. Sudah dipakai dua minggu aman.', '2026-11-07 20:15:00'),
(3, 3, 3, 5, 'Peredam bisingnya terasa banget di perpustakaan. Baterai awet.', '2026-11-08 20:15:00'),
(4, 9, 4, 5, 'Cepat sekali, render video jadi lebih lancar. Ukurannya juga kecil.', '2026-11-09 20:15:00'),
(5, 11, 4, 4, 'Kabelnya tebal dan terasa awet, pengisian cepat berjalan baik.', '2026-11-09 20:15:00'),
(6, 5, 2, 4, 'Klik-nya senyap, cocok dipakai di kelas. Pengiriman cepat.', '2026-11-10 20:15:00'),
(7, 7, 2, 5, 'Luas dan halus, mouse jadi lebih presisi. Harganya masuk akal.', '2026-11-10 20:15:00'),
(8, 12, 5, 4, 'Muat laptop 14 inci dan banyak saku. Jahitannya rapi.', '2026-11-11 20:15:00'),
(9, 4, 3, 5, 'Ketikannya enak, switch-nya taktil dan mantap untuk ngoding.', '2026-11-12 20:15:00'),
(10, 5, 3, 3, 'Bagus, tetapi receiver-nya agak mudah lepas dari port.', '2026-11-12 20:15:00');
INSERT INTO wishlist (pengguna_id, produk_id, dibuat) VALUES
(2, 4, '2026-11-13 21:00:00'),
(2, 9, '2026-11-13 21:00:00'),
(3, 1, '2026-11-13 21:00:00'),
(3, 14, '2026-11-13 21:00:00');