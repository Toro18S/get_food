<?php
// Cek halaman aktif untuk highlight menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-slate-800 text-white min-h-screen flex flex-col fixed left-0 top-0 bottom-0 z-50">
    <div class="h-16 flex items-center justify-center border-b border-slate-700 bg-orange-600">
        <h2 class="text-xl font-bold tracking-wider">ADMIN PANEL</h2>
    </div>

    <div class="p-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-slate-600 flex items-center justify-center text-lg font-bold">
            <?php echo substr($_SESSION['user']['name'] ?? 'A', 0, 1); ?>
        </div>
        <div>
            <p class="text-sm font-semibold"><?php echo $_SESSION['user']['name'] ?? 'Admin'; ?></p>
            <p class="text-xs text-slate-400 capitalize"><?php echo $_SESSION['user']['job_role'] ?? 'Staff'; ?></p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            <li>
                <a href="<?php echo url('admin/index.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo ($current_page == 'index.php') ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo url('admin/menu/index.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/menu/') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3">🍔</span> Kelola Menu
                </a>
            </li>
            <li>
                <a href="<?php echo url('admin/orders/list.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/orders/') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3">📝</span> Pesanan Masuk
                </a>
            </li>

            <li>
                <a href="<?php echo url('admin/orders/kitchen.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], 'kitchen.php') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3 text-xl">👨‍🍳</span>
                    <span class="font-medium">Kitchen Display</span>
                </a>
            </li>

            <li>
                <a href="<?php echo url('admin/restaurants/index.php'); ?>" 
                    class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/restaurants/') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3">🏢</span> Cabang Restoran
                </a>
            </li>
            <li>
                <a href="<?php echo url('admin/users/index.php'); ?>" class="flex items-center px-6 py-3 hover:bg-slate-700 transition">
                    <span class="mr-3">👥</span> Data Pegawai
                </a>
            </li>

            <li>
                <a href="<?php echo url('admin/transactions/index.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], 'transactions') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3 text-xl">💰</span>
                    <span class="font-medium">Kasir / Transaksi</span>
                </a>
            </li>

            <li>
                <a href="<?php echo url('admin/tables/index.php'); ?>" 
                   class="flex items-center px-6 py-3 hover:bg-slate-700 transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/tables/') !== false) ? 'bg-slate-700 border-r-4 border-orange-500' : ''; ?>">
                    <span class="mr-3 text-xl">🪑</span>
                    <span class="font-medium">Manajemen Meja</span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="p-4 border-t border-slate-700">
        <a href="<?php echo url('auth/logout.php'); ?>" class="block w-full text-center py-2 px-4 bg-red-600 hover:bg-red-700 rounded text-sm font-bold transition">
            Keluar Sistem
        </a>
    </div>
</aside>