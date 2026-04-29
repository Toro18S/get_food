<?php
// Panggil konfigurasi (otomatis start session)
require_once 'config/functions.php';

// Cek apakah user sudah punya sesi login?
if (isset($_SESSION['user'])) {
    
    // Jika dia Customer -> Lempar ke Menu
    // Jika dia Customer -> Lempar ke Home
    if ($_SESSION['user']['role'] == 'customer') {
        header("Location: " . url('customer/home.php')); // Arahkan ke Home
        exit;
    }
    // Jika dia Admin/Pegawai -> Lempar ke Dashboard
    else {
        header("Location: " . url('admin/index.php'));
        exit;
    }

} else {
    // Jika belum login -> Lempar ke Halaman Login
    header("Location: " . url('auth/login.php'));
    exit;
}
?>