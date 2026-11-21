<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
$galP = [];
$galS = [];
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    cek_csrf();
    if (post_teks("aksi", 10) === "profil") {
        $nama = post_teks("nama", 100); $tel = post_teks("telepon", 20); $alamat = post_teks("alamat", 500);
        if ($nama === "") $galP[] = "Nama wajib diisi.";
        if ($tel !== "" && !preg_match('/^[0-9+\-\s]{8,20}$/', $tel)) $galP[] = "Nomor telepon tidak valid.";
        if (!$galP) {
            q("UPDATE pengguna SET nama = ?, telepon = ?, alamat = ? WHERE id = ?", [$nama, $tel ?: null, $alamat ?: null, $u["id"]]);
            set_flash("Profil diperbarui.");
            redirect("akun.php");
        }
        $u = array_merge($u, ["nama" => $nama, "telepon" => $tel, "alamat" => $alamat]);
    } else {
        $lama = (string)($_POST["lama"] ?? ""); $baru = (string)($_POST["baru"] ?? "");
        $hash = nilai("SELECT password FROM pengguna WHERE id = ?", [$u["id"]]);
        if (!password_verify($lama, $hash)) $galS[] = "Kata sandi lama salah.";
        if (strlen($baru) < 6) $galS[] = "Kata sandi baru minimal 6 karakter.";
        if ($baru !== (string)($_POST["baru2"] ?? "")) $galS[] = "Konfirmasi kata sandi baru tidak sama.";
        if (!$galS) {
            q("UPDATE pengguna SET password = ? WHERE id = ?", [password_hash($baru, PASSWORD_DEFAULT), $u["id"]]);
            session_regenerate_id(true);
            set_flash("Kata sandi diganti.");
            redirect("akun.php");
        }
    }
}
$ringkas = ambil("SELECT COUNT(*) AS n, COALESCE(SUM(CASE WHEN status <> 'dibatalkan' THEN total ELSE 0 END), 0) AS belanja FROM pesanan WHERE pengguna_id = ?", [$u["id"]]);
tampil_awal("Profil saya");
?>
<h1>Profil saya</h1>
<div class="stat-baris">
  <div class="stat"><span>Total pesanan</span><b><?= (int)$ringkas["n"] ?></b></div>
  <div class="stat"><span>Total belanja</span><b><?= rupiah($ringkas["belanja"]) ?></b></div>
  <div class="stat"><span>Email</span><b class="kecil"><?= e($u["email"]) ?></b></div>
</div>
<div class="dua-sama">
  <form method="post" class="form kartu-form" action="<?= e(url("akun.php")) ?>">
    <h2>Data diri</h2>
    <?php foreach ($galP as $g): ?><div class="alert alert-bad"><?= e($g) ?></div><?php endforeach; ?>
    <?= csrf_field() ?><input type="hidden" name="aksi" value="profil">
    <label>Nama<input type="text" name="nama" value="<?= e($u["nama"]) ?>" required maxlength="100"></label>
    <label>Telepon<input type="tel" name="telepon" value="<?= e($u["telepon"]) ?>" maxlength="20"></label>
    <label>Alamat utama<textarea name="alamat" rows="3" maxlength="500"><?= e($u["alamat"]) ?></textarea></label>
    <button class="tombol" type="submit">Simpan</button>
  </form>
  <form method="post" class="form kartu-form" action="<?= e(url("akun.php")) ?>">
    <h2>Ganti kata sandi</h2>
    <?php foreach ($galS as $g): ?><div class="alert alert-bad"><?= e($g) ?></div><?php endforeach; ?>
    <?= csrf_field() ?><input type="hidden" name="aksi" value="sandi">
    <label>Kata sandi lama<input type="password" name="lama" required autocomplete="current-password"></label>
    <label>Kata sandi baru<input type="password" name="baru" required minlength="6" autocomplete="new-password"></label>
    <label>Ulangi kata sandi baru<input type="password" name="baru2" required minlength="6" autocomplete="new-password"></label>
    <button class="tombol" type="submit">Ganti kata sandi</button>
  </form>
</div>
<?php tampil_akhir();
