<?php
require __DIR__ . "/inc/bootstrap.php";
$wish = sudah_login() ? id_wishlist(pengguna()["id"]) : [];
$unggulan = produk_daftar("p.unggulan = 1", [], "p.terjual DESC", 4);
$terbaru = produk_daftar("1 = 1", [], "p.id DESC", 10);
$kat = kategori_semua();
$kupon = semua("SELECT * FROM kupon WHERE aktif = 1 AND (berlaku_sampai IS NULL OR berlaku_sampai >= ?) ORDER BY id LIMIT 3", [date("Y-m-d")]);
tampil_awal("", TAGLINE, "beranda");
?>
<section class="hero">
  <div class="hero-teks">
    <span class="label">Belanja pintar untuk mahasiswa</span>
    <h1>Gadget terbaik untuk belajar dan berkarya</h1>
    <p>Headphone, keyboard, SSD, hingga power bank. Harga bersahabat, stok jelas, dan pengiriman cepat ke seluruh Indonesia.</p>
    <div class="hero-aksi">
      <a class="tombol besar" href="<?= e(url("katalog.php")) ?>">Mulai belanja</a>
      <a class="tombol besar sekunder" href="<?= e(url("katalog.php?urut=terlaris")) ?>">Lihat terlaris</a>
    </div>
  </div>
  <div class="hero-gambar" aria-hidden="true">
    <?php foreach (array_slice($unggulan, 0, 3) as $i => $p): ?>
      <img class="h<?= $i ?>" src="<?= e(gambar_url($p["gambar"])) ?>" <?= gambar_fallback_attr($p["slug"]) ?>" alt="" width="400" height="400">
    <?php endforeach; ?>
  </div>
</section>

<ul class="nilai">
  <li><?= ikon("truck", 26) ?><div><b>Pengiriman cepat</b><span>Kurir Hemat, Reguler, dan Ekspres</span></div></li>
  <li><?= ikon("shield", 26) ?><div><b>Transaksi aman</b><span>Stok dan harga selalu diperbarui</span></div></li>
  <li><?= ikon("tag", 26) ?><div><b>Banyak kupon</b><span>Hemat hingga 20% setiap belanja</span></div></li>
  <li><?= ikon("check", 26) ?><div><b>Ulasan asli</b><span>Hanya dari pembeli yang sudah menerima barang</span></div></li>
</ul>

<section>
  <div class="judul-bagian"><h2>Kategori</h2></div>
  <div class="grid-kat">
    <?php foreach ($kat as $k): ?>
      <a class="kartu-kat" href="<?= e(url("katalog.php?kategori=" . rawurlencode($k["slug"]))) ?>">
        <span class="bulat"><?= ikon($k["ikon"], 28) ?></span>
        <b><?= e($k["nama"]) ?></b>
        <small><?= (int)$k["jumlah"] ?> produk</small>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section>
  <div class="judul-bagian"><h2>Produk unggulan</h2><a href="<?= e(url("katalog.php?urut=terlaris")) ?>">Lihat semua →</a></div>
  <div class="grid-produk"><?php foreach ($unggulan as $p) echo kartu_produk($p, $wish); ?></div>
</section>

<?php if ($kupon): ?>
<section class="banner-kupon">
  <div><h2>Kupon belanja</h2><p>Masukkan kode di keranjang untuk potongan harga.</p></div>
  <ul>
    <?php foreach ($kupon as $k): ?>
      <li><b><?= e($k["kode"]) ?></b>
        <span><?= $k["jenis"] === "persen" ? (int)$k["nilai"] . "%" : e(rupiah($k["nilai"])) ?> · min. <?= e(rupiah($k["min_belanja"])) ?></span></li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<section>
  <div class="judul-bagian"><h2>Produk terbaru</h2><a href="<?= e(url("katalog.php")) ?>">Lihat semua →</a></div>
  <div class="grid-produk"><?php foreach ($terbaru as $p) echo kartu_produk($p, $wish); ?></div>
</section>
<?php tampil_akhir();
