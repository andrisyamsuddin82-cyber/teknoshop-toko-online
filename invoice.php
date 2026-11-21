<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
// Admin boleh membuka semua invoice; pelanggan hanya miliknya
$o = pesanan_kode(get_teks("kode"), $u["peran"] === "admin" ? null : (int)$u["id"]);
if (!$o) { http_response_code(404); exit("Invoice tidak ditemukan."); }
$item = pesanan_item((int)$o["id"]);
$metode = METODE_BAYAR[$o["metode_bayar"]]["nama"] ?? $o["metode_bayar"];
$kurir = KURIR[$o["kurir"]]["nama"] ?? $o["kurir"];
?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice <?= e($o["kode"]) ?></title>
<link rel="stylesheet" href="<?= e(aset("assets/css/style.css")) ?>">
<script src="<?= e(aset("assets/js/app.js")) ?>" defer></script>
</head><body class="invoice-halaman">
<div class="invoice">
  <div class="tidak-cetak aksi-baris"><button class="tombol" type="button" data-cetak><?= ikon("print", 16) ?> Cetak</button></div>
  <header class="inv-kepala">
    <div class="logo"><img src="<?= e(url("assets/img/logo.svg")) ?>" alt="" width="40" height="40"><span><?= e(NAMA_TOKO) ?></span></div>
    <div class="kanan"><h1>INVOICE</h1><b><?= e($o["kode"]) ?></b><br><span><?= e(tgl($o["dibuat"], true)) ?></span><br><?= badge_status($o["status"]) ?></div>
  </header>
  <div class="inv-info">
    <div><h3>Dikirim ke</h3><b><?= e($o["nama_penerima"]) ?></b><br><?= e($o["telepon"]) ?><br><?= nl2br(e($o["alamat"])) ?></div>
    <div><h3>Pengiriman &amp; pembayaran</h3>Kurir: <?= e($kurir) ?><br>Pembayaran: <?= e($metode) ?><?= $o["resi"] ? "<br>Resi: " . e($o["resi"]) : "" ?></div>
  </div>
  <table class="tabel">
    <thead><tr><th>Produk</th><th class="kanan">Harga</th><th class="kanan">Jml</th><th class="kanan">Subtotal</th></tr></thead>
    <tbody><?php foreach ($item as $it): ?><tr><td><?= e($it["nama"]) ?></td><td class="kanan"><?= rupiah($it["harga"]) ?></td><td class="kanan"><?= (int)$it["jumlah"] ?></td><td class="kanan"><?= rupiah($it["harga"] * $it["jumlah"]) ?></td></tr><?php endforeach; ?></tbody>
  </table>
  <dl class="rincian inv-total">
    <dt>Subtotal</dt><dd><?= rupiah($o["subtotal"]) ?></dd>
    <?php if ($o["diskon"]): ?><dt>Diskon <?= e($o["kupon_kode"]) ?></dt><dd>-<?= rupiah($o["diskon"]) ?></dd><?php endif; ?>
    <dt>Ongkir</dt><dd><?= $o["ongkir"] ? rupiah($o["ongkir"]) : "Gratis" ?></dd>
    <dt class="total">Total</dt><dd class="total"><?= rupiah($o["total"]) ?></dd>
  </dl>
  <p class="redup tengah">Terima kasih telah berbelanja di <?= e(NAMA_TOKO) ?>.</p>
</div></body></html>
