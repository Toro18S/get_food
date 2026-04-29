<?php
require_once '../../config/functions.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    redirect('auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status   = $_POST['status'];
    
    // Siapkan Query dasar
    $query = "UPDATE orders SET status = '$status' ";

    // Jika Koki memasukkan estimasi waktu (hanya saat status 'cooking')
    if (isset($_POST['estimasi']) && !empty($_POST['estimasi'])) {
        $waktu = $_POST['estimasi'];
        $query .= ", estimasi_waktu = '$waktu' ";
    }

    $query .= "WHERE order_id = '$order_id'";

    if (query($query)) {
        // Redirect kembali ke halaman asal (Kitchen atau List)
        $referer = $_SERVER['HTTP_REFERER'];
        set_flash_message('success', "Status pesanan #$order_id berhasil diubah menjadi $status!");
        header("Location: $referer");
    } else {
        set_flash_message('error', 'Gagal update status.');
        redirect('admin/orders/list.php');
    }
}
?>