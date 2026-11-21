<?php
require __DIR__ . "/inc/bootstrap.php";
$kembali = aman_kembali(get_teks("kembali") ?: post_teks("kembali", 300), "index.php");
if (sudah_login()) redirect($kembali);
$email = "";
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    cek_csrf();
    $email = strtolower(post_teks("email", 150));
    $tunggu = login_terkunci();
    if ($tunggu > 0) set_flash("Terlalu banyak percobaan. Coba lagi dalam $tunggu detik.", "bad");
    elseif (login_coba($email, (string)($_POST["password"] ?? ""))) {
        set_flash("Selamat datang kembali, " . explode(" ", pengguna()["nama"] ?? "")[0] . "!");
        redirect($kembali);
    } else set_flash("Email atau kata sandi salah.", "bad");
}
tampil_awal("Masuk");
?>
<div class="auth">
  <h1>Masuk</h1>
  <form method="post" class="form kartu-form" action="<?= e(url("login.php")) ?>">
    <?= csrf_field() ?><input type="hidden" name="kembali" value="<?= e($kembali) ?>">
    <label>Email<input type="email" name="email" value="<?= e($email) ?>" required autocomplete="username" autofocus></label>
    <label>Kata sandi<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="tombol blok besar" type="submit">Masuk</button>
    <p class="tengah redup">Belum punya akun? <a href="<?= e(url("daftar.php")) ?>">Daftar</a></p>
  </form>
  <?php if (MODE_DEMO): ?>
  <div class="demo"><b>Akun contoh</b>
    <p>Admin: <code>admin@teknoshop.test</code> / <code>admin123</code></p>
    <p>Pelanggan: <code>budi@contoh.test</code> / <code>pelanggan123</code></p>
  </div>
  <?php endif; ?>
</div>
<?php tampil_akhir();
