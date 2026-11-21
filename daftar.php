<?php
require __DIR__ . "/inc/bootstrap.php";
if (sudah_login()) redirect("index.php");
$v = ["nama" => "", "email" => "", "telepon" => ""];
$galat = [];
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    cek_csrf();
    $v = ["nama" => post_teks("nama", 100), "email" => strtolower(post_teks("email", 150)), "telepon" => post_teks("telepon", 20)];
    $sandi = (string)($_POST["password"] ?? "");
    if ($v["nama"] === "") $galat[] = "Nama wajib diisi.";
    if (!filter_var($v["email"], FILTER_VALIDATE_EMAIL)) $galat[] = "Format email tidak valid.";
    elseif (nilai("SELECT 1 FROM pengguna WHERE email = ?", [$v["email"]])) $galat[] = "Email sudah terdaftar.";
    if ($v["telepon"] !== "" && !preg_match('/^[0-9+\-\s]{8,20}$/', $v["telepon"])) $galat[] = "Nomor telepon tidak valid.";
    if (strlen($sandi) < 6) $galat[] = "Kata sandi minimal 6 karakter.";
    if ($sandi !== (string)($_POST["password2"] ?? "")) $galat[] = "Konfirmasi kata sandi tidak sama.";
    if (!$galat) {
        q("INSERT INTO pengguna (nama, email, telepon, alamat, password, peran, dibuat) VALUES (?,?,?,?,?,?,?)",
            [$v["nama"], $v["email"], $v["telepon"] ?: null, null, password_hash($sandi, PASSWORD_DEFAULT), "pelanggan", date("Y-m-d H:i:s")]);
        login_coba($v["email"], $sandi);
        set_flash("Akun berhasil dibuat. Selamat berbelanja!");
        redirect("index.php");
    }
}
tampil_awal("Daftar");
?>
<div class="auth">
  <h1>Buat akun</h1>
  <?php foreach ($galat as $g): ?><div class="alert alert-bad" role="alert"><?= e($g) ?></div><?php endforeach; ?>
  <form method="post" class="form kartu-form" action="<?= e(url("daftar.php")) ?>">
    <?= csrf_field() ?>
    <label>Nama lengkap<input type="text" name="nama" value="<?= e($v["nama"]) ?>" required maxlength="100"></label>
    <label>Email<input type="email" name="email" value="<?= e($v["email"]) ?>" required></label>
    <label>Telepon (opsional)<input type="tel" name="telepon" value="<?= e($v["telepon"]) ?>" maxlength="20"></label>
    <label>Kata sandi<input type="password" name="password" required minlength="6" autocomplete="new-password"></label>
    <label>Ulangi kata sandi<input type="password" name="password2" required minlength="6" autocomplete="new-password"></label>
    <button class="tombol blok besar" type="submit">Daftar</button>
    <p class="tengah redup">Sudah punya akun? <a href="<?= e(url("login.php")) ?>">Masuk</a></p>
  </form>
</div>
<?php tampil_akhir();
