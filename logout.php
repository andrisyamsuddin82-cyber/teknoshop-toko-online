<?php
require __DIR__ . "/inc/bootstrap.php";
wajib_post();
logout_sesi();
set_flash("Anda sudah keluar.");
redirect("index.php");
