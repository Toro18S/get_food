<?php
require_once '../../config/functions.php';

// Ambil semua order, urutkan dari yang terbaru
$orders = query("SELECT * FROM orders ORDER BY created_at DESC");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📝 Semua Pesanan</h1>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Pelanggan</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Total</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Waktu</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($orders as $order) : ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-700">#<?php echo $order['order_id']; ?></td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800"><?php echo $order['customer_name']; ?></div>
                            <div class="text-xs text-gray-500">Meja <?php echo $order['table_number']; ?> • <?php echo ucfirst($order['order_type']); ?></div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700">
                            <?php echo format_rupiah($order['total_amount']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $status_color = 'gray';
                            if($order['status'] == 'pending') $status_color = 'yellow';
                            if($order['status'] == 'cooking') $status_color = 'blue';
                            if($order['status'] == 'served') $status_color = 'orange';
                            if($order['status'] == 'completed') $status_color = 'green';
                            ?>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-<?php echo $status_color; ?>-100 text-<?php echo $status_color; ?>-700">
                                <?php echo strtoupper($order['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo date('d/m H:i', strtotime($order['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($order['status'] == 'served') : ?>
                                <form action="update_status.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="status" value="completed" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded shadow">
                                        💰 Bayar/Selesai
                                    </button>
                                </form>
                            <?php elseif ($order['status'] == 'completed') : ?>
                                <span class="text-green-600 text-xs font-bold">Lunas ✅</span>
                            <?php else : ?>
                                <span class="text-gray-400 text-xs">Menunggu Dapur</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>