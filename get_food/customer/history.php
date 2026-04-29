<?php
require_once '../config/functions.php';

$name = $_SESSION['user']['name'];
// Ambil pesanan milik user ini (limit 5 terakhir)
$orders = query("SELECT * FROM orders WHERE customer_name = '$name' ORDER BY created_at DESC LIMIT 5");

require_once '../layouts/header.php';
?>

<div class="bg-gray-50 min-h-screen pb-24">
    <div class="bg-white p-4 shadow-sm mb-4 sticky top-0 z-10 flex justify-between items-center">
        <h1 class="font-bold text-xl">Riwayat Reservasi</h1>
        <div class="flex items-center gap-2">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs text-green-600 font-bold">Live Update</span>
        </div>
    </div>

    <div class="px-4 space-y-4">
        <?php if(empty($orders)): ?>
            <div class="text-center py-20 text-gray-400">
                <i class="fas fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                <p>Belum ada riwayat reservasi.</p>
                <a href="menu.php" class="text-orange-500 font-bold text-sm mt-2 block">Buat Reservasi Baru</a>
            </div>
        <?php endif; ?>

        <?php foreach($orders as $order): ?>
        <div class="bg-white p-0 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 flex justify-between items-center border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400">ORDER #<?php echo $order['order_id']; ?></span>
                
                <?php 
                    $color = 'gray'; $text = 'Menunggu';
                    if($order['status']=='pending') { $color = 'yellow'; $text = 'Menunggu Konfirmasi'; }
                    if($order['status']=='cooking') { $color = 'blue'; $text = 'Dapur Menyiapkan'; }
                    if($order['status']=='served') { $color = 'green'; $text = 'Siap Disajikan'; }
                    if($order['status']=='completed') { $color = 'gray'; $text = 'Selesai / Lunas'; }
                ?>
                <span class="bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-700 text-[10px] font-bold px-2 py-1 rounded">
                    <?php echo strtoupper($text); ?>
                </span>
            </div>

            <div class="p-4">
                <div class="flex gap-2 mb-4">
                    <div class="flex-1 bg-orange-50 p-2 rounded-lg text-center border border-orange-100">
                        <p class="text-[10px] text-gray-500 uppercase">Meja</p>
                        <p class="text-xl font-bold text-orange-600"><?php echo $order['table_number']; ?></p>
                    </div>
                    <div class="flex-1 bg-blue-50 p-2 rounded-lg text-center border border-blue-100">
                        <p class="text-[10px] text-gray-500 uppercase">Jam</p>
                        <p class="text-sm font-bold text-blue-600 leading-tight">
                            <?php echo date('H:i', strtotime($order['reservation_time'])); ?><br>
                            <span class="text-[10px] font-normal text-gray-500"><?php echo date('d M', strtotime($order['reservation_time'])); ?></span>
                        </p>
                    </div>
                    <div class="flex-1 bg-gray-50 p-2 rounded-lg text-center border border-gray-100">
                        <p class="text-[10px] text-gray-500 uppercase">Tamu</p>
                        <p class="text-sm font-bold text-gray-700 mt-1"><?php echo $order['number_of_people']; ?> Org</p>
                    </div>
                </div>

                <div class="text-sm text-gray-600 border-t border-dashed border-gray-200 pt-3">
                    <p class="text-xs text-gray-400 mb-1">Rincian Menu:</p>
                    <ul class="space-y-1">
                        <?php 
                        $oid = $order['order_id'];
                        $items = query("SELECT oi.quantity, m.name, oi.subtotal FROM order_items oi JOIN menu_items m ON oi.menu_id = m.menu_id WHERE order_id='$oid'");
                        foreach($items as $item) {
                            echo "<li class='flex justify-between text-xs'>
                                    <span>{$item['quantity']}x {$item['name']}</span>
                                    <span>".format_rupiah($item['subtotal'])."</span>
                                  </li>";
                        }
                        ?>
                    </ul>
                </div>
                
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-gray-500 block">Total Tagihan</span>
                        <span class="text-lg font-bold text-gray-800"><?php echo format_rupiah($order['total_amount']); ?></span>
                    </div>

                    <?php if($order['status'] == 'completed' || $order['status'] == 'served'): ?>
                        <button onclick="openReceipt(<?php echo $order['order_id']; ?>)" 
                                class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-xs font-bold hover:bg-gray-200 transition flex items-center gap-2 border border-gray-200">
                            <i class="fas fa-receipt"></i> Struk
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="receiptModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-80 backdrop-blur-sm transition-opacity" onclick="closeReceipt()"></div>
    
    <div class="relative bg-white w-full max-w-sm mx-auto mt-20 h-[500px] rounded-t-2xl md:rounded-2xl overflow-hidden flex flex-col shadow-2xl animate-fade-in-up">
        
        <div class="bg-gray-100 px-4 py-3 flex justify-between items-center border-b border-gray-200">
            <h3 class="font-bold text-gray-700">Bukti Transaksi</h3>
            <button onclick="closeReceipt()" class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow text-gray-500 hover:text-red-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex-1 bg-gray-50 overflow-hidden relative">
            <div id="loadingReceipt" class="absolute inset-0 flex items-center justify-center text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
            </div>
            <iframe id="receiptFrame" src="" class="w-full h-full border-none" onload="document.getElementById('loadingReceipt').style.display='none'"></iframe>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
    // Logic Struk
    function openReceipt(orderId) {
        const modal = document.getElementById('receiptModal');
        const iframe = document.getElementById('receiptFrame');
        const loader = document.getElementById('loadingReceipt');
        
        modal.classList.remove('hidden');
        loader.style.display = 'flex';
        iframe.src = 'receipt.php?id=' + orderId;
    }

    function closeReceipt() {
        const modal = document.getElementById('receiptModal');
        const iframe = document.getElementById('receiptFrame');
        modal.classList.add('hidden');
        iframe.src = ''; 
    }

    // --- LOGIC AUTO REFRESH PINTAR ---
    // Refresh halaman setiap 15 detik
    setInterval(function() {
        const modal = document.getElementById('receiptModal');
        
        // Hanya refresh jika Modal Struk SEDANG TERTUTUP (hidden)
        // Agar user tidak terganggu saat membaca struk
        if (modal.classList.contains('hidden')) {
            window.location.reload();
        }
    }, 15000); // 15000 ms = 15 detik
</script>