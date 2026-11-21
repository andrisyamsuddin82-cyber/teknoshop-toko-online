<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_post();
$aksi = post_teks("aksi");
$pid = post_int("produk_id");
$tujuan = "keranjang.php";

if ($aksi === "tambah") {
    $gal = kr_tambah($pid, max(1, min(99, post_int("jumlah", 1))));
    if ($gal !== "" && strpos($gal, "disesuaikan") === false) {
        set_flash($gal, "bad");
        redirect("katalog.php");
    }
    set_flash($gal !== "" ? $gal : "Produk ditambahkan ke keranjang.", $gal !== "" ? "warn" : "ok");
    if (!empty($_POST["langsung"])) $tujuan = "checkout.php";
} elseif ($aksi === "ubah") {
    $p = produk_id($pid);
    $j = max(0, min(99, post_int("jumlah", 1)));
    if ($p && $j > $p["stok"]) { $j = (int)$p["stok"]; set_flash("Jumlah disesuaikan dengan stok tersedia ($j).", "warn"); }
    kr_set($pid, $j);
} elseif ($aksi === "hapus") {
    kr_set($pid, 0);
    set_flash("Produk dihapus dari keranjang.");
} elseif ($aksi === "kupon") {
    $kode = strtoupper(post_teks("kode", 30));
    $sub = array_sum(array_column(kr_item(), "subtotal"));
    [$k, $gal] = kupon_cek($kode, $sub);
    if ($gal) set_flash($gal, "bad");
    elseif ($k) { $_SESSION["kupon"] = $k["kode"]; set_flash("Kupon {$k["kode"]} dipakai."); }
    else set_flash("Masukkan kode kupon.", "warn");
} elseif ($aksi === "kupon_hapus") {
    unset($_SESSION["kupon"]);
    set_flash("Kupon dilepas.");
}
redirect($tujuan);
