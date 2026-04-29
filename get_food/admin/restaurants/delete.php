<?php
require_once '../../config/functions.php';

// Cek sesi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];

// --- TAHAP PEMBERSIHAN DATA (CLEANUP) ---
// Kita harus menghapus data anak-anaknya dulu sebelum induknya.

// 1. Hapus Ketersediaan Menu di Cabang ini
query("DELETE FROM restaurant_menus WHERE restaurant_id = '$id'");

// 2. Hapus Pegawai di Cabang ini (Opsional, jika ada)
query("DELETE FROM employees WHERE restaurant_id = '$id'");

// 3. Hapus Detail Pesanan (Order Items) yang terhubung ke Order di Resto ini
// (Agak rumit: Hapus item DIMANA order_id-nya milik resto ini)
query("DELETE order_items FROM order_items 
       INNER JOIN orders ON order_items.order_id = orders.order_id 
       WHERE orders.restaurant_id = '$id'");

// 4. Hapus Pesanan (Orders) di Cabang ini
query("DELETE FROM orders WHERE restaurant_id = '$id'");

// --- TAHAP HAPUS UTAMA ---
// 5. Akhirnya, Hapus Restorannya
if (query("DELETE FROM restaurants WHERE restaurant_id = '$id'")) {
    set_flash_message('success', 'Cabang restoran dan seluruh riwayatnya berhasil dihapus.');
} else {
    set_flash_message('error', 'Gagal menghapus cabang.');
}

redirect('admin/restaurants/index.php');
?>