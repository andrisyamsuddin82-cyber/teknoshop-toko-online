<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
$daftar = semua(SELECT_PRODUK . " JOIN wishlist w ON w.produk_id = p.id WHERE w.pengguna_id = ? AND p.aktif = 1 ORDER BY w.dibuat DESC", [$u["id"]]);
$wish = id_wishlist((int)$u["id"]);
tampil_awal("Wishlist");
?>
<h1>Wishlist</h1>
<?php if (!$daftar): ?>
  <div class="kosong"><?= ikon("heart", 44) ?><h3>Wishlist masih kosong</h3><p>Tekan ikon hati pada produk untuk menyimpannya di sini.</p><a class="tombol" href="<?= e(url("katalog.php")) ?>">Jelajahi produk</a></div>
<?php else: ?>
  <div class="grid-produk"><?php foreach ($daftar as $p) echo kartu_produk($p, $wish); ?></div>
<?php endif; ?>
<?php tampil_akhir();
