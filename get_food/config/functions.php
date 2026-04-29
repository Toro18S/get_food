<?php
// config/functions.php

// Mulai session otomatis setiap kali file ini dipanggil
session_start();

// Panggil file koneksi dan konstanta
require_once 'database.php';
require_once 'constants.php';

/**
 * Fungsi untuk mengambil URL absolut
 * Contoh: url('assets/img/logo.png') -> http://localhost/get_food/assets/img/logo.png
 */
function url($path = '') {
    return BASE_URL . $path;
}

/**
 * Fungsi Query Database Sederhana
 * Menerima string SQL dan mengembalikan array data (jika SELECT)
 */
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    
    // Jika query gagal/error
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    // Jika query SELECT, kembalikan dalam bentuk array assoc
    // Cek apakah $result adalah object (hasil SELECT) atau boolean (hasil INSERT/UPDATE)
    if (is_object($result)) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    return $result; // Mengembalikan true/false untuk INSERT/UPDATE/DELETE
}

/**
 * Fungsi Format Rupiah
 * Contoh: format_rupiah(25000) -> "Rp 25.000"
 */
function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi Redirect (Pindah Halaman)
 */
function redirect($path) {
    header("Location: " . url($path));
    exit;
}

/**
 * Fungsi Debugging (Cek isi variabel lalu stop program)
 */
function dd($var) {
    echo "<pre style='background:#222; color:#0f0; padding:10px; z-index:9999; position:relative;'>";
    var_dump($var);
    echo "</pre>";
    die;
}

/**
 * Fungsi Flash Message (Pesan Kilat untuk Notifikasi)
 * Set pesan: set_flash_message('success', 'Login Berhasil');
 * Tampil pesan: show_flash_message();
 */
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning
        'message' => $message
    ];
}

function show_flash_message() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        
        // Warna alert berdasarkan tipe (Tailwind classes)
        $color = ($type == 'success') ? 'green' : (($type == 'error') ? 'red' : 'yellow');
        
        echo "<div class='bg-{$color}-100 border border-{$color}-400 text-{$color}-700 px-4 py-3 rounded relative mb-4' role='alert'>";
        echo "<span class='block sm:inline'>{$message}</span>";
        echo "</div>";
        
        // Hapus pesan setelah ditampilkan
        unset($_SESSION['flash']);
    }
}

/**
 * Fungsi Pintar untuk Gambar Menu
 * Bisa menangani File Upload (lokal) ATAU Link URL (https)
 */
function menu_image($dbValue) {
    // 1. Cek apakah database kosong/null
    if (empty($dbValue)) {
        return url('assets/img/default.jpg');
    }

    // 2. Cek apakah ini Link URL (dimulai dengan http atau https)
    if (strpos($dbValue, 'http://') === 0 || strpos($dbValue, 'https://') === 0) {
        return $dbValue; // Kembalikan link mentah-mentah
    }

    // 3. Jika bukan link, berarti file lokal di assets/img
    return url('assets/img/' . $dbValue);
}

?>