<?php
require_once '../../config/functions.php';

// Cek Login Admin/Staff
if (!isset($_SESSION['user'])) {
    redirect('auth/login.php');
}

// --- LOGIKA UPDATE STATUS ---
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status   = $_POST['status']; // 'cooking' atau 'served'
    
    // Update status di database
    $query = "UPDATE orders SET status = '$status' WHERE order_id = '$order_id'";
    
    if (query($query)) {
        // Redirect agar tidak resubmit form saat refresh
        header("Location: kitchen.php");
        exit;
    }
}

// --- AMBIL DATA PESANAN AKTIF ---
// Kita hanya ambil yang statusnya 'pending' (Baru) atau 'cooking' (Sedang dimasak)
// Urutkan berdasarkan waktu (ASC) agar pesanan yang masuk duluan ada di atas/kiri
$orders = query("SELECT * FROM orders WHERE status IN ('pending', 'cooking') ORDER BY created_at ASC");

require_once '../../layouts/header.php';
?>

<meta http-equiv="refresh" content="30">

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    👨‍🍳 Kitchen Display
                    <span class="text-sm font-normal bg-gray-200 text-gray-600 px-3 py-1 rounded-full">Auto-refresh 30s</span>
                </h1>
                <p class="text-gray-500 mt-1">Pantau antrian pesanan yang perlu dimasak.</p>
            </div>
            
            <div class="flex gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                    <span class="text-sm text-gray-600">Pending (Baru)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-sm text-gray-600">Cooking (Dimasak)</span>
                </div>
            </div>
        </div>

        <?php if(empty($orders)): ?>
            <div class="flex flex-col items-center justify-center h-96 text-gray-400 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                <i class="fas fa-utensils text-6xl mb-4 opacity-20"></i>
                <p class="text-xl font-semibold">Tidak ada pesanan aktif.</p>
                <p class="text-sm">Dapur bisa istirahat sejenak! ☕</p>
            </div>
        <?php else: ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($orders as $order) : ?>
                    <?php 
                        // Tentukan Warna Kartu berdasarkan status
                        $card_class = ($order['status'] == 'pending') 
                            ? 'border-yellow-400 bg-yellow-50' 
                            : 'border-blue-400 bg-blue-50';
                        
                        $badge_class = ($order['status'] == 'pending') 
                            ? 'bg-yellow-100 text-yellow-700' 
                            : 'bg-blue-100 text-blue-700';
                    ?>

                    <div class="bg-white border-t-4 <?php echo $card_class; ?> rounded-xl shadow-sm p-5 flex flex-col h-full relative overflow-hidden group hover:shadow-md transition">
                        
                        <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-3">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase">Meja</span>
                                <h2 class="text-3xl font-bold text-gray-800"><?php echo $order['table_number']; ?></h2>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $badge_class; ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                                <p class="text-xs text-gray-400 mt-1 font-mono">
                                    <?php echo date('H:i', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex-1 mb-6">
                            <ul class="space-y-3">
                                <?php 
                                $oid = $order['order_id'];
                                // Ambil detail item
                                $items = query("SELECT oi.quantity, m.name, oi.notes 
                                                FROM order_items oi 
                                                JOIN menu_items m ON oi.menu_id = m.menu_id 
                                                WHERE oi.order_id = '$oid'");
                                
                                foreach($items as $item): 
                                ?>
                                <li class="flex items-start gap-3">
                                    <span class="bg-gray-800 text-white font-bold text-sm w-6 h-6 flex items-center justify-center rounded shrink-0">
                                        <?php echo $item['quantity']; ?>
                                    </span>
                                    <div class="leading-tight">
                                        <p class="font-semibold text-gray-700 text-sm"><?php echo $item['name']; ?></p>
                                        <?php if(!empty($item['notes'])): ?>
                                            <p class="text-xs text-red-500 italic mt-0.5">"<?php echo $item['notes']; ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                
                                <?php if ($order['status'] == 'pending') : ?>
                                    <button type="submit" name="update_status" value="update" class="hidden"></button> <input type="hidden" name="status" value="cooking">
                                    <button type="submit" name="update_status" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow transition flex items-center justify-center gap-2">
                                        <i class="fas fa-fire"></i> Mulai Masak
                                    </button>
                                
                                <?php elseif ($order['status'] == 'cooking') : ?>
                                    <input type="hidden" name="status" value="served">
                                    <button type="submit" name="update_status" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow transition flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i> Siap Disajikan
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </main>
</div>
</body>
</html>