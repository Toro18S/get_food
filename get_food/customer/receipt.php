<?php
require_once '../config/functions.php';

// Cek Login Customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer') {
    die("Akses ditolak.");
}

if (!isset($_GET['id'])) {
    die("Order ID tidak ditemukan.");
}

$id = $_GET['id'];
$cust_name = $_SESSION['user']['name'];

// 1. QUERY AMAN: Pastikan pesanan ini MILIK user yang sedang login
// Kita tambahkan "AND o.customer_name = '$cust_name'" untuk keamanan
$order_data = query("SELECT o.*, r.name as resto_name, r.address as resto_address 
                     FROM orders o 
                     JOIN restaurants r ON o.restaurant_id = r.restaurant_id 
                     WHERE o.order_id = '$id' AND o.customer_name = '$cust_name'");

if (empty($order_data)) {
    die("Struk tidak ditemukan atau bukan milik Anda.");
}

$order = $order_data[0];
$items = query("SELECT oi.*, m.name 
                FROM order_items oi 
                JOIN menu_items m ON oi.menu_id = m.menu_id 
                WHERE oi.order_id = '$id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?php echo $id; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap');
        body {
            font-family: 'Inconsolata', monospace;
            font-size: 12px;
            width: 100%;
            margin: 0;
            padding: 10px;
            box-sizing: border-box;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        .info { margin-top: 10px; display: flex; flex-direction: column; gap: 2px; }
        .info-row { display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; padding-bottom: 4px; }
        td { padding: 2px 0; vertical-align: top; }
        .col-name { width: 55%; }
        .col-qty { width: 10%; text-align: center; }
        .col-total { width: 35%; text-align: right; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .grand-total { font-size: 14px; margin: 5px 0; }
        .footer { margin-top: 15px; font-size: 10px; text-align: center;}
        
        /* Tombol download/print kecil di pojok */
        .fab-print {
            position: fixed; bottom: 10px; right: 10px;
            background: #000; color: #fff; width: 35px; height: 35px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            text-decoration: none; font-size: 16px; cursor: pointer; border: none;
        }
        @media print { .fab-print { display: none; } }
    </style>
</head>
<body>

    <div class="header text-center">
        <h2 class="bold"><?php echo $order['resto_name']; ?></h2>
        <p><?php echo $order['resto_address']; ?></p>
    </div>

    <div class="dashed-line"></div>

    <div class="info">
        <div class="info-row"><span>ID:</span> <span>#<?php echo $order['order_id']; ?></span></div>
        <div class="info-row"><span>Tgl:</span> <span><?php echo date('d/m/y H:i', strtotime($order['created_at'])); ?></span></div>
        <div class="info-row"><span>Meja:</span> <span><?php echo $order['table_number']; ?></span></div>
    </div>

    <div class="dashed-line"></div>

    <table>
        <thead>
            <tr><th class="col-name">Item</th><th class="col-qty">Qty</th><th class="col-total">Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td class="col-name"><?php echo $item['name']; ?></td>
                <td class="col-qty"><?php echo $item['quantity']; ?></td>
                <td class="col-total"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <div class="totals">
        <div class="totals-row bold grand-total">
            <span>TOTAL</span>
            <span><?php echo format_rupiah($order['total_amount']); ?></span>
        </div>
        <div class="totals-row">
            <span>Bayar (<?php echo strtoupper($order['payment_method'] ?? '-'); ?>)</span>
            <span><?php echo format_rupiah($order['amount_received']); ?></span>
        </div>
        <?php if($order['change_amount'] > 0): ?>
        <div class="totals-row">
            <span>Kembali</span>
            <span><?php echo format_rupiah($order['change_amount']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Status: <?php echo strtoupper($order['status']); ?></p>
        <p>Terima Kasih!</p>
    </div>

    <button onclick="window.print()" class="fab-print">🖨️</button>

</body>
</html>