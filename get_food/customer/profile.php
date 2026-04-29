<?php
require_once '../config/functions.php';

// Cek Login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer') {
    redirect('auth/login.php');
}

$user = $_SESSION['user'];

require_once '../layouts/header.php';
?>

<div class="bg-gray-50 min-h-screen pb-24">
    
    <div class="bg-primary pb-20 pt-10 px-6 rounded-b-[2.5rem] shadow-md text-center">
        <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center border-4 border-orange-200 shadow-lg mb-4">
            <i class="fas fa-user text-4xl text-primary"></i>
        </div>
        <h2 class="text-white text-2xl font-bold"><?php echo $user['name']; ?></h2>
        <p class="text-orange-100 text-sm mt-1">Pelanggan Setia</p>
    </div>

    <div class="px-6 -mt-12">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 w-10 h-10 rounded-full flex items-center justify-center text-blue-500">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lokasi Makan</p>
                        <p class="font-bold text-gray-800">Dine In</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-50 w-10 h-10 rounded-full flex items-center justify-center text-orange-500">
                        <i class="fas fa-chair"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Nomor Meja</p>
                        <p class="font-bold text-gray-800 text-xl">Meja <?php echo $user['table_number']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 mt-6 space-y-3">
        <a href="history.php" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <i class="fas fa-history text-gray-400 w-6"></i>
                <span class="text-sm font-semibold text-gray-700">Riwayat Pesanan</span>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
        </a>

        <a href="../auth/logout.php" onclick="return confirm('Yakin ingin menyudahi sesi makan?');" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-red-50 group transition">
            <div class="flex items-center gap-3">
                <i class="fas fa-sign-out-alt text-red-500 w-6 group-hover:text-red-600"></i>
                <span class="text-sm font-semibold text-red-500 group-hover:text-red-600">Keluar / Selesai Makan</span>
            </div>
        </a>
    </div>

    <div class="text-center mt-8 text-xs text-gray-400">
        Get Food App v1.0
    </div>

</div>

<?php require_once '../layouts/footer.php'; ?>