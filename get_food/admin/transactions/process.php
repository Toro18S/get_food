<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $amount_received = $_POST['amount_received'];
    $change_amount = $_POST['change_amount'];

    // Update Data Order
    $query = "UPDATE orders SET 
              status = 'completed',
              payment_method = '$payment_method',
              amount_received = '$amount_received',
              change_amount = '$change_amount'
              WHERE order_id = '$order_id'";

    if (query($query)) {
        // Redirect langsung ke halaman Print Struk
        header("Location: print.php?id=" . $order_id);
        exit;
    } else {
        set_flash_message('error', 'Gagal memproses pembayaran.');
        redirect('admin/transactions/index.php');
    }
}
?>