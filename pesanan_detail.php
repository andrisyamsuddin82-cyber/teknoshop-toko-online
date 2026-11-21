<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
// Pesanan milik pengguna lain dibalas 404 agar keberadaannya tidak bocor
$o = pesanan_kode(get_teks("kode"), (int)$u["id"]);
if (!$o) {
    http_response_code(404);
    tampil_awal("Pesanan tidak ditemukan");
    echo '<div class="kosong"><h1>Pesanan tidak ditemukan</h1><a class="tombol" href="' . e(url("pesanan.php")) . '">Pesanan saya</a></div>';
    tampil_akhir();
    exit;
}
$item = pesanan_item((int)$o["id"]);
$riwayat = pesanan_riwayat((int)$o["id"]);
$metode = METODE_BAYAR[$o["metode_bayar"]] ?? ["nama" => $o["metode_bayar"], "info" => ""];
$kurir = KURIR[$o["kurir"]] ?? ["nama" => $o["kurir"], "estimasi" => ""];
tampil_awal("Pesanan " . $o["kode"]);
?>
<nav class="remah"><a href="<?= e(url("pesanan.php")) ?>">Pesanan saya</a> › <span><?= e($o["kode"]) ?></span></nav>
<div class="judul-bagian"><h1><?= e($o["kode"]) ?></h1><?= badge_status($o["status"]) ?></div>
<div class="dua-kolom">
  <div>
    <section class="kartu-form"><h2>Produk</h2>
      <?php foreach ($item as $it): ?>
        <div class="baris-kr ringkas"><a href="<?= e(url("produk.php?slug=" . rawurlencode($it["slug"]))) ?>"><img src="<?= e(gambar_url($it["gambar"])) ?>" <?= gambar_fallback_attr($it["slug"]) ?>" alt="" width="64" height="64"></a>
          <div class="info"><b><?= e($it["nama"]) ?></b><span class="redup"><?= rupiah($it["harga"]) ?> × <?= (int)$it["jumlah"] ?></span></div>
          <strong><?= rupiah($it["harga"] * $it["jumlah"]) ?></strong></div>
      <?php endforeach; ?>
    </section>
    <?php if ($o["status"] === "menunggu_pembayaran"): ?>
    <section class="kartu-form sorot"><h2>Instruksi pembayaran</h2>
      <p>Transfer tepat <b><?= rupiah($o["total"]) ?></b> melalui <b><?= e($metode["nama"]) ?></b>:</p>
      <p class="kotak-info"><?= e($metode["info"]) ?></p>
      <p class="redup">Setelah membayar, tekan tombol konfirmasi agar admin dapat memverifikasi.</p>
      <div class="aksi-baris">
        <form method="post" action="<?= e(url("pesanan_aksi.php")) ?>"><?= csrf_field() ?><input type="hidden" name="kode" value="<?= e($o["kode"]) ?>"><input type="hidden" name="aksi" value="bayar"><button class="tombol" type="submit"><?= ikon("check", 16) ?> Saya sudah membayar</button></form>
        <form method="post" action="<?= e(url("pesanan_aksi.php")) ?>" data-konfirmasi="Batalkan pesanan ini?"><?= csrf_field() ?><input type="hidden" name="kode" value="<?= e($o["kode"]) ?>"><input type="hidden" name="aksi" value="batal"><button class="tombol bahaya-garis" type="submit">Batalkan pesanan</button></form>
      </div>
    </section>
    <?php elseif ($o["status"] === "dikirim"): ?>
    <section class="kartu-form sorot"><h2>Pesanan dalam pengiriman</h2>
      <p>Nomor resi: <b><?= e($o["resi"]) ?></b> (<?= e($kurir["nama"]) ?>)</p>
      <form method="post" action="<?= e(url("pesanan_aksi.php")) ?>"><?= csrf_field() ?><input type="hidden" name="kode" value="<?= e($o["kode"]) ?>"><input type="hidden" name="aksi" value="terima"><button class="tombol" type="submit"><?= ikon("check", 16) ?> Pesanan sudah saya terima</button></form>
    </section>
    <?php endif; ?>
    <section class="kartu-form"><h2>Status pesanan</h2>
      <ol class="linimasa">
        <?php foreach (array_reverse($riwayat) as $i => $r): ?>
          <li class="<?= $i === 0 ? "kini" : "" ?>"><b><?= e(status_info($r["status"])["label"]) ?></b><span><?= e($r["catatan"]) ?></span><time><?= e(tgl($r["dibuat"], true)) ?></time></li>
        <?php endforeach; ?>
      </ol>
    </section>
  </div>
  <aside class="ringkasan">
    <h2>Rincian</h2>
    <dl class="rincian">
      <dt>Subtotal</dt><dd><?= rupiah($o["subtotal"]) ?></dd>
      <?php if ($o["diskon"]): ?><dt>Diskon <?= e($o["kupon_kode"]) ?></dt><dd class="hemat">-<?= rupiah($o["diskon"]) ?></dd><?php endif; ?>
      <dt>Ongkir <?= e($kurir["nama"]) ?></dt><dd><?= $o["ongkir"] ? rupiah($o["ongkir"]) : "Gratis" ?></dd>
      <dt class="total">Total</dt><dd class="total"><?= rupiah($o["total"]) ?></dd>
    </dl>
    <h3>Pengiriman</h3>
    <p><b><?= e($o["nama_penerima"]) ?></b><br><?= e($o["telepon"]) ?><br><?= nl2br(e($o["alamat"])) ?></p>
    <?php if ($o["catatan"]): ?><p class="redup">Catatan: <?= e($o["catatan"]) ?></p><?php endif; ?>
    <p>Pembayaran: <?= e($metode["nama"]) ?></p>
    <?php if ($o["resi"]): ?><p>Resi: <b><?= e($o["resi"]) ?></b></p><?php endif; ?>
    <a class="tombol blok sekunder" href="<?= e(url("invoice.php?kode=" . rawurlencode($o["kode"]))) ?>" target="_blank" rel="noopener"><?= ikon("print", 16) ?> Cetak invoice</a>
  </aside>
</div>
<?php tampil_akhir();
