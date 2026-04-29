<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login (atau index.php)
// Kita pakai header native karena session sudah hancur, fungsi redirect mungkin butuh file config yang session_start lagi
header("Location: ../auth/login.php");
exit;
?>