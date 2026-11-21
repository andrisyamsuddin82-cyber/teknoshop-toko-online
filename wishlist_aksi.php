<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_post();
wajib_login();
$u = pengguna();
$pid = post_int("produk_id");
if (produk_id($pid)) {
    if (di_wishlist((int)$u["id"], $pid)) {
        q("DELETE FROM wishlist WHERE pengguna_id = ? AND produk_id = ?", [$u["id"], $pid]);
        set_flash("Dihapus dari wishlist.");
    } else {
        q("INSERT INTO wishlist (pengguna_id, produk_id, dibuat) VALUES (?,?,?)", [$u["id"], $pid, date("Y-m-d H:i:s")]);
        set_flash("Ditambahkan ke wishlist.");
    }
}
redirect(aman_kembali(post_teks("kembali", 300), "wishlist.php"));
