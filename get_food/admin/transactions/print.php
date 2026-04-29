<?php
require_once '../../config/functions.php';

if (!isset($_GET['id'])) {
    die("Order ID tidak ditemukan.");
}

$id = $_GET['id'];

// 1. QUERY UTAMA: Join ke tabel restaurants untuk ambil Nama & Alamat Cabang
$order = query("SELECT o.*, r.name as resto_name, r.address as resto_address 
                FROM orders o 
                JOIN restaurants r ON o.restaurant_id = r.restaurant_id 
                WHERE o.order_id = '$id'")[0];

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap');
        
        body {
            font-family: 'Inconsolata', monospace; /* Font struk */
            font-size: 12px;
            width: 58mm; /* Ukuran standar kertas thermal 58mm */
            margin: 0 auto;
            background: #fff;
            padding: 10px 0;
            color: #000;
        }
        
        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line { border-bottom: 1px dashed #000; margin: 8px 0; }
        
        /* Header */
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        /* Info Transaksi */
        .info { margin-top: 10px; display: flex; flex-direction: column; gap: 2px; }
        .info-row { display: flex; justify-content: space-between; }
        
        /* Tabel Item */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; padding-bottom: 4px; }
        td { padding: 2px 0; vertical-align: top; }
        
        /* Kolom Harga */
        .col-name { width: 55%; }
        .col-qty { width: 10%; text-align: center; }
        .col-total { width: 35%; text-align: right; }

        /* Totals */
        .totals { margin-top: 5px; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .grand-total { font-size: 14px; margin: 5px 0; }

        /* Footer */
        .footer { margin-top: 15px; font-size: 10px; }

        /* Tombol Navigasi (Hanya muncul di layar, tidak ikut diprint) */
        @media print {
            .no-print { display: none; }
            body { width: 100%; }
        }
        
        .action-area {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            display: block; width: 100%; padding: 12px;
            text-align: center; text-decoration: none;
            font-family: sans-serif; cursor: pointer; border: none;
            font-size: 14px; font-weight: bold; border-radius: 5px;
        }

        .btn-print { background: #000; color: #fff; }
        .btn-back { background: #e5e7eb; color: #374151; }
        .btn-back:hover { background: #d1d5db; }
    </style>
</head>
<body>

    <div class="header text-center">
        <h2 class="bold"><?php echo $order['resto_name']; ?></h2>
        <p><?php echo $order['resto_address']; ?></p>
    </div>

    <div class="dashed-line"></div>

    <div class="info">
        <div class="info-row">
            <span>Order ID</span>
            <span>#<?php echo $order['order_id']; ?></span>
        </div>
        <div class="info-row">
            <span>Tanggal</span>
            <span><?php echo date('d/m/y H:i', strtotime($order['created_at'])); ?></span>
        </div>
        <div class="info-row">
            <span>Pelanggan</span>
            <span><?php echo substr($order['customer_name'], 0, 15); ?></span>
        </div>
        <div class="info-row">
            <span>Meja</span>
            <span><?php echo $order['table_number']; ?></span>
        </div>
    </div>

    <div class="dashed-line"></div>

    <table>
        <thead>
            <tr>
                <th class="col-name">Item</th>
                <th class="col-qty">Qty</th>
                <th class="col-total">Total</th>
            </tr>
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
            <span>Metode</span>
            <span style="text-transform: uppercase;"><?php echo $order['payment_method']; ?></span>
        </div>

        <?php if($order['payment_method'] == 'tunai'): ?>
            <div class="totals-row">
                <span>Tunai</span>
                <span><?php echo format_rupiah($order['amount_received']); ?></span>
            </div>
            <div class="totals-row">
                <span>Kembali</span>
                <span><?php echo format_rupiah($order['change_amount']); ?></span>
            </div>
        <?php else: ?>
            <div class="totals-row bold" style="margin-top: 5px;">
                <span>STATUS</span>
                <span>LUNAS</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashed-line"></div>

    <div class="footer text-center">
        <p>Terima Kasih atas Kunjungan Anda!</p>
        <p>Follow us @getfood.id</p>
    </div>

    <div class="no-print action-area">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
        
        <button onclick="finishTransaction()" class="btn btn-back">⬅ Selesai & Kembali</button>
    </div>

    <script>
        // Otomatis muncul dialog print saat halaman dibuka (Opsional, bisa dihapus kalau mengganggu)
        // window.onload = function() { window.print(); }

        function finishTransaction() {
            Swal.fire({
                title: 'Transaksi Selesai!',
                text: 'Pembayaran telah berhasil dikonfirmasi.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'index.php';
            });
        }
    </script>

</body>
</html>