<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_login();
$u = pengguna();
$daftar = semua("SELECT o.*, (SELECT COUNT(*) FROM pesanan_item i WHERE i.pesanan_id = o.id) AS jml_jenis,
        (SELECT i.nama FROM pesanan_item i WHERE i.pesanan_id = o.id ORDER BY i.id LIMIT 1) AS nama_pertama
        FROM pesanan o WHERE o.pengguna_id = ? ORDER BY o.id DESC", [$u["id"]]);
tampil_awal("Pesanan saya");
?>
<h1>Pesanan saya</h1>
<?php if (!$daftar): ?>
  <div class="kosong"><?= ikon("box", 44) ?><h3>Belum ada pesanan</h3><p>Pesanan Anda akan muncul di sini.</p><a class="tombol" href="<?= e(url("katalog.php")) ?>">Mulai belanja</a></div>
<?php else: ?>
  <div class="tabel-wrap"><table class="tabel">
    <thead><tr><th>Kode</th><th>Tanggal</th><th>Produk</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $o): ?>
      <tr>
        <td data-label="Kode"><b><?= e($o["kode"]) ?></b></td>
        <td data-label="Tanggal"><?= e(tgl($o["dibuat"], true)) ?></td>
        <td data-label="Produk"><?= e(potong($o["nama_pertama"], 34)) ?><?= $o["jml_jenis"] > 1 ? " <small class='redup'>+" . ((int)$o["jml_jenis"] - 1) . " lainnya</small>" : "" ?></td>
        <td data-label="Total"><?= rupiah($o["total"]) ?></td>
        <td data-label="Status"><?= badge_status($o["status"]) ?></td>
        <td><a class="tombol kecil sekunder" href="<?= e(url("pesanan_detail.php?kode=" . rawurlencode($o["kode"]))) ?>">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
<?php tampil_akhir();
