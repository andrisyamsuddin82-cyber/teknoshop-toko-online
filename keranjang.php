<?php
require __DIR__ . "/inc/bootstrap.php";
$item = kr_item();
$sub = array_sum(array_column($item, "subtotal"));
$kupon = null;
if (!empty($_SESSION["kupon"])) {
    [$kupon, $gal] = kupon_cek($_SESSION["kupon"], $sub);
    if (!$kupon) { unset($_SESSION["kupon"]); if ($gal) set_flash("Kupon dilepas: $gal", "warn"); }
}
$t = total_hitung($item, "hemat", $kupon);
tampil_awal("Keranjang");
?>
<h1>Keranjang belanja</h1>
<?php if (!$item): ?>
  <div class="kosong"><?= ikon("cart", 44) ?><h3>Keranjang masih kosong</h3><p>Yuk temukan produk favoritmu.</p><a class="tombol" href="<?= e(url("katalog.php")) ?>">Mulai belanja</a></div>
<?php else: ?>
<div class="dua-kolom">
  <section class="daftar-keranjang">
    <?php foreach ($item as $it): ?>
      <div class="baris-kr">
        <a href="<?= e(url("produk.php?slug=" . rawurlencode($it["slug"]))) ?>"><img src="<?= e(gambar_url($it["gambar"])) ?>" <?= gambar_fallback_attr($it["slug"]) ?>" alt="" width="84" height="84"></a>
        <div class="info">
          <a href="<?= e(url("produk.php?slug=" . rawurlencode($it["slug"]))) ?>"><b><?= e($it["nama"]) ?></b></a>
          <span class="redup"><?= rupiah($it["harga"]) ?> · stok <?= (int)$it["stok"] ?></span>
        </div>
        <form method="post" action="<?= e(url("keranjang_aksi.php")) ?>" class="ubah" data-autokirim>
          <?= csrf_field() ?><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="produk_id" value="<?= (int)$it["id"] ?>">
          <div class="stepper" data-stepper>
            <button type="button" data-step="-1" aria-label="Kurangi"><?= ikon("minus", 14) ?></button>
            <input type="number" name="jumlah" value="<?= (int)$it["jumlah"] ?>" min="1" max="<?= (int)$it["stok"] ?>" aria-label="Jumlah">
            <button type="button" data-step="1" aria-label="Tambah"><?= ikon("plus", 14) ?></button>
          </div>
          <noscript><button class="tombol kecil" type="submit">Ubah</button></noscript>
        </form>
        <strong class="sub"><?= rupiah($it["subtotal"]) ?></strong>
        <form method="post" action="<?= e(url("keranjang_aksi.php")) ?>">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="produk_id" value="<?= (int)$it["id"] ?>">
          <button class="ikon-tombol bahaya" type="submit" aria-label="Hapus <?= e($it["nama"]) ?>"><?= ikon("trash", 18) ?></button>
        </form>
      </div>
    <?php endforeach; ?>
  </section>
  <aside class="ringkasan">
    <h2>Ringkasan</h2>
    <?php if ($kupon): ?>
      <form method="post" action="<?= e(url("keranjang_aksi.php")) ?>" class="kupon-aktif"><?= csrf_field() ?><input type="hidden" name="aksi" value="kupon_hapus">
        <span><?= ikon("tag", 16) ?> <b><?= e($kupon["kode"]) ?></b></span><button class="tautan-kecil" type="submit">Lepas</button></form>
    <?php else: ?>
      <form method="post" action="<?= e(url("keranjang_aksi.php")) ?>" class="kupon-form"><?= csrf_field() ?><input type="hidden" name="aksi" value="kupon">
        <input type="text" name="kode" placeholder="Kode kupon" maxlength="30" aria-label="Kode kupon"><button class="tombol kecil" type="submit">Pakai</button></form>
    <?php endif; ?>
    <dl class="rincian">
      <dt>Subtotal</dt><dd><?= rupiah($t["subtotal"]) ?></dd>
      <?php if ($t["diskon"]): ?><dt>Diskon</dt><dd class="hemat">-<?= rupiah($t["diskon"]) ?></dd><?php endif; ?>
      <dt>Ongkir</dt><dd class="redup">dihitung di checkout</dd>
    </dl>
    <?php $kurang = GRATIS_ONGKIR_MIN - $sub; if ($kurang > 0): ?><p class="info-kecil">Belanja <?= rupiah($kurang) ?> lagi untuk gratis ongkir Reguler.</p><?php else: ?><p class="info-kecil ok">Selamat! Ongkir Reguler gratis.</p><?php endif; ?>
    <a class="tombol blok besar" href="<?= e(url("checkout.php")) ?>">Lanjut ke checkout</a>
    <a class="tautan-kecil tengah" href="<?= e(url("katalog.php")) ?>">Lanjut belanja</a>
  </aside>
</div>
<?php endif; ?>
<?php tampil_akhir();
