<?php
// config/database.php

$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP biasanya kosong
$db   = 'db_restoran';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Set timezone agar waktu pesanan sesuai WIB
date_default_timezone_set('Asia/Jakarta');
?>