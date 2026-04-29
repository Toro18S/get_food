<?php
require_once '../../config/functions.php';

// Cek sesi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];

// 1. Ambil nama gambar lama dulu (untuk dihapus nanti)
$data = query("SELECT image_url FROM menu_items WHERE menu_id = '$id'");

// Cek apakah menu ditemukan?
if (empty($data)) {
    set_flash_message('error', 'Menu tidak ditemukan.');
    redirect('admin/menu/index.php');
    exit;
}

$image_file = $data[0]['image_url'];

// --- PROSES PEMBERSIHAN (Solusi Error Foreign Key) ---

// 2. Hapus Ketersediaan di Cabang (Tabel restaurant_menus)
query("DELETE FROM restaurant_menus WHERE menu_id = '$id'");

// 3. Hapus Riwayat Pesanan terkait menu ini (Tabel order_items)
// PERINGATAN: Ini akan menghapus detail menu ini dari nota pesanan lama.
query("DELETE FROM order_items WHERE menu_id = '$id'");

// 4. Hapus Menu Utama (Tabel menu_items)
if (query("DELETE FROM menu_items WHERE menu_id = '$id'")) {
    
    // 5. Hapus file gambar fisik di folder assets/img
    if ($image_file != 'default.jpg') {
        $path = '../../assets/img/' . $image_file;
        if (file_exists($path)) {
            unlink($path); // Hapus file dari folder
        }
    }

    set_flash_message('success', 'Menu dan riwayatnya berhasil dihapus!');
} else {
    set_flash_message('error', 'Gagal menghapus menu.');
}

redirect('admin/menu/index.php');
?>