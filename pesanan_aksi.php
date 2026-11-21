<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_post();
wajib_login();
$o = pesanan_kode(post_teks("kode", 40), (int)pengguna()["id"]);
if (!$o) { http_response_code(404); exit("Pesanan tidak ditemukan."); }
$peta = ["bayar" => "menunggu_verifikasi", "batal" => "dibatalkan", "terima" => "selesai"];
$baru = $peta[post_teks("aksi", 10)] ?? null;
if (!$baru) { http_response_code(400); exit("Aksi tidak dikenal."); }
$gal = pesanan_ubah_status($o, $baru, "pelanggan");
if ($gal) set_flash($gal, "bad");
else set_flash(["menunggu_verifikasi" => "Konfirmasi pembayaran terkirim. Admin akan memverifikasi.", "dibatalkan" => "Pesanan dibatalkan dan stok dikembalikan.", "selesai" => "Terima kasih! Pesanan selesai. Anda kini dapat menulis ulasan."][$baru]);
redirect("pesanan_detail.php?kode=" . rawurlencode($o["kode"]));
