<?php
require __DIR__ . "/inc/bootstrap.php";
$f = [
    "q" => mb_substr(get_teks("q"), 0, 80),
    "kategori" => get_teks("kategori"),
    "min" => get_int("min"),
    "maks" => get_int("maks"),
    "urut" => isset(URUTAN_PRODUK[get_teks("urut")]) ? get_teks("urut") : "terbaru",
    "hal" => max(1, get_int("hal", 1)),
];
$kat = kategori_semua();
$katAktif = null;
foreach ($kat as $k) if ($k["slug"] === $f["kategori"]) $katAktif = $k;
if (!$katAktif) $f["kategori"] = "";
$hasil = produk_cari($f);
$wish = sudah_login() ? id_wishlist(pengguna()["id"]) : [];
$param = array_filter(["q" => $f["q"], "kategori" => $f["kategori"], "min" => $f["min"] ?: "", "maks" => $f["maks"] ?: "", "urut" => $f["urut"] !== "terbaru" ? $f["urut"] : ""], fn($v) => $v !== "");
$judul = $katAktif ? $katAktif["nama"] : ($f["q"] !== "" ? "Hasil pencarian" : "Semua Produk");
tampil_awal($judul, "", $katAktif ? "kat-" . $katAktif["slug"] : "katalog");
?>
<nav class="remah" aria-label="Jejak"><a href="<?= e(url("index.php")) ?>">Beranda</a> › <span><?= e($judul) ?></span></nav>
<div class="katalog">
  <aside class="filter">
    <form method="get" action="<?= e(url("katalog.php")) ?>">
      <h2>Filter</h2>
      <label>Kata kunci<input type="search" name="q" value="<?= e($f["q"]) ?>" maxlength="80"></label>
      <label>Kategori
        <select name="kategori">
          <option value="">Semua kategori</option>
          <?php foreach ($kat as $k): ?><option value="<?= e($k["slug"]) ?>" <?= $k["slug"] === $f["kategori"] ? "selected" : "" ?>><?= e($k["nama"]) ?> (<?= (int)$k["jumlah"] ?>)</option><?php endforeach; ?>
        </select></label>
      <div class="dua">
        <label>Harga min<input type="number" name="min" min="0" step="1000" value="<?= $f["min"] ?: "" ?>" placeholder="0"></label>
        <label>Harga maks<input type="number" name="maks" min="0" step="1000" value="<?= $f["maks"] ?: "" ?>" placeholder="∞"></label>
      </div>
      <label>Urutkan
        <select name="urut"><?php foreach (URUTAN_PRODUK as $k => $v): ?><option value="<?= e($k) ?>" <?= $k === $f["urut"] ? "selected" : "" ?>><?= e($v[0]) ?></option><?php endforeach; ?></select></label>
      <button class="tombol blok" type="submit">Terapkan</button>
      <a class="tautan-kecil" href="<?= e(url("katalog.php")) ?>">Atur ulang</a>
    </form>
  </aside>
  <section>
    <div class="judul-bagian"><h1><?= e($judul) ?></h1><span class="redup"><?= (int)$hasil["total"] ?> produk ditemukan</span></div>
    <?php if ($katAktif && $katAktif["deskripsi"]): ?><p class="redup"><?= e($katAktif["deskripsi"]) ?></p><?php endif; ?>
    <?php if (!$hasil["baris"]): ?>
      <div class="kosong"><?= ikon("search", 40) ?><h3>Produk tidak ditemukan</h3><p>Coba ubah kata kunci atau filter harga.</p><a class="tombol" href="<?= e(url("katalog.php")) ?>">Lihat semua produk</a></div>
    <?php else: ?>
      <div class="grid-produk"><?php foreach ($hasil["baris"] as $p) echo kartu_produk($p, $wish); ?></div>
      <?= paginasi($hasil["hal"], $hasil["halaman"], "katalog.php", $param) ?>
    <?php endif; ?>
  </section>
</div>
<?php tampil_akhir();
