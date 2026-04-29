<div class="mb-20 md:mb-0"></div> 

<?php 
// Logika Cek Halaman
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$is_auth  = strpos($_SERVER['REQUEST_URI'], '/auth/') !== false;

if (!$is_admin && !$is_auth) : 
    $current = basename($_SERVER['PHP_SELF']);
?>

    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around items-center py-3 z-50 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        
        <a href="<?php echo url('customer/home.php'); ?>" class="flex flex-col items-center w-full group <?php echo ($current == 'home.php') ? 'text-primary' : 'text-gray-400'; ?>">
            <i class="fas fa-home text-xl mb-1 group-active:scale-90 transition"></i>
            <span class="text-[10px] font-medium">Home</span>
        </a>

        <a href="<?php echo url('customer/menu.php'); ?>" class="flex flex-col items-center w-full group <?php echo ($current == 'menu.php') ? 'text-primary' : 'text-gray-400'; ?>">
            <i class="fas fa-utensils text-xl mb-1 group-active:scale-90 transition"></i>
            <span class="text-[10px] font-medium">Menu</span>
        </a>

        <a href="<?php echo url('customer/history.php'); ?>" class="flex flex-col items-center w-full group <?php echo ($current == 'history.php' || $current == 'status.php') ? 'text-primary' : 'text-gray-400'; ?>">
            <i class="fas fa-clipboard-list text-xl mb-1 group-active:scale-90 transition"></i>
            <span class="text-[10px] font-medium">Order</span>
        </a>

        <a href="<?php echo url('customer/profile.php'); ?>" class="flex flex-col items-center w-full group <?php echo ($current == 'profile.php') ? 'text-primary' : 'text-gray-400'; ?>">
            <i class="fas fa-user text-xl mb-1 group-active:scale-90 transition"></i>
            <span class="text-[10px] font-medium">Me</span>
        </a>

    </nav>

<?php endif; ?>

</body>
</html>