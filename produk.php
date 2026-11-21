<?php
require __DIR__ . "/inc/bootstrap.php";
$p = produk_slug(get_teks("slug"));
if (!$p) {
    http_response_code(404);
    tampil_awal("Produk tidak ditemukan");
    echo '<div class="kosong"><h1>Produk tidak ditemukan</h1><p>Produk mungkin sudah tidak dijual.</p><a class="tombol" href="' . e(url("katalog.php")) . '">Kembali ke katalog</a></div>';
    tampil_akhir();
    exit;
}
$u = pengguna();
$ulasan = ulasan_produk((int)$p["id"]);
$terkait = produk_terkait($p);
$wishP = $u ? di_wishlist((int)$u["id"], (int)$p["id"]) : false;
$wish = $u ? id_wishlist((int)$u["id"]) : [];
$sudahUlas = $u ? (bool)nilai("SELECT 1 FROM ulasan WHERE produk_id = ? AND pengguna_id = ?", [$p["id"], $u["id"]]) : false;
$bolehUlas = $u && !$sudahUlas && boleh_ulas((int)$u["id"], (int)$p["id"]);
$diskon = !empty($p["harga_coret"]) && $p["harga_coret"] > $p["harga"] ? (int)round(100 - $p["harga"] * 100 / $p["harga_coret"]) : 0;
$stok = (int)$p["stok"];
tampil_awal($p["nama"], potong(strtok($p["deskripsi"], "\n"), 150), "kat-" . $p["kategori_slug"]);
?>
<nav class="remah" aria-label="Jejak"><a href="<?= e(url("index.php")) ?>">Beranda</a> › <a href="<?= e(url("katalog.php?kategori=" . rawurlencode($p["kategori_slug"]))) ?>"><?= e($p["kategori_nama"]) ?></a> › <span><?= e($p["nama"]) ?></span></nav>
<article class="detail">
  <div class="detail-gambar"><img src="<?= e(gambar_url($p["gambar"])) ?>" <?= gambar_fallback_attr($p["slug"]) ?>" alt="<?= e($p["nama"]) ?>" width="600" height="600"><?php if ($diskon): ?><span class="pita">-<?= $diskon ?>%</span><?php endif; ?></div>
  <div class="detail-info">
    <span class="kat"><?= e($p["kategori_nama"]) ?></span>
    <h1><?= e($p["nama"]) ?></h1>
    <div class="rating"><?= bintang((float)$p["rating"]) ?> <small><?= (int)$p["jumlah_ulasan"] ? number_format((float)$p["rating"], 1, ",", ".") . " · " . (int)$p["jumlah_ulasan"] . " ulasan" : "Belum ada ulasan" ?></small> · <small><?= (int)$p["terjual"] ?> terjual</small></div>
    <div class="harga besar"><strong><?= rupiah($p["harga"]) ?></strong><?php if ($diskon): ?> <s><?= rupiah($p["harga_coret"]) ?></s><?php endif; ?></div>
    <p class="stok <?= $stok < 1 ? "habis" : ($stok <= 5 ? "tipis" : "") ?>"><?= $stok < 1 ? "Stok habis" : ($stok <= 5 ? "Tersisa $stok unit" : "Stok tersedia ($stok)") ?></p>
    <div class="deskripsi">
      <?php
      $baris = preg_split('/\R/', $p["deskripsi"]);
      $list = [];
      foreach ($baris as $b) {
          if (strpos($b, "- ") === 0) { $list[] = substr($b, 2); continue; }
          if ($list) { echo "<ul>" . implode("", array_map(fn($x) => "<li>" . e($x) . "</li>", $list)) . "</ul>"; $list = []; }
          if (trim($b) !== "") echo "<p>" . e($b) . "</p>";
      }
      if ($list) echo "<ul>" . implode("", array_map(fn($x) => "<li>" . e($x) . "</li>", $list)) . "</ul>";
      ?>
    </div>
    <?php if ($stok > 0): ?>
    <form method="post" action="<?= e(url("keranjang_aksi.php")) ?>" class="beli">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="tambah">
      <input type="hidden" name="produk_id" value="<?= (int)$p["id"] ?>">
      <div class="stepper" data-stepper>
        <button type="button" data-step="-1" aria-label="Kurangi"><?= ikon("minus", 16) ?></button>
        <input type="number" name="jumlah" value="1" min="1" max="<?= $stok ?>" aria-label="Jumlah">
        <button type="button" data-step="1" aria-label="Tambah"><?= ikon("plus", 16) ?></button>
      </div>
      <button class="tombol besar" type="submit"><?= ikon("cart", 18) ?> Tambah ke keranjang</button>
      <button class="tombol besar sekunder" type="submit" name="langsung" value="1">Beli sekarang</button>
    </form>
    <?php endif; ?>
    <?php if ($u): ?>
    <form method="post" action="<?= e(url("wishlist_aksi.php")) ?>" class="wish-detail">
      <?= csrf_field() ?><input type="hidden" name="produk_id" value="<?= (int)$p["id"] ?>">
      <input type="hidden" name="kembali" value="<?= e($_SERVER["REQUEST_URI"] ?? "/") ?>">
      <button class="tautan-kecil" type="submit"><?= ikon("heart", 16) ?> <?= $wishP ? "Hapus dari wishlist" : "Simpan ke wishlist" ?></button>
    </form>
    <?php endif; ?>
    <ul class="jaminan"><li><?= ikon("truck", 18) ?> Gratis ongkir Reguler di atas <?= e(rupiah(GRATIS_ONGKIR_MIN)) ?></li><li><?= ikon("shield", 18) ?> Garansi toko 7 hari</li></ul>
  </div>
</article>

<section class="ulasan" id="ulasan">
  <div class="judul-bagian"><h2>Ulasan pembeli</h2></div>
  <?php if ($bolehUlas): ?>
    <form method="post" action="<?= e(url("ulasan_aksi.php")) ?>" class="form kartu-form">
      <?= csrf_field() ?><input type="hidden" name="produk_id" value="<?= (int)$p["id"] ?>">
      <h3>Tulis ulasan</h3>
      <label>Rating
        <select name="rating"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> bintang</option><?php endfor; ?></select></label>
      <label>Komentar<textarea name="komentar" rows="3" maxlength="500" placeholder="Ceritakan pengalaman Anda"></textarea></label>
      <button class="tombol" type="submit">Kirim ulasan</button>
    </form>
  <?php elseif ($u && !$sudahUlas): ?>
    <p class="redup">Ulasan dapat ditulis setelah pesanan produk ini berstatus selesai.</p>
  <?php endif; ?>
  <?php if (!$ulasan): ?><p class="redup">Belum ada ulasan untuk produk ini.</p><?php endif; ?>
  <?php foreach ($ulasan as $r): ?>
    <div class="ulasan-item">
      <div class="avatar"><?= e(mb_strtoupper(mb_substr($r["nama"], 0, 1))) ?></div>
      <div><b><?= e($r["nama"]) ?></b> <span class="redup">· <?= e(tgl($r["dibuat"])) ?></span> <span class="label ok">Pembelian terverifikasi</span>
        <div><?= bintang((float)$r["rating"]) ?></div>
        <?php if ($r["komentar"]): ?><p><?= nl2br(e($r["komentar"])) ?></p><?php endif; ?></div>
    </div>
  <?php endforeach; ?>
</section>

<?php if ($terkait): ?>
<section><div class="judul-bagian"><h2>Produk terkait</h2></div>
  <div class="grid-produk"><?php foreach ($terkait as $t) echo kartu_produk($t, $wish); ?></div></section>
<?php endif; ?>
<?php tampil_akhir();
