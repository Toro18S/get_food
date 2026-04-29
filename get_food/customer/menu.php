<?php
require_once '../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer') {
    redirect('auth/login.php');
}

$current_resto_id = $_SESSION['restaurant_id'] ?? 1; 

$sql_filter = "SELECT DISTINCT m.* FROM menu_items m 
               JOIN restaurant_menus rm ON m.menu_id = rm.menu_id 
               WHERE rm.restaurant_id = '$current_resto_id' 
               AND rm.is_available = 1";

$foods  = query("$sql_filter AND m.category = 'makanan' ORDER BY m.name ASC");
$drinks = query("$sql_filter AND m.category = 'minuman' ORDER BY m.name ASC");
$snacks = query("$sql_filter AND m.category = 'snack' ORDER BY m.name ASC");

require_once '../layouts/header.php';
?>

<?php require_once '../layouts/layout_menu.php'; ?>

<div class="bg-gray-50 min-h-screen">
    
    <div class="w-full flex h-[calc(100vh-140px)] overflow-hidden">
        
        <aside class="w-1/4 max-w-[280px] bg-white h-full overflow-y-auto no-scrollbar border-r border-gray-200 hidden md:block pb-20">
            <div class="p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Kategori Menu</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#cat-makanan" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-600 rounded-xl hover:bg-orange-50 hover:text-primary transition group">
                            <span class="bg-gray-100 p-2 rounded-lg group-hover:bg-white transition">🍜</span>
                            Makanan Berat
                        </a>
                    </li>
                    <li>
                        <a href="#cat-minuman" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-600 rounded-xl hover:bg-orange-50 hover:text-primary transition group">
                            <span class="bg-gray-100 p-2 rounded-lg group-hover:bg-white transition">🥤</span>
                            Minuman Segar
                        </a>
                    </li>
                    <li>
                        <a href="#cat-snack" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-600 rounded-xl hover:bg-orange-50 hover:text-primary transition group">
                            <span class="bg-gray-100 p-2 rounded-lg group-hover:bg-white transition">🍟</span>
                            Cemilan / Snack
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <aside class="w-20 bg-white h-full overflow-y-auto border-r border-gray-100 md:hidden pb-20">
            <ul class="space-y-1 py-2">
                <li><a href="#cat-makanan" class="block py-4 text-center text-2xl hover:bg-gray-50">🍜</a></li>
                <li><a href="#cat-minuman" class="block py-4 text-center text-2xl hover:bg-gray-50">🥤</a></li>
                <li><a href="#cat-snack" class="block py-4 text-center text-2xl hover:bg-gray-50">🍟</a></li>
            </ul>
        </aside>

        <main class="flex-1 h-full overflow-y-auto pb-32 scroll-smooth px-6 md:px-10 py-8" id="main-content">
            
            <?php show_flash_message(); ?>

            <div id="cat-makanan" class="mb-10">
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-primary rounded-full"></span> Makanan Berat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if(empty($foods)): ?><p class="text-sm text-gray-400 col-span-full italic">Menu tidak tersedia di cabang ini.</p><?php endif; ?>
                    <?php foreach($foods as $item): ?>
                        <?php include 'component_menu_item.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="cat-minuman" class="mb-10">
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span> Minuman Segar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if(empty($drinks)): ?><p class="text-sm text-gray-400 col-span-full italic">Menu tidak tersedia.</p><?php endif; ?>
                    <?php foreach($drinks as $item): ?>
                        <?php include 'component_menu_item.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="cat-snack" class="mb-24">
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Cemilan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if(empty($snacks)): ?><p class="text-sm text-gray-400 col-span-full italic">Menu tidak tersedia.</p><?php endif; ?>
                    <?php foreach($snacks as $item): ?>
                        <?php include 'component_menu_item.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): 
    $total_qty = 0; $total_price = 0;
    foreach($_SESSION['cart'] as $c) { $total_qty += $c['qty']; $total_price += $c['subtotal']; }
?>
<div class="fixed bottom-20 md:bottom-10 left-0 w-full flex justify-center z-50 pointer-events-none">
    <div class="pointer-events-auto bg-gray-900 text-white rounded-full shadow-2xl p-2 pr-6 flex items-center gap-4 transition hover:scale-105 cursor-pointer border border-gray-700" onclick="window.location.href='cart.php'">
        <div class="bg-primary text-white font-bold w-10 h-10 rounded-full flex items-center justify-center">
            <?php echo $total_qty; ?>
        </div>
        <div class="flex flex-col">
            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Total</span>
            <span class="font-bold text-sm"><?php echo format_rupiah($total_price); ?></span>
        </div>
        <div class="h-8 w-[1px] bg-gray-700 mx-2"></div>
        <span class="font-bold text-sm flex items-center gap-2">
            Checkout <i class="fas fa-arrow-right"></i>
        </span>
    </div>
</div>
<?php endif; ?>

<?php require_once '../layouts/footer.php'; ?>