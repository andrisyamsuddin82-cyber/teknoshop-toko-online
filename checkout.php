<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
$item = kr_item();
if (!$item) { set_flash("Keranjang masih kosong.", "warn"); redirect("keranjang.php"); }

$in = [
    "nama" => $u["nama"], "telepon" => (string)$u["telepon"], "alamat" => (string)$u["alamat"],
    "kurir" => "reguler", "metode" => "transfer", "kupon" => $_SESSION["kupon"] ?? "", "catatan" => "",
];
$galat = [];
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    cek_csrf();
    $in = [
        "nama" => post_teks("nama", 100), "telepon" => post_teks("telepon", 20), "alamat" => post_teks("alamat", 500),
        "kurir" => post_teks("kurir", 20), "metode" => post_teks("metode", 20), "kupon" => strtoupper(post_teks("kupon", 30)),
        "catatan" => post_teks("catatan", 255),
    ];
    if ($in["nama"] === "") $galat[] = "Nama penerima wajib diisi.";
    if (!preg_match('/^[0-9+\-\s]{8,20}$/', $in["telepon"])) $galat[] = "Nomor telepon tidak valid (8-20 digit).";
    if (mb_strlen($in["alamat"]) < 10) $galat[] = "Alamat lengkap wajib diisi (minimal 10 karakter).";
    if (!isset(KURIR[$in["kurir"]])) $galat[] = "Pilih kurir pengiriman.";
    if (!isset(METODE_BAYAR[$in["metode"]])) $galat[] = "Pilih metode pembayaran.";
    if (!$galat) {
        [$kode, $gal] = pesanan_buat($u, $in);
        if ($kode) {
            set_flash("Pesanan berhasil dibuat. Terima kasih sudah berbelanja!");
            redirect("pesanan_detail.php?kode=" . rawurlencode($kode));
        }
        $galat[] = $gal;
        $item = kr_item();
        if (!$item) redirect("keranjang.php");
    }
}
$sub = array_sum(array_column($item, "subtotal"));
[$kupon, $galKupon] = kupon_cek($in["kupon"], $sub);
$t = total_hitung($item, $in["kurir"], $kupon);
tampil_awal("Checkout");
?>
<h1>Checkout</h1>
<?php foreach ($galat as $g): ?><div class="alert alert-bad" role="alert"><?= e($g) ?></div><?php endforeach; ?>
<?php if ($galKupon && $in["kupon"] !== ""): ?><div class="alert alert-warn"><?= e($galKupon) ?></div><?php endif; ?>
<form method="post" action="<?= e(url("checkout.php")) ?>" class="dua-kolom" data-checkout data-subtotal="<?= (int)$sub ?>" data-diskon="<?= (int)$t["diskon"] ?>" data-gratis="<?= GRATIS_ONGKIR_MIN ?>">
  <?= csrf_field() ?>
  <div class="form-kolom">
    <fieldset class="kartu-form"><legend>1. Alamat pengiriman</legend>
      <label>Nama penerima<input type="text" name="nama" value="<?= e($in["nama"]) ?>" required maxlength="100"></label>
      <label>Nomor telepon<input type="tel" name="telepon" value="<?= e($in["telepon"]) ?>" required maxlength="20"></label>
      <label>Alamat lengkap<textarea name="alamat" rows="3" required maxlength="500"><?= e($in["alamat"]) ?></textarea></label>
      <label>Catatan untuk kurir (opsional)<input type="text" name="catatan" value="<?= e($in["catatan"]) ?>" maxlength="255"></label>
    </fieldset>
    <fieldset class="kartu-form"><legend>2. Kurir</legend>
      <?php foreach (KURIR as $k => $v): ?>
        <label class="pilihan"><input type="radio" name="kurir" value="<?= e($k) ?>" data-tarif="<?= (int)$v["tarif"] ?>" <?= $in["kurir"] === $k ? "checked" : "" ?>>
          <span><b><?= e($v["nama"]) ?></b> <small>estimasi <?= e($v["estimasi"]) ?></small></span>
          <span class="hrg"><?= $k === "reguler" ? "<span data-reguler>" . rupiah($v["tarif"]) . "</span>" : rupiah($v["tarif"]) ?></span></label>
      <?php endforeach; ?>
    </fieldset>
    <fieldset class="kartu-form"><legend>3. Pembayaran</legend>
      <?php foreach (METODE_BAYAR as $k => $v): ?>
        <label class="pilihan"><input type="radio" name="metode" value="<?= e($k) ?>" <?= $in["metode"] === $k ? "checked" : "" ?>>
          <span><b><?= e($v["nama"]) ?></b> <small><?= e($v["info"]) ?></small></span></label>
      <?php endforeach; ?>
    </fieldset>
  </div>
  <aside class="ringkasan">
    <h2>Pesanan Anda</h2>
    <ul class="mini-item">
      <?php foreach ($item as $it): ?><li><img src="<?= e(gambar_url($it["gambar"])) ?>" <?= gambar_fallback_attr($it["slug"]) ?>" alt="" width="44" height="44"><span><?= e($it["nama"]) ?> <small>× <?= (int)$it["jumlah"] ?></small></span><b><?= rupiah($it["subtotal"]) ?></b></li><?php endforeach; ?>
    </ul>
    <label>Kode kupon<input type="text" name="kupon" value="<?= e($in["kupon"]) ?>" maxlength="30" placeholder="Opsional"></label>
    <dl class="rincian">
      <dt>Subtotal</dt><dd><?= rupiah($t["subtotal"]) ?></dd>
      <dt>Diskon</dt><dd class="hemat" data-diskon-teks>-<?= rupiah($t["diskon"]) ?></dd>
      <dt>Ongkir</dt><dd data-ongkir><?= $t["ongkir"] ? rupiah($t["ongkir"]) : "Gratis" ?></dd>
      <dt class="total">Total</dt><dd class="total" data-total><?= rupiah($t["total"]) ?></dd>
    </dl>
    <button class="tombol blok besar" type="submit">Buat pesanan</button>
    <p class="info-kecil">Stok dikurangi saat pesanan dibuat. Pesanan yang belum dibayar dapat dibatalkan.</p>
  </aside>
</form>
<?php tampil_akhir();
