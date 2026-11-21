<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_post();
wajib_login();
$u = pengguna();
$pid = post_int("produk_id");
$p = produk_id($pid);
if (!$p) { http_response_code(404); exit("Produk tidak ditemukan."); }
$rating = post_int("rating");
if ($rating < 1 || $rating > 5) { set_flash("Rating harus 1 sampai 5.", "bad"); redirect("produk.php?slug=" . rawurlencode($p["slug"])); }
if (!boleh_ulas((int)$u["id"], $pid)) { set_flash("Anda hanya dapat mengulas produk dari pesanan yang sudah selesai.", "bad"); redirect("produk.php?slug=" . rawurlencode($p["slug"])); }
if (nilai("SELECT 1 FROM ulasan WHERE produk_id = ? AND pengguna_id = ?", [$pid, $u["id"]])) {
    set_flash("Anda sudah mengulas produk ini.", "warn");
} else {
    q("INSERT INTO ulasan (produk_id, pengguna_id, rating, komentar, dibuat) VALUES (?,?,?,?,?)",
        [$pid, $u["id"], $rating, post_teks("komentar", 500) ?: null, date("Y-m-d H:i:s")]);
    set_flash("Terima kasih atas ulasan Anda!");
}
redirect("produk.php?slug=" . rawurlencode($p["slug"]) . "#ulasan");
