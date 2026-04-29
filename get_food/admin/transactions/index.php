<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// Ambil Order Siap Bayar
$unpaid_orders = query("SELECT * FROM orders WHERE status = 'served' ORDER BY created_at ASC");
$today_sales = query("SELECT * FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE() ORDER BY created_at DESC");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-cash-register text-orange-500"></i> Kasir & Transaksi
        </h1>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8 border border-orange-100">
            <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                <h3 class="font-bold text-orange-800">Menunggu Pembayaran</h3>
                <span class="bg-orange-200 text-orange-800 text-xs font-bold px-2 py-1 rounded-full"><?php echo count($unpaid_orders); ?> Pesanan</span>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-orange-50/50 text-orange-900 text-sm">
                    <tr>
                        <th class="px-6 py-3">Order ID</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Meja</th>
                        <th class="px-6 py-3">Total Tagihan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($unpaid_orders)): ?>
                        <tr><td colspan="5" class="text-center py-8 text-gray-400">Tidak ada antrian pembayaran.</td></tr>
                    <?php else: ?>
                        <?php foreach ($unpaid_orders as $order) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4"><?php echo $order['customer_name']; ?></td>
                            <td class="px-6 py-4 font-bold text-gray-600"><?php echo $order['table_number']; ?></td>
                            <td class="px-6 py-4 font-bold text-lg text-primary"><?php echo format_rupiah($order['total_amount']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openPaymentModal(<?php echo htmlspecialchars(json_encode($order)); ?>)" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition flex items-center gap-2 mx-auto">
                                    <i class="fas fa-wallet"></i> Bayar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-700">Riwayat Transaksi Hari Ini</h3>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3 text-center">Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($today_sales as $sale) : ?>
                    <tr>
                        <td class="px-6 py-3">#<?php echo $sale['order_id']; ?></td>
                        <td class="px-6 py-3 text-gray-500"><?php echo date('H:i', strtotime($sale['created_at'])); ?></td>
                        <td class="px-6 py-3 font-bold"><?php echo format_rupiah($sale['total_amount']); ?></td>
                        <td class="px-6 py-3 uppercase text-xs font-bold text-gray-500"><?php echo $sale['payment_method']; ?></td>
                        <td class="px-6 py-3 text-center">
                            <a href="print.php?id=<?php echo $sale['order_id']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-print"></i> Struk
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto mt-10 p-8 transform transition-all scale-100">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Konfirmasi Pembayaran</h2>
                <p class="text-sm text-gray-500">Order ID: <span id="modalOrderId" class="font-bold text-orange-500">#</span></p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <form action="process.php" method="POST" onsubmit="return validatePayment()">
            <input type="hidden" name="order_id" id="inputOrderId">
            <input type="hidden" name="total_amount" id="inputTotalRaw">

            <div class="bg-gray-50 p-4 rounded-xl mb-6 text-center border border-gray-200">
                <p class="text-sm text-gray-500 uppercase tracking-wide font-bold">Total Tagihan</p>
                <h1 class="text-4xl font-extrabold text-gray-800 mt-1" id="displayTotal">Rp 0</h1>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" id="paymentMethod" onchange="checkPaymentType()" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 outline-none text-gray-700 font-medium">
                        <option value="tunai">💵 Tunai (Cash)</option>
                        <option value="qris">📱 QRIS</option>
                        <option value="debit">💳 Debit / Kartu Kredit</option>
                    </select>
                </div>

                <div id="cashInputContainer">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Uang Diterima (Rp)</label>
                    <input type="number" name="amount_received" id="amountReceived" 
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 outline-none text-lg font-bold text-gray-800" 
                           placeholder="0" required oninput="calculateChange()">
                </div>

                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100" id="changeContainer">
                    <span class="text-sm font-bold text-blue-600">Kembalian:</span>
                    <span class="text-xl font-bold text-blue-700" id="displayChange">Rp 0</span>
                    <input type="hidden" name="change_amount" id="inputChange">
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg transition flex justify-center items-center gap-2">
                    <i class="fas fa-check-circle"></i> Bayar & Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTotal = 0;

    function openPaymentModal(order) {
        document.getElementById('paymentModal').classList.remove('hidden');
        
        document.getElementById('modalOrderId').innerText = '#' + order.order_id;
        document.getElementById('inputOrderId').value = order.order_id;
        document.getElementById('inputTotalRaw').value = order.total_amount;
        
        document.getElementById('displayTotal').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.total_amount);
        
        currentTotal = parseFloat(order.total_amount);
        
        // Reset Form
        document.getElementById('paymentMethod').value = 'tunai';
        checkPaymentType(); // Panggil fungsi cek metode saat buka modal
    }

    function closeModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    // FUNGSI BARU: Cek Tipe Pembayaran
    function checkPaymentType() {
        const method = document.getElementById('paymentMethod').value;
        const inputField = document.getElementById('amountReceived');
        const changeContainer = document.getElementById('changeContainer');

        if (method === 'tunai') {
            // Jika Tunai: Input kosong, Bisa diedit, Kembalian Muncul
            inputField.value = '';
            inputField.readOnly = false;
            inputField.classList.remove('bg-gray-100', 'text-gray-500');
            inputField.focus();
            changeContainer.style.display = 'flex';
            document.getElementById('displayChange').innerText = 'Rp 0';
        } else {
            // Jika QRIS/Debit: Otomatis Lunas, Readonly, Kembalian Sembunyi
            inputField.value = currentTotal;
            inputField.readOnly = true;
            inputField.classList.add('bg-gray-100', 'text-gray-500');
            changeContainer.style.display = 'none';
            
            // Set kembalian 0 di background
            document.getElementById('inputChange').value = 0;
        }
    }

    function calculateChange() {
        // Hanya hitung kembalian jika metode tunai (atau input tidak readonly)
        if (document.getElementById('paymentMethod').value === 'tunai') {
            let received = parseFloat(document.getElementById('amountReceived').value) || 0;
            let change = received - currentTotal;
            if (change < 0) change = 0;

            document.getElementById('displayChange').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(change);
            document.getElementById('inputChange').value = change;
        }
    }

    function validatePayment() {
        let received = parseFloat(document.getElementById('amountReceived').value) || 0;
        if (received < currentTotal) {
            Swal.fire({
                icon: 'error',
                title: 'Uang Kurang!',
                text: 'Jumlah uang diterima kurang dari total tagihan.',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        return true;
    }
</script>
</body>
</html>